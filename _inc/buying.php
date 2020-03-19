<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then return error
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

//  Load Language File
$language->load('buy');

$store_id = store_id();
$user_id = $user->getId();

// Validate post data
function validate_request_data($request, $language) 
{    
    // Validate invoice id
    if (!validateString($request->post['invoice_id'])) {
      throw new Exception($language->get('error_invoice_id'));
    }

    // Validate supplier id
    if (!validateInteger($request->post['sup_id'])) {
      throw new Exception($language->get('error_sup_id'));
    }

    // Validate date
    if (!isItValidDate($request->post['date'])) {
      throw new Exception($language->get('error_date'));
    }

    // Validate time
    if (!isItValidTime12($request->post['time'])) {
      throw new Exception($language->get('error_time'));
    }

    // Validate product
    if (!isset($request->post['product']) || empty($request->post['product'])) {
        throw new Exception($language->get('error_product_item'));
    }

    // Validate tax
    if (!is_numeric($request->post['total_tax'])) {
      throw new Exception($language->get('error_tax'));
    }

    // Validate payadble amount
    if (!is_numeric($request->post['total'])) {
      throw new Exception($language->get('error_payable_amount'));
    }

    // Validate paid amount
    if (!is_numeric($request->post['paid_amount'])) {
      throw new Exception($language->get('error_paid_amount'));
    }
}

// Create invoice
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_purchase_invoice')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    $Hooks->do_action('Before_Create_Buying_Invoice', $request);

    $sup_id = $request->post['sup_id'];
    $supplier_info = get_the_supplier($sup_id);
    $invoice_id = $request->post['invoice_id'];

    foreach ($request->post['product'] as $id => $product) 
    {  
        $statement = $db->prepare("SELECT * FROM `products`
            LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`) 
            WHERE `p2s`.`store_id` = ? AND `p_id` = ?");
        $statement->execute(array($store_id, $id));
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
          throw new Exception($language->get('error_product_not_found'));
        }
        
        // Validate quantity    
        if(!validateInteger($product['quantity']) || $product['quantity'] <= 0) {
          throw new Exception($language->get('error_quantity'));
        }

        // Validate buying cost
        if(!validateFloat($product['cost']) || $product['cost'] <= 0) {
          throw new Exception($language->get('error_buying_price'));
        }

        // Validate selling price
        if(!validateFloat($product['sell']) || $product['sell'] <= 0) {
          throw new Exception($language->get('error_selling_price'));
        }

        if($product['cost'] >= $product['sell']) {
          throw new Exception($language->get('error_low_selling_price'));
        }
    }

    // Validate and update attachment
    if(isset($_FILES["attachment"]["type"]) && $_FILES["attachment"]["type"])
    {
        if (!$_FILES["attachment"]["type"] == "image/jpg" || !$_FILES["attachment"]["type"] == "application/pdf" || $_FILES["attachment"]["size"] > 1048576) {  // 1MB  
            throw new Exception($language->get('error_size_or_type'));
        }

        if ($_FILES["attachment"]["error"] > 0) {
            throw new Exception("Return Code: " . $_FILES["attachment"]["error"]);
        }
    }

    $total_item = count($request->post['product']);
    $buy_date = $request->post['date'];
    $buy_time = date("H:i:s", strtotime($request->post['time']));
    $created_at = date('Y-m-d H:i:s', strtotime($buy_date . ' ' . $buy_time));
    $buying_note = $request->post['buying_note'];
    $tax_amount = $request->post['total_tax'];
    $payable_amount = $request->post['total'];
    $paid_amount = $request->post['paid_amount'];
    $total_paid = $paid_amount;
    $balance = 0;
    if ($paid_amount > $payable_amount) {
        $balance = $paid_amount - $payable_amount;
        $paid_amount = $paid_amount - $balance;
    }
    $due  = ($payable_amount - $paid_amount) > 0 ? ($payable_amount - $paid_amount) : 0;

    // Check for dublicate, if present then update otherwise insert
    $statement = $db->prepare("SELECT * FROM `buying_info` WHERE `invoice_id` = ?");
    $statement->execute(array($invoice_id));
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        throw new Exception($language->get('error_invoice_exist'));
    }

    // insert data
    $statement = $db->prepare("INSERT INTO `buying_info` (invoice_id, store_id, total_item, buy_date, buy_time, created_at, sup_id, created_by, invoice_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $statement->execute(array($invoice_id, $store_id, $total_item, $buy_date, $buy_time, $created_at, $sup_id, $user_id, $buying_note));
    
    $gst = 0;
    $cgst = 0;
    $sgst = 0;
    $igst = 0;

    $tgst = 0;
    $tcgst = 0;
    $tsgst = 0;
    $tigst = 0;
    $taxrate = 0;
    foreach ($request->post['product'] as $id => $product) {  
        $status = 'active';

        // fetch product recent stock amount
        $the_product = get_the_product($id, null, $store_id);
        if (isset($the_product['quantity_in_stock']) && $the_product['quantity_in_stock'] > 0) {
            $status = 'stock';
        }
        if ($the_product['taxrate']) {
            $taxrate = $the_product['taxrate']['taxrate'];
        }
        $item_name = $product['name'];
        $category_id = $product['category_id'];
        $item_buying_price = $product['cost'];
        $item_selling_price = $product['sell'];
        $item_quantity = $product['quantity'];
        $item_tax = $product['item_tax_amount'];
        $tax_method = $product['item_tax_method'];
        if ($tax_method == 'exclusive') {
            $item_total = ((int)$item_quantity * (float)$item_buying_price) + $item_tax;
        } else {
            $item_total = ((int)$item_quantity * (float)$item_buying_price);
        }

        if ($supplier_info['sup_state'] == get_preference('business_state')) {
          $cgst = $item_tax / 2;
          $sgst = $item_tax / 2;
        } else {
          $igst = $item_tax;
        }
        
        // insert data
        $statement = $db->prepare("INSERT INTO `buying_item` (invoice_id, store_id, item_id, category_id, item_name, item_tax, tax_method, tax, item_buying_price, item_selling_price, item_quantity, item_total, status, gst, cgst, sgst, igst) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->execute(array($invoice_id, $store_id, $id, $category_id, $item_name, $item_tax, $tax_method, $taxrate, $item_buying_price, $item_selling_price, $item_quantity, $item_total, $status, $taxrate, $cgst, $sgst, $igst));
        
        // update data
        $statement = $db->prepare("UPDATE `product_to_store` SET `buy_price` = ?, `sell_price` = ?, `quantity_in_stock` = `quantity_in_stock` + $item_quantity WHERE `product_id` = ? AND `store_id` = ?");
        $statement->execute(array($item_buying_price, $item_selling_price, $id, $store_id));
    }

    if ($supplier_info['sup_state'] == get_preference('business_state')) {
      $tcgst = $tax_amount / 2;
      $tsgst = $tax_amount / 2;
    } else {
      $tigst = $tax_amount;
    }

    // insert data
    $statement = $db->prepare("INSERT INTO `buying_price` (invoice_id, store_id, item_tax, cgst, sgst, igst, payable_amount, paid_amount, due) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $statement->execute(array($invoice_id, $store_id, $tax_amount, $tcgst, $tsgst, $tigst, $payable_amount, $paid_amount, $due));

    // upload attachment
    if(isset($_FILES["attachment"]["type"]) && $_FILES["attachment"]["type"])
    {
        $temporary = explode(".", $_FILES["attachment"]["name"]);
        $file_extension = end($temporary);
        $temp = explode(".", $_FILES["attachment"]["name"]);
        $newfilename = $invoice_id . '.' . end($temp);
        $sourcePath = $_FILES["attachment"]["tmp_name"]; // Storing source path of the file in a variable
        $targetFile = DIR_STORAGE . 'buying-invoices/' . $newfilename; // Target path where file is to be stored
        if (file_exists($targetFile) && is_file($targetFile)) {
            if (!isset($request->post['force_upload'])) {
                throw new Exception($language->get('error_image_exist'));
            } 
            unlink($targetFile);  
        } 
        // update invoice url
        $statement = $db->prepare("UPDATE  `buying_info` SET `attachment` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
        $statement->execute(array($newfilename, $invoice_id, $store_id));

        move_uploaded_file($sourcePath, $targetFile);
    }

    if ($paid_amount > 0) {
        $statement = $db->prepare("INSERT INTO `buying_payments` (store_id, invoice_id, amount, note, total_paid, balance, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $statement->execute(array($store_id, $invoice_id, $paid_amount, $buying_note, $total_paid, $balance, $user_id, $created_at));

        // update checkout status
        $statement = $db->prepare("UPDATE `buying_info` SET `checkout_status` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
        $statement->execute(array(1, $invoice_id, $store_id));
    }

    $reference_no = generate_supplier_transacton_ref_no('purchase');
    $statement = $db->prepare("INSERT INTO `supplier_transactions` (sup_id, reference_no, type, description, amount, store_id, ref_invoice_id, created_by, created_at) VALUES (?,?,?,?,?,?,?,?,?)");
    $statement->execute(array($sup_id, $reference_no, 'purchase', 'Paid while purchasing', $paid_amount, $store_id, $invoice_id, user_id(), $created_at));

    if ($due > 0) {
        $reference_no = generate_supplier_transacton_ref_no('due');
        $statement = $db->prepare("INSERT INTO `supplier_transactions` (sup_id, reference_no, type, description, amount, store_id, ref_invoice_id, created_by, created_at) VALUES (?,?,?,?,?,?,?,?,?)");
        $statement->execute(array($sup_id, $reference_no, 'due', 'Due while purchasing', $due, $store_id, $invoice_id, user_id(), $created_at));

        $update_due = $db->prepare("UPDATE `supplier_to_store` SET `balance` = `balance` + $due WHERE `sup_id` = ? AND `store_id` = ?");
        $update_due->execute(array($sup_id, $store_id));
    } else {
        $update_due = $db->prepare("UPDATE `buying_info` SET `payment_status` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
        $update_due->execute(array('paid', $invoice_id, $store_id));
    }
    

    $Hooks->do_action('After_Create_Buying_Invoice', $invoice_id);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $invoice_id));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// Delete Invoice
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_purchase_invoice')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate invoice id
    if (empty($request->post['invoice_id'])) {
      throw new Exception($language->get('error_invoice_id'));
    }

    $Hooks->do_action('Before_Delete_Buying_Invoice', $request);

    $invoice_id = $request->post['invoice_id'];

    // Check, if invoice exist or not
    $statement = $db->prepare("SELECT * FROM `buying_info` WHERE `store_id` = ? AND `invoice_id` = ?");
    $statement->execute(array($store_id, $invoice_id));
    $invoice_info = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$invoice_info) {
        throw new Exception($language->get('error_invoice_id'));
    }
    $buying_date_time = strtotime($invoice_info['buy_date'] . ' ' . $invoice_info['buy_time']);

    $statement = $db->prepare("SELECT `item_id`, SUM(`item_quantity`) as item_quantity, SUM(`total_sell`) as total_sell, `status` FROM `buying_item` WHERE `store_id` = ? AND `invoice_id` = ? GROUP BY `status` DESC");
    $statement->execute(array($store_id, $invoice_id));
    $buying_item = $statement->fetch(PDO::FETCH_ASSOC);

    if ($buying_item['total_sell'] > 0
      && (($buying_item['status'] == 'active') || ($buying_item['status'] == 'sold'))) {

       throw new Exception($language->get('error_active_or_sold'));
    }

    // Check invoice delete duration 
    if (invoice_delete_lifespan() > $buying_date_time) {
       throw new Exception($language->get('error_delete_duration_expired'));
    }

    // increase quantitity of stock
    $return_quantity = $buying_item['item_quantity'] - $buying_item['total_sell'];
    $statement = $db->prepare("UPDATE `product_to_store` SET `quantity_in_stock` = `quantity_in_stock` - $return_quantity WHERE `store_id` = ? AND `product_id` = ?");
    $statement->execute(array($store_id, $buying_item['item_id']));

    // delete invoice info
    $statement = $db->prepare("DELETE FROM  `buying_info` WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
    $statement->execute(array($store_id, $invoice_id));

    // delete invocie item
    $statement = $db->prepare("DELETE FROM  `buying_item` WHERE `store_id` = ? AND `invoice_id` = ?");
    $statement->execute(array($store_id, $invoice_id));

    // delete buying price info
    $statement = $db->prepare("DELETE FROM  `buying_price` WHERE `store_id` = ? AND `invoice_id` = ?");
    $statement->execute(array($store_id, $invoice_id));

    $Hooks->do_action('After_Delete_Buying_Invoice', $invoice_id);
    
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_delete_success')));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// invoice edit form
if ($request->server['REQUEST_METHOD'] == 'GET' AND $request->get['action_type'] == 'EDIT') 
{
    try {
        
        $sup_id = isset($request->get['sup_id']) ? $request->get['sup_id'] : '';
        if ($sup_id) {
            if (total_product_of_supplier($sup_id) <= 0) {
                throw new Exception($language->get('error_product_not_found'));
            }

            // fetch suppliers
            $statement = $db->prepare("SELECT * FROM `suppliers` WHERE `sup_id` = ?");
            $statement->execute(array($sup_id));
            $supplier = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$statement->rowCount() > 0) {
                throw new Exception($language->get('error_supplier_not_found'));
            }
        }

        $invoice_id = isset($request->get['invoice_id']) ? $request->get['invoice_id'] : '';
        if ($invoice_id) {
            // fetch invoice info
            $statement = $db->prepare("SELECT `buying_info`.*, `buying_price`.`payable_amount`, `buying_price`.`order_tax` FROM `buying_info` 
                LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`) 
                WHERE `buying_info`.`invoice_id` = ?");
            $statement->execute(array($invoice_id));
            $invoice = $statement->fetch(PDO::FETCH_ASSOC);

            // fetch buying info
            $statement = $db->prepare("SELECT * FROM `buying_item` 
                LEFT JOIN `products` ON (`buying_item`.`item_id` = `products`.`p_id`) 
                LEFT JOIN `product_to_store` p2s ON (`buying_item`.`item_id` = `p2s`.`product_id`) 
                WHERE `p2s`.`store_id` = ? AND `buying_item`.`status` != ? AND `invoice_id` = ?");
            $statement->execute(array($store_id, 'sold', $invoice_id));
            $invoice_items = $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        include 'template/buying_form.php';
        exit();

    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
      }
}

// invoice create form
if (isset($request->get['sup_id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
    $sup_id = (int)$request->get['sup_id'];

    $Hooks->do_action('Before_Buying_Invoice_Create_Form', $sup_id);

    // fetch supplier by id
    $statement = $db->prepare("SELECT * FROM `suppliers` WHERE `sup_id` = ?");
    $statement->execute(array($sup_id));
    $supplier = $statement->fetch(PDO::FETCH_ASSOC);

    include 'template/buying_form.php';

    $Hooks->do_action('After_Buying_Invoice_Create_Form', $sup_id);
}

// view invoice
if (isset($request->get['invoice_id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'VIEW') 
{
    $invoice_id = $request->get['invoice_id'];
    $Hooks->do_action('Before_View_Buying_Invoice', $invoice_id);
    $statement = $db->prepare("SELECT * FROM `buying_info` LEFT JOIN `buying_price` ON `buying_info`.`invoice_id` = `buying_price`.`invoice_id` WHERE `buying_info`.`invoice_id` = ?");
    $statement->execute(array($invoice_id));
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = $db->prepare("SELECT * FROM `buying_item` WHERE `invoice_id` = ?");
    $statement->execute(array($invoice_id));
    $invoice_items = $statement->fetchAll(PDO::FETCH_ASSOC);
    $payment_model = $registry->get('loader')->model('payment');
    $payments = $payment_model->getPurchasePayments($invoice_id, store_id());
    include ROOT.'/_inc/template/buying_invoice.php';
    $Hooks->do_action('After_View_Buying_Invoice', $invoice_id);
}