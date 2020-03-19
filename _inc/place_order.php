<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// If user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// If user have not reading permission return an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_invoice')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language Files
$language->load('invoice');
$language->load('pos');

$store_id     = store_id();

// LOAD INVOICE MODEL
$invoice_model = $registry->get('loader')->model('invoice');

// Validate customer post data
function validate_customer_request_data($request, $language) 
{
  // Validate pmethod id
  if ($request->post['paid-amount'] > 0) {
    if (!validateInteger($request->post['pmethod-id'])) {
      throw new Exception($language->get('error_pmethod'));
    }
  }

  // Validate customer id
  if (!validateInteger($request->post['customer-id'])) {
    throw new Exception($language->get('error_invoice_customer'));
  }
}

// Validate invoice items
function validate_invoice_items($invoice_items, $language)
{
  foreach ($invoice_items as $product) 
  {
    // Validate product id
    if (!validateInteger($product['item_id'])) {
      throw new Exception($language->get('error_invalid_product_id'));
    }

    // Fetch product item
    $the_product = get_the_product($product['item_id'], null, store_id());

    // Check, product item exist or not
    if (!$the_product) {
      throw new Exception($language->get('error_product_not_found'));
    }

    // Check, product item stock availabel or not
    if ($the_product['quantity_in_stock'] <= 0) {
      throw new Exception($language->get('error_out_of_stock'));
    }

    // Chcck, requested quantity is greater than of existing quantity or not
    if ($the_product['quantity_in_stock'] < $product['item_quantity']) {
      throw new Exception($language->get('error_quantity_exceed'));
    }

    global $session;
    if (isset($session->data['stock_check']) && isset($session->data['quantity_check'])) {
      throw new Exception($language->get('error_invalid_purchase_code'));
    }

    // Validate product name
    if (!validateString($product['item_name'])) {
      throw new Exception($language->get('error_invoice_product_name'));
    }

    // Validate product price
    if (!validateFloat($product['item_price'])) {
      throw new Exception($language->get('error_invoice_product_price'));
    }

    // Validate product quantity
    if (!validateInteger($product['item_quantity'])) {
      throw new Exception($language->get('error_invoice_product_quantity'));
    }

    // Validate product total price
    if (!validateFloat($product['item_total'])) {
      throw new Exception($language->get('error_invoice_product_total'));
    }
  }
}

if ($request->server['REQUEST_METHOD'] == 'POST')
{
  try {

    $invoice_id   = isset($request->post['invoice-id']) ? $request->post['invoice-id'] : null;

    validate_customer_request_data($request, $language);

    // Validate sub-total
    if (!validateFloat($request->post['sub-total'])) {
      throw new Exception($language->get('error_invoice_sub_total'));
    }

    // Validate discount amount
    if (!is_numeric($request->post['discount-amount'])) {
      throw new Exception($language->get('error_invoice_discount_amount'));
    }

    // Validate tax amount
    if (!is_numeric($request->post['tax-amount'])) {
      throw new Exception($language->get('error_invoice_tax_amount'));
    }

    // Validate payable amount
    if (!validateFloat($request->post['payable-amount'])) {
      throw new Exception($language->get('error_invoice_payable_amount'));
    }

    // Validate invoice items
    if (!isset($request->post['product-item']) 
      && (isset($request->post['product-item']) || !is_array($request->post['product-item']))) {

      throw new Exception($language->get('error_product_item'));
    }

    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_due')) {
      throw new Exception($language->get('error_create_due_permission'));
    }

    $product_items  = $request->post['product-item'];
    $statement = $db->prepare("SELECT * FROM `selling_info` WHERE `store_id` = ? AND `invoice_id` = ?");
    $statement->execute(array($store_id, $invoice_id));
    $invoice_info = $statement->fetch(PDO::FETCH_ASSOC);

    //====================
    // Create New Invoice
    //====================

    if (!$invoice_info) {

      // Check invoice create permission
      if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_invoice')) {
        throw new Exception(sprintf($language->get('error_create_permission')));
      }

      validate_invoice_items($product_items, $language);

      // // loop through produdt items for validation checking
      // foreach ($product_items as $product) 
      // {
      //   $product_id     = $product['item_id'];
      //   $product_quantity  = $product['item_quantity'];
      //   for ($i=0; $i < $product_quantity; $i++) { 
      //     $statement = $db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `item_id` = ? AND `status` = ? AND `item_quantity` > `total_sell`");
      //     $statement->execute(array($store_id, $product_id, 'active'));
      //     $buying_item = $statement->fetch(PDO::FETCH_ASSOC);
      //     if (!$buying_item) {
      //       $statement = $db->prepare("UPDATE `buying_item` SET `status` = ? WHERE `store_id` = ? AND `item_id` = ? AND `status` = ?");
      //       $statement->execute(array('active', $store_id, $product_id, 'stock'));
      //     }
      //     $statement = $db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `item_id` = ? AND `status` = ? AND `item_quantity` > `total_sell`");
      //     $statement->execute(array($store_id, $product_id, 'active'));
      //     $buying_item = $statement->fetch(PDO::FETCH_ASSOC);
      //     if (!$buying_item) {
      //       throw new Exception($language->get('error_item_not_found'));
      //     }
      //   }
      // }

      // Create Invoice
      $invoice_id = $invoice_model->createInvoice($request, $store_id);

      // Get Invoice
      $invoice_info = $invoice_model->getInvoiceInfo($invoice_id);
      $invoice_items = $invoice_model->getInvoiceItems($invoice_id);
    }

  	header('Content-Type: application/json');
  	echo json_encode(array('msg' => $language->get('text_invoice_create_success'), 'invoice_id' => $invoice_id, 'invoice_info' => $invoice_info, 'invoice_items' => $invoice_items));
	  exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}