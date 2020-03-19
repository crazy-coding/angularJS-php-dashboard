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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_invoice_list')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('purchase');
$store_id = store_id();

// LOAD INVOICE MODEL
$invoice_model = $registry->get('loader')->model('purchase');

if (isset($request->get['action_type']) && $request->get['action_type'] == 'DETAILS')
{
    $invoice_id = isset($request->get['invoice_id']) && $request->get['invoice_id'] != 'null' ? $request->get['invoice_id'] : '';
    $order = array();
    $items = array();
    $where_query = "`buying_info`.`store_id` = ?";
    if ($invoice_id) {
      $where_query .= " AND `buying_info`.`invoice_id` = '{$invoice_id}'";
    }
    $statement = $db->prepare("SELECT * FROM `buying_info` 
          LEFT JOIN `buying_price` ON (`buying_price`.`invoice_id` = `buying_info`.`invoice_id`)
          WHERE $where_query");
    $statement->execute(array($store_id));
    $order = $statement->fetch(PDO::FETCH_ASSOC);

    $payment_model = $registry->get('loader')->model('payment');
    $items = $invoice_model->getInvoiceItems($order['invoice_id'], $store_id);
    $payments = $payment_model->getPurchasePayments($order['invoice_id'], $store_id);

    $order['items']     = $items;
    $order['payments']  = $payments;

    ob_start();
    include 'template/purchase_payment_form.php';
    $html = ob_get_contents();
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'html' => $html, 'order' => $order));
    exit();
}

// Delete invoice
if($request->server['REQUEST_METHOD'] == 'POST' && $request->post['action_type'] == 'DELETE')
{
    try {
        
        // Check permission
        if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_purchase_invoice')) {
          throw new Exception($language->get('error_delete_permission'));
        }

        // Validate invoice id
        if (empty($request->post['invoice_id'])) {
            throw new Exception($language->get('error_invoice_id'));
        }

        $invoice_id = $request->post['invoice_id'];

        // Check, if invoice exist or not
        $statement = $db->prepare("SELECT * FROM `buying_info` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));
        $buying_info = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$buying_info) {
            throw new Exception($language->get('error_invoice_not_found'));
        }

        // Check invoice delete duration
        $buying_date_time = strtotime($buying_info['created_at']);
        if (invoice_delete_lifespan() > $buying_date_time) {
          throw new Exception($language->get('error_delete_duration_expired'));
        }

        $items = $invoice_model->getInvoiceItems($invoice_id);

        // Delete payments
        $statement = $db->prepare("DELETE FROM  `buying_payments` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete returns
        $statement = $db->prepare("DELETE FROM  `buying_returns` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete return items
        $statement = $db->prepare("DELETE FROM  `buying_return_items` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice info
        $statement = $db->prepare("DELETE FROM  `buying_info` WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice items
        $statement = $db->prepare("DELETE FROM  `buying_item` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice price info
        $statement = $db->prepare("DELETE FROM  `buying_price` WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($store_id, $invoice_id));

        foreach ($items as $item) {
            $item_info = get_the_product($item['item_id']);
            $quantity = $item['item_quantity'] - $item['total_sell'];
            if ($quantity <= $item_info['quantity_in_stock']) {
                $statement = $db->prepare("UPDATE  `product_to_store` SET `quantity_in_stock` = `quantity_in_stock`-$quantity WHERE `store_id` = ? AND `product_id` = ?");
                $statement->execute(array($store_id, $item['item_id']));
            }
        }

        header('Content-Type: application/json');
        echo json_encode(array('msg' => $language->get('text_delete_success')));
        exit();

    } catch(Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
  }
}

// Update invoice info
if($request->server['REQUEST_METHOD'] == 'POST' && $request->post['action_type'] == 'UPDATEINVOICEINFO')
{
    try {
        
        // Check permission
        if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_purchase_invoice_info')) {
          throw new Exception($language->get('error_update_permission'));
        }

        // Validate invoice id
        if (empty($request->post['invoice_id'])) {
            throw new Exception($language->get('error_invoice_id'));
        }

        $invoice_id = $request->post['invoice_id'];

        // Check, if invoice exist or not
        $invoice_info = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice_info) {
            throw new Exception($language->get('error_invoice_id'));
        }

        if (!is_numeric($request->post['status'])) {
            throw new Exception($language->get('error_status'));
        }

        // $sup_id = $request->post['sup_id'];
        $invoice_note = $request->post['invoice_note'];
        $status = $request->post['status'];
        $payable_amount = $invoice_info['payable_amount'];

        // Update invoice info
        $statement = $db->prepare("UPDATE `buying_info` SET `invoice_note` = ?, `is_visible` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($invoice_note, $status, $store_id, $invoice_id));


        header('Content-Type: application/json');
        echo json_encode(array('msg' => $language->get('text_purchase_update_success')));
        exit();

    } catch(Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
  }
}

// Invoice Info Edit Form
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEINFOEDIT') {

    try {

        $invoice_id = isset($request->get['invoice_id']) ? $request->get['invoice_id'] : null;
        $invoice = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice) {
            throw new Exception($language->get('error_invoice_not_found'));
        }

        include('template/buying_invoice_info_edit_form.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// Fetch invoice
if ($request->server['REQUEST_METHOD'] == 'GET' && isset($request->get['invoice_id']))
{
    try {

        // Validate invoice id
        $invoice_id = $request->get['invoice_id'];
        $invoice = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice) {
            throw new Exception($language->get('error_invoice_id'));
        }        

        // Fetch invoice info
        $statement = $db->prepare("SELECT `buying_info`.*, `buying_price`.*, `suppliers`.`sup_name` FROM `buying_info` 
            LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`) 
            LEFT JOIN `suppliers` ON (`buying_info`.`sup_id` = `suppliers`.`sup_id`) 
            WHERE `buying_info`.`invoice_id` = ?");
        $statement->execute(array($invoice_id));
        $invoice = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($invoice)) {
            throw new Exception($language->get('error_buying_not_found'));
        }

        if (isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
            $buying_date_time = strtotime($invoice['created_at']);
            if (invoice_edit_lifespan() > $buying_date_time) {
                throw new Exception($language->get('error_duration_expired'));
            }
        }
        
        // Fetch invoice item
        $statement = $db->prepare("SELECT * FROM `buying_item` WHERE invoice_id = ?");
        $statement->execute(array($invoice_id));
        $buying_items = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($buying_items)) {
            throw new Exception($language->get('error_buying_item'));
        }

        $invoice['items'] = $buying_items;

        header('Content-Type: application/json');
        echo json_encode(array('msg' => $language->get('text_success'), 'invoice' => $invoice));
        exit();

    } catch(Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// View invoice details
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEDETAILS') {

    try {

        $user_id = isset($request->get['user_id']) ? $request->get['user_id'] : null;
        $where_query = "`buying_info`.`inv_type` IN ('buy','transfer') AND `created_by` = ? AND `is_visible` = ?";
        $from = from() ? from() : date('Y-m-d');
        $to = to() ? to() : date('Y-m-d');
        $where_query .= date_range_filter($from, $to);
        $statement = $db->prepare("SELECT * FROM `buying_info` 
            LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`)
            WHERE $where_query");
        $statement->execute(array($user_id, 1));
        $invoices = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!$statement->rowCount() > 0) {
            throw new Exception($language->get('error_not_found'));
        }

        include('template/user_invoice_details.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// View invoice due details
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEDUEDETAILS') {

    try {

        $user_id = isset($request->get['user_id']) ? $request->get['user_id'] : null;
        $where_query = "`buying_info`.`inv_type`IN ('buy','transfer') AND `created_by` = ? AND `is_visible` = ? AND `buying_price`.`due` > 0";
        $from = from() ? from() : date('Y-m-d');
        $to = to() ? to() : date('Y-m-d');
        $where_query .= date_range_filter($from, $to);

        $statement = $db->prepare("SELECT * FROM `buying_info` 
            LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`)
            WHERE $where_query");
        $statement->execute(array($user_id, 1));
        $invoices = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!$statement->rowCount() > 0) {
            throw new Exception($language->get('error_not_found'));
        }

        include('template/user_invoice_due_details.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Invoice_List');

$where_query = "buying_info.is_visible = 1 AND buying_info.inv_type IN ('buy','transfer') AND buying_info.store_id = " . $store_id;
if (isset($request->get['type']) && ($request->get['type'] != 'undefined') && $request->get['type'] != '') {
    switch ($request->get['type']) {
        case 'due':
            $where_query .= " AND buying_info.payment_status = 'due'";
            break;
        case 'paid':
            $where_query .= " AND buying_info.payment_status = 'paid'";
            break;
        case 'transfer':
            $where_query .= " AND buying_info.inv_type = 'transfer'";
            break;
        default:
            # code...
            break;
    }
};
if (from()) {
    $from = from();
    $to = to();
    $where_query .= date_range_filter2($from, $to);
}

// DB table to use
$table = "(SELECT buying_info.*, buying_price.payable_amount, buying_price.paid_amount, buying_price.due FROM `buying_info` 
  LEFT JOIN `buying_price` ON (buying_info.invoice_id = buying_price.invoice_id) 
  WHERE $where_query) as buying_info";

// Table's primary key
$primaryKey = 'info_id';

$columns = array(
    array( 'db' => 'invoice_id', 'dt' => 'id' ),
    array( 
      'db' => 'inv_type',   
      'dt' => 'inv_type' ,
      'formatter' => function($d, $row) {
        return '<span class="label label-warning">'.ucfirst($row['inv_type']).'</span>';
      }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'invoice_id',
        'formatter' => function( $d, $row) {
            $o = $row['invoice_id'];           
            return $o;
        }
    ),
    array( 
      'db' => 'created_at',   
      'dt' => 'created_at' ,
      'formatter' => function($d, $row) {
        return $row['created_at'];
      }
    ),
    array(
        'db' => 'sup_id',
        'dt' => 'sup_name',
        'formatter' => function( $d, $row) {

            $supplier = get_the_supplier($row['sup_id']);
            return '<a href="supplier_profile.php?sup_id=' . $supplier['sup_id'] . '">' . $supplier['sup_name'] . '</a>';
        }
    ),
    array(
        'db' => 'created_by',
        'dt' => 'created_by',
        'formatter' => function( $d, $row) use($db) {
            $the_user = get_the_user($row['created_by']);
            if (isset($the_user['id'])) {
                return '<a href="user.php?user_id=' . $the_user['id'] . '&username='.$the_user['username'].'">' . $the_user['username'] . '</a>';
            }
            return;
        }
    ),
    array(
        'db' => 'payable_amount',
        'dt' => 'invoice_amount',
        'formatter' => function($d, $row) {
            return currency_format($row['payable_amount']);
        }
    ),
    array(
        'db' => 'paid_amount',
        'dt' => 'paid_amount',
        'formatter' => function($d, $row) use($invoice_model) {
            return currency_format($row['paid_amount']);
        }
    ),
    array(
        'db' => 'due',
        'dt' => 'due',
        'formatter' => function($d, $row) use($invoice_model) {
            return currency_format($row['due']);
        }
    ),
    array( 'db' => 'payment_status', 'dt' => 'payment_status' ),
    array(
        'db' => 'invoice_id',
        'dt' => 'status',
        'formatter' => function($d, $row) use($language, $invoice_model)  {
            if ($row['payment_status'] == 'due') {
                return '<span class="label label-danger">'.$language->get('text_unpaid').'</span>';
            } else {
                return '<span class="label label-success">'.$language->get('text_paid').'</span>';
            }
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_pay',
        'formatter' => function($d, $row) use($language) {
            if ($row['payment_status'] != 'paid') {
                return '<button id="pay_now" class="btn btn-sm btn-block btn-success" title="'.$language->get('button_pay_now').'" data-loading-text="..."><i class="fa fa-money"></i></button>';
            }
            return '-';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_return',
        'formatter' => function($d, $row) use($language) {
            return '<button id="return_item" class="btn btn-sm btn-block btn-warning" title="'.$language->get('button_return').'" data-loading-text="..."><i class="fa fa-minus"></i></button>';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_view',
        'formatter' => function($d, $row) use($language) {
            return '<button id="view-invoice-btn" class="btn btn-sm btn-block btn-info" title="'.$language->get('button_view_receipt').'" data-loading-text="..."><i class="fa fa-eye"></i></button>';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_edit',
        'formatter' => function($d, $row) use($db, $language) {
            return '<button id="edit-invoice-info" class="btn btn-sm btn-block btn-warning" title="'.$language->get('button_edit').'" data-loading-text="..."><span class="fa fa-pencil"></span></button>';     
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_delete',
        'formatter' => function($d, $row) use($db, $language) {
            return '<button class="btn btn-sm btn-block btn-danger" id="delete-invoice" title="'.$language->get('button_delete').'" data-loading-text="..."><i class="fa fa-trash"></i></button>';

        }
    ),
);

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Invoice_List');

/**
 *===================
 * END DATATABLE
 *===================
 */