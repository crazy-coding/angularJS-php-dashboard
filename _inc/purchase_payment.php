<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// If user is not logged in then return error
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// If user have not reading permission return error
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'purchase_payment')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('payment');

$store_id = store_id();
$user_id = user_id();

// Validate post data
function validate_request_data($request, $language) {

  // Payment method validation
  if (!validateInteger($request->post['pmethod-id'])) {
      throw new Exception($language->get('error_payment_method'));
  }

  // Paid amount validation
  if ($request->post['paid-amount'] <= 0) {
      throw new Exception($language->get('error_paid_amount'));
  }
}

if ($request->server['REQUEST_METHOD'] == 'POST' && $request->get['action_type'] == 'PAYMENT')
{
  try {

    // Validate post data
    validate_request_data($request, $language);

    $invoice_model = $registry->get('loader')->model('purchase');
    $created_at = date('Y-m-d H:i:s');
    $invoice_id = $request->post['invoice-id'];
    $note = $request->post['note'];
    $sup_id = $request->post['sup-id'];
    $pmethod_id = $request->post['pmethod-id'];
    $paid_amount = $request->post['paid-amount'];
    $total_paid = $paid_amount;
    $invoice_price = $invoice_model->getSellingPrice($invoice_id, $store_id);
    $payable_amount = $invoice_price['payable_amount'] - $invoice_price['paid_amount'];
    $due = ($invoice_price['due'] - $paid_amount) > 0 ? ($invoice_price['due'] - $paid_amount) : 0;
    $balance = 0;
    if ($paid_amount > $payable_amount) {
      $due = 0;
      $balance = $paid_amount - $payable_amount;
      $paid_amount = $payable_amount;
    }


    $details = isset($request->post['payment_details']) ? $request->post['payment_details'] : array();
    $details = serialize($details);

    $statement = $db->prepare("INSERT INTO `buying_payments` (type, store_id, invoice_id, pmethod_id, amount, details, note, total_paid, balance, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $statement->execute(array('due_paid', $store_id, $invoice_id, $pmethod_id, $paid_amount, $details, $note, $total_paid, $balance, $user_id, $created_at));

    // Checkout status
    $statement = $db->prepare("UPDATE `buying_info` SET `checkout_status` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
    $statement->execute(array(1, $invoice_id, $store_id));

    // Add Paid Amount
    $statement = $db->prepare("UPDATE `buying_price` SET `paid_amount` = `paid_amount`+$paid_amount, `due_paid` = `due_paid`+$paid_amount, `due` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
    $statement->execute(array($due, $invoice_id, $store_id));

    // Fetch invoice price
    $invoice_price = $invoice_model->getSellingPrice($invoice_id, $store_id);
    if ($invoice_price['payable_amount'] <= $invoice_price['paid_amount']) {
      $statement = $db->prepare("UPDATE `buying_info` SET `payment_status` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
      $statement->execute(array('paid', $invoice_id, $store_id));
    }

    if ($paid_amount > 0) {
      $reference_no = generate_supplier_transacton_ref_no('due_paid');
      $statement = $db->prepare("INSERT INTO `supplier_transactions` (sup_id, reference_no, type, pmethod_id, description, amount, store_id, ref_invoice_id, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $statement->execute(array($sup_id, $reference_no, 'due_paid', $pmethod_id, 'Due paid to supplier', $paid_amount, $store_id, $invoice_id, $user_id, $created_at));

      $statement = $db->prepare("UPDATE `supplier_to_store` SET `balance` = `balance` - $paid_amount WHERE `sup_id` = ? AND `store_id` = ?");
      $statement->execute(array($sup_id, $store_id));
    }

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_buy_due_paid_success')));
    exit();

  } catch (Exception $e) {
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// payment method fields
if (isset($request->get['pmethod_id']) && $request->get['action_type'] == 'FIELD') 
{
  $pmethod_model = $registry->get('loader')->model('pmethod');
  $pmethod_id = $request->get['pmethod_id'];
  $pmethod = $pmethod_model->getPMethod($pmethod_id);
  if ($pmethod && file_exists(ROOT.'/_inc/template/partials/pmethodfield/'.strtolower(str_replace(' ', '_',$pmethod['name'])).'_field.php')) {
    include ROOT.'/_inc/template/partials/pmethodfield/'.strtolower(str_replace(' ', '_',$pmethod['name'])).'_field.php';
  }
  exit();
}

if (isset($request->get['action_type']) && $request->get['action_type'] == 'ORDERDETAILS')
{
  $invoice_id = $request->get['invoice_id'];
  if (!$invoice_id) {
    throw new Exception($language->get('error_invoice_id'));
  }
  $order = array();
  $items = array();
  $where_query = "`buying_info`.`store_id` = ? AND `buying_info`.`invoice_id` = '{$invoice_id}'";
  $statement = $db->prepare("SELECT * FROM `buying_info` 
        LEFT JOIN `buying_price` ON (`buying_price`.`invoice_id` = `buying_info`.`invoice_id`)
        WHERE $where_query");
  $statement->execute(array(store_id()));
  $order = $statement->fetch(PDO::FETCH_ASSOC);
  
  $purchase_model = $registry->get('loader')->model('purchase');
  $payment_model = $registry->get('loader')->model('payment');
  $items = $purchase_model->getInvoiceItems($order['invoice_id'], store_id());
  $payments = $payment_model->getPurchasePayments($order['invoice_id'], store_id());

  $order['items']     = $items;
  $order['table']     = '';
  $order['payments']  = $payments;

  header('Content-Type: application/json');
  echo json_encode(array('msg' => $language->get('text_success'), 'order' => $order));
  exit();
}