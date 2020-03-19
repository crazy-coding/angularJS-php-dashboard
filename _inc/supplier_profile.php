<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if your logged in or not
// if user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_supplier_profile') && !$user->hasPermission('access', 'read_supplier')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('supplier');

// supplier id
$sup_id = $request->get['sup_id'];
$store_id = store_id();

// LOAD INVOICE MODEL
$invoice_model = $registry->get('loader')->model('purchase');

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Supplier_Invoice_List');

$where_query = "buying_info.is_visible = 1 AND buying_info.inv_type IN ('buy','transfer') AND buying_info.store_id = " . $store_id . " AND sup_id=" . $sup_id;
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
$from = from();
$to = to();
$where_query .= date_range_filter2($from, $to);

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
          return $row['invoice_id'];           
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
             if ($row['inv_type'] == 'due_paid') {
                return '-';
            }
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
                return '<button id="pay_now" class="btn btn-sm btn-block btn-success" title="'.$language->get('button_pay_now').'"><i class="fa fa-money"></i></button>';
            }
            return '-';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_return',
        'formatter' => function($d, $row) use($language) {
            return '<button id="return_item" class="btn btn-sm btn-block btn-warning" title="'.$language->get('button_return').'"><i class="fa fa-minus"></i></button>';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_view',
        'formatter' => function($d, $row) use($language) {
            return '<button id="view-invoice-btn" class="btn btn-sm btn-block btn-info" title="'.$language->get('button_view_receipt').'"><i class="fa fa-eye"></i></button>';
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_edit',
        'formatter' => function($d, $row) use($db, $language) {
            return '<button id="edit-invoice-info" class="btn btn-sm btn-block btn-warning" title="'.$language->get('button_edit').'"><span class="fa fa-pencil"></span></button>';     
        }
    ),
    array(
        'db' => 'invoice_id',
        'dt' => 'btn_delete',
        'formatter' => function($d, $row) use($db, $language) {
            return '<button class="btn btn-sm btn-block btn-danger" id="delete-invoice" title="'.$language->get('button_delete').'"><i class="fa fa-trash"></i></button>';

        }
    ),
);

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Supplier_Invoice_List');

/**
 *===================
 * END DATATABLE
 *===================
 */