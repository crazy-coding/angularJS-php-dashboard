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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'payment')) {
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
function validate_request_data($request, $language) 
{

  // Validate Invoice ID
  if (!validateString($request->post['invoice-id'])) {
     throw new Exception($language->get('error_invoice_id'));
  }

  // Validate Customer ID
  if (!validateString($request->post['customer-id'])) {
    throw new Exception($language->get('error_customer_id'));
  }

  // Validate Payment Method ID
  if (!validateInteger($request->post['pmethod-id'])) {
    throw new Exception($language->get('error_payment_method'));
  }
}

if ($request->server['REQUEST_METHOD'] == 'POST' && $request->get['action_type'] == 'PAYMENT')
{
  try {

    // Validate post data
    validate_request_data($request, $language);

    $invoice_model = $registry->get('loader')->model('invoice');
    $created_at = date('Y-m-d H:i:s');
    $invoice_id = $request->post['invoice-id'];
    $customer_id = $request->post['customer-id'];
    $pmethod_id = $request->post['pmethod-id'];
    $invoice_price = $invoice_model->getSellingPrice($invoice_id, $store_id);
    $discount_amount = $request->post['discount-amount'] ? $request->post['discount-amount'] : 0;
    $payable_amount = $invoice_price['payable_amount'] - $invoice_price['paid_amount'];
    $paid_amount = $request->post['paid-amount'] ? $request->post['paid-amount'] : 0;
    if ($discount_amount > $payable_amount) {
      throw new Exception($language->get('error_discount_amount_exceed'));
    } else {
      $paid_amount = $paid_amount+$discount_amount;
    }
    $total_paid = $paid_amount;
    $note = $request->post['note'];

    $details_raw = isset($request->post['payment_details']) ? $request->post['payment_details'] : array();
    $details = serialize($details_raw);
    $is_card_payments = false;
    $card_no = '';
    if (isset($details_raw['card_no'])) {
      $card_no = $details_raw['card_no'];
      $statement = $db->prepare("SELECT * FROM `gift_cards` WHERE `customer_id` = ? AND `card_no` = ? AND `balance` >= ? AND `expiry` > NOW()");
      $statement->execute(array($customer_id, $card_no, $payable_amount));
      $row = $statement->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $is_card_payments = true;
        $paid_amount = $row['balance'];
      } else {
        throw new Exception($language->get('error_not_found_or_insufficient_balance'));
      }
    }

    $due = ($invoice_price['due'] - $paid_amount) > 0 ? ($invoice_price['due'] - $paid_amount) : 0;
    $balance = 0;
    if ($paid_amount > $payable_amount) {
      $due = 0;
      $balance = $paid_amount - $payable_amount;
      $paid_amount = $payable_amount;
    }

    if ($paid_amount <= 0 && $discount_amount <= 0) {
      throw new Exception($language->get('error_paid_amount'));
    }

    if ($paid_amount > 0 && $discount_amount <= 0) {
      $statement = $db->prepare("INSERT INTO `payments` (type, store_id, invoice_id, pmethod_id, amount, details, note, total_paid, pos_balance, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $statement->execute(array('due_paid', $store_id, $invoice_id, $pmethod_id, $paid_amount, $details, $note, $total_paid, $balance, $user_id, $created_at));
    }
    if ($discount_amount > 0) {
      $statement = $db->prepare("INSERT INTO `payments` (type, store_id, invoice_id, amount, details, note, total_paid, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $statement->execute(array('discount', $store_id, $invoice_id, $discount_amount, $details, 'discount_whilte_due_paid', $discount_amount, $user_id, $created_at));
    }

    // Checkout status
    $statement = $db->prepare("UPDATE `selling_info` SET `checkout_status` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
    $statement->execute(array(1, $invoice_id, $store_id));

    // Add Paid Amount
    $statement = $db->prepare("UPDATE `selling_price` SET `paid_amount` = `paid_amount`+$paid_amount, `due_paid` = `due_paid`+$paid_amount, `due` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
    $statement->execute(array($due, $invoice_id, $store_id));

    // Fetch invoice price
    $invoice_price = $invoice_model->getSellingPrice($invoice_id, $store_id);
    if ($invoice_price['payable_amount'] <= $invoice_price['paid_amount']) {
      $statement = $db->prepare("UPDATE `selling_info` SET `payment_status` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
      $statement->execute(array('paid', $invoice_id, $store_id));
    }

    // Decrease card balance
    if ($paid_amount > 0 && $is_card_payments && $card_no) {
        $statement = $db->prepare("UPDATE `gift_cards` SET `balance` = `balance` - $paid_amount  WHERE `card_no` = ?");
        $statement->execute(array($card_no));
    }

    if ($paid_amount > 0) {
      // Add customer transaction
      $reference_no = generate_customer_transacton_ref_no('due_paid');
      $statement = $db->prepare("INSERT INTO `customer_transactions` (customer_id, reference_no, type, pmethod_id, description, amount, store_id, ref_invoice_id, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $statement->execute(array($customer_id, $reference_no, 'due_paid', $pmethod_id, 'Due paid by customer', $paid_amount, $store_id, $invoice_id, $user_id, $created_at));

      // Add Balance to customer
      $statement = $db->prepare("UPDATE `customer_to_store` SET `balance` = `balance` + $paid_amount WHERE `customer_id` = ? AND `store_id` = ?");
      $statement->execute(array($customer_id, $store_id));
    }

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_sell_due_paid_success')));
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
  $where_query = "`selling_info`.`store_id` = ? AND `selling_info`.`invoice_id` = '{$invoice_id}'";
  $statement = $db->prepare("SELECT * FROM `selling_info` 
        LEFT JOIN `selling_price` ON (`selling_price`.`invoice_id` = `selling_info`.`invoice_id`)
        WHERE $where_query");
  $statement->execute(array(store_id()));
  $order = $statement->fetch(PDO::FETCH_ASSOC);
  
  $invoice_model = $registry->get('loader')->model('invoice');
  $payment_model = $registry->get('loader')->model('payment');
  $items = $invoice_model->getInvoiceItems($order['invoice_id'], store_id());
  $payments = $payment_model->getPayments($order['invoice_id'], store_id());
  $order['customer_name'] = get_the_customer($order['customer_id'], 'customer_name');
  $order['items']     = $items;
  $order['table']     = '';
  $order['payments']  = $payments;

  header('Content-Type: application/json');
  echo json_encode(array('msg' => $language->get('text_success'), 'order' => $order));
  exit();
}