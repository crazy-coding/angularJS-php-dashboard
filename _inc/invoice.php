<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_invoice_list')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('invoice');

$store_id = store_id();
$user_id = user_id();

// LOAD INVOICE MODEL
$invoice_model = $registry->get('loader')->model('invoice');

// Delete invoice
if($request->server['REQUEST_METHOD'] == 'POST' && $request->post['action_type'] == 'DELETE')
{
    try {
        
        // Check permission
        if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_invoice')) {
          throw new Exception($language->get('error_delete_permission'));
        }

        // Validate invoice id
        if (empty($request->post['invoice_id'])) {
            throw new Exception($language->get('error_invoice_id'));
        }

        $invoice_id = $request->post['invoice_id'];

        // Check, if invoice exist or not
        $statement = $db->prepare("SELECT * FROM `selling_info` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));
        $selling_info = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$selling_info) {
            throw new Exception($language->get('error_invoice_not_found'));
        }

        // Check invoice delete duration
        $selling_date_time = strtotime($selling_info['created_at']);
        if (invoice_delete_lifespan() > $selling_date_time) {
          throw new Exception($language->get('error_delete_duration_expired'));
        }

        // Fetch selling invoice item
        $statement = $db->prepare("SELECT * FROM `selling_item` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));
        $selling_items = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Check, if invoice item exist or not
        if (!$statement->rowCount()) {
            throw new Exception($language->get('error_selling_item'));
        }

        // Delete payments
        $statement = $db->prepare("DELETE FROM  `payments` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete returns
        $statement = $db->prepare("DELETE FROM  `returns` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete return items
        $statement = $db->prepare("DELETE FROM  `return_items` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete items
        $statement = $db->prepare("DELETE FROM `selling_item` WHERE `store_id` = ? AND `invoice_id` = ?");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice price info
        $statement = $db->prepare("DELETE FROM  `selling_price` WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($store_id, $invoice_id));

        // Delete invoice info
        $statement = $db->prepare("DELETE FROM  `selling_info` WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($store_id, $invoice_id));

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
        if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_invoice_info')) {
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

        // Check invoice edit duration
        $selling_date_time = strtotime($invoice_info['created_at']);
        if (invoice_edit_lifespan() > $selling_date_time) {
          throw new Exception($language->get('error_edit_duration_expired'));
        }

        $customer_mobile = $request->post['customer_mobile'];
        $invoice_note = $request->post['invoice_note'];
        $status = $request->post['status'];
        $subtotal = $invoice_info['subtotal'];
        $payable_amount = $invoice_info['payable_amount'];
        $discount_amount = $request->post['discount_amount'];
        if ($discount_amount > $subtotal) {
            throw new Exception($language->get('error_discount_amount_exceed'));
        }

        $payable_amount = $subtotal - $discount_amount;
        $paid_amount = $invoice_info['paid_amount'];
        $due_paid = $invoice_info['due_paid'];
        $due = 0;
        $balance = 0;
        if ($due_paid > $payable_amount) {
            $due_paid = $payable_amount;
        }
        if ($payable_amount > $paid_amount) {
            $due = $payable_amount - $paid_amount;
        }
        if ($paid_amount > $payable_amount) {
            $balance = $paid_amount - $payable_amount;
        }

        if ($balance > 0) {
            $paid_amount = $paid_amount - $balance;
            $statement = $db->prepare("INSERT INTO `payments` SET `type` = ?, `store_id` = ?, `invoice_id` = ?, `pos_balance` = ?, `created_by` = ?");
            $statement->execute(array('change', $store_id, $invoice_id, $balance, $user_id));
        } else {
            $statement = $db->prepare("DELETE FROM `payments` WHERE `store_id` = ? AND `invoice_id` = ? AND `type` = ?");
            $statement->execute(array($store_id, $invoice_id, 'change'));
        }

        $statement = $db->prepare("UPDATE `selling_price` SET `discount_amount` = ?, `payable_amount` = ?, `paid_amount` = ?, `due_paid` = ?, `due` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($discount_amount, $payable_amount, $paid_amount, $due_paid, $due, $store_id, $invoice_id));

        if ($due > 0) {
            $payment_status = 'due';
        } else {
            $payment_status = 'paid';
        }

        $statement = $db->prepare("DELETE FROM `payments` WHERE `store_id` = ? AND `invoice_id` = ? AND `type` = ? AND `note` = ?");
        $statement->execute(array($store_id, $invoice_id, 'discount', 'discount_while_invoice_edit'));
        if ($discount_amount > 0) {
            $statement = $db->prepare("INSERT INTO `payments` (type, store_id, invoice_id, amount, note, total_paid, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $statement->execute(array('discount', $store_id, $invoice_id, $discount_amount, 'discount_while_invoice_edit', $discount_amount, $user_id, date_time()));
        }

        $statement = $db->prepare("UPDATE `selling_info` SET `payment_status` = ?, `checkout_status` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($payment_status, 1, $store_id, $invoice_id));

        // Update invoice info
        $statement = $db->prepare("UPDATE `selling_info` SET `customer_mobile` = ?, `invoice_note` = ?, `status` = ? WHERE `store_id` = ? AND `invoice_id` = ? LIMIT 1");
        $statement->execute(array($customer_mobile, $invoice_note, $status, $store_id, $invoice_id));

        header('Content-Type: application/json');
        echo json_encode(array('msg' => $language->get('text_sell_update_success')));
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

        include('template/invoice_info_edit_form.php');
        exit();
        
    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

// Fetch Invoice
if ($request->server['REQUEST_METHOD'] == 'GET' && isset($request->get['invoice_id']))
{
    try {

        // Validate invoice id
        $invoice_id = $request->get['invoice_id'];
        $invoice = $invoice_model->getInvoiceInfo($invoice_id);
        if (!$invoice) {
            throw new Exception($language->get('error_invoice_id'));
        }        

        // fetch invoice info
        $statement = $db->prepare("SELECT selling_info.*, selling_price.*, customers.customer_name FROM `selling_info` 
            LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
            LEFT JOIN `customers` ON (`selling_info`.`customer_id` = `customers`.`customer_id`) 
            WHERE `selling_info`.`invoice_id` = ?");
        $statement->execute(array($invoice_id));
        $invoice = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($invoice)) {
            throw new Exception($language->get('error_selling_not_found'));
        }

        if (isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
            $selling_date_time = strtotime($invoice['created_at']);
            if (invoice_edit_lifespan() > $selling_date_time) {
                throw new Exception($language->get('error_duration_expired'));
            }
        }
        
        // fetch invoice item
        $statement = $db->prepare("SELECT * FROM `selling_item` WHERE invoice_id = ?");
        $statement->execute(array($invoice_id));
        $selling_items = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($selling_items)) {
            throw new Exception($language->get('error_selling_item'));
        }

        $invoice['items'] = $selling_items;

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

// view invoice details
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEDETAILS') {

    try {

        $user_id = isset($request->get['user_id']) ? $request->get['user_id'] : null;
        $where_query = "`selling_info`.`inv_type` = 'sell' AND `created_by` = ? AND `status` = ?";
        $from = from() ? from() : date('Y-m-d');
        $to = to() ? to() : date('Y-m-d');
        $where_query .= date_range_filter($from, $to);
        $statement = $db->prepare("SELECT * FROM `selling_info` 
            LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`)
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

// view invoice due details
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'INVOICEDUEDETAILS') {

    try {

        $user_id = isset($request->get['user_id']) ? $request->get['user_id'] : null;
        $where_query = "`selling_info`.`inv_type` = 'sell' AND `created_by` = ? AND `status` = ? AND `selling_price`.`due` > 0";
        $from = from() ? from() : date('Y-m-d');
        $to = to() ? to() : date('Y-m-d');
        $where_query .= date_range_filter($from, $to);

        $statement = $db->prepare("SELECT * FROM `selling_info` 
            LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`)
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

$where_query = "selling_info.status = 1 AND selling_info.store_id = '$store_id'";
if (isset($request->get['type']) && ($request->get['type'] != 'undefined') && $request->get['type'] != '') {
    switch ($request->get['type']) {
        case 'due':
            $where_query .= " AND selling_info.payment_status = 'due'";
            break;
        case 'paid':
            $where_query .= " AND selling_info.payment_status = 'paid'";
            break;
        default:
            # code...
            break;
    }
};
if ($request->get['type'] != 'all_due' && $request->get['type'] != 'all_invoice') {
    $from = from();
    $to = to();
    $where_query .= date_range_filter($from, $to);
}

// DB table to use
$table = "(SELECT selling_info.*, selling_price.payable_amount, selling_price.paid_amount, selling_price.due FROM `selling_info` 
  LEFT JOIN `selling_price` ON (selling_info.invoice_id = selling_price.invoice_id) 
  WHERE $where_query) as selling_info";

// Table's primary key
$primaryKey = 'info_id';

$columns = array(
    array( 'db' => 'edit_counter', 'dt' => 'edit_counter' ),
    array( 'db' => 'invoice_id', 'dt' => 'id' ),
    array(
        'db' => 'invoice_id',
        'dt' => 'invoice_id',
        'formatter' => function( $d, $row) {
            $o = $row['invoice_id'];   
            if ($row['edit_counter'] > 0) {
                $o .= ' <span class="fa fa-edit" title="'.$row['edit_counter'].' time(s) edited"></span>';
            }         
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
        'db' => 'customer_id',
        'dt' => 'customer_name',
        'formatter' => function( $d, $row) {

            $customer = get_the_customer($row['customer_id']);
            return '<a href="customer_profile.php?customer_id=' . $customer['customer_id'] . '">' . $customer['customer_name'] . '</a>';
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
                return '<button id="pay_now" class="btn btn-sm btn-block btn-success" title="'.$language->get('button_view_receipt').'" data-loading-text="..."><i class="fa fa-money"></i></button>';
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
            return '<a class="btn btn-sm btn-block btn-info" href="view_invoice.php?invoice_id='.$row['invoice_id'].'" title="'.$language->get('button_view_receipt').'" data-loading-text="..."><i class="fa fa-eye"></i></a>';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_edit',
        'formatter' => function($d, $row) use($db, $language) {
            $selling_date_time = strtotime($row['created_at']);
            if (invoice_edit_lifespan() > $selling_date_time) {
                return '<a class="btn btn-sm btn-block btn-default" href="#" disabled><span class="fa fa-pencil"></span></a>';
            }
            return '<button id="edit-invoice-info" class="btn btn-sm btn-block btn-primary" title="'.$language->get('button_edit').'" data-loading-text="..."><span class="fa fa-pencil"></span></button>'; 
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_delete',
        'formatter' => function($d, $row) use($db, $language) {
            $selling_date_time = strtotime($row['created_at']);
            if (invoice_delete_lifespan() > $selling_date_time) {
                return '<a class="btn btn-sm btn-block btn-default" href="#" disabled><span class="fa fa-trash"></span></a>';
            } 
            return '<button class="btn btn-sm btn-block btn-danger" id="delete-invoice" title="'.$language->get('button_delete').'" data-loading-text="..."><i class="fa fa-trash"  data-loading-text="..."></i></button>';
        }
    )
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