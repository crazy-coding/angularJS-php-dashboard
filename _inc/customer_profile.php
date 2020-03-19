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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_customer_profile')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

$customer_id = (int)$request->get['customer_id'];

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Customer_Invoice_List');

$where_query = "selling_info.status = 1 AND selling_info.store_id = " . store_id();
$from = from();
$to = to();
if (from()) {
  $where_query .= date_range_filter($from, $to);
}
// DB table to use
$table = "(SELECT selling_info.*, selling_price.payable_amount, selling_price.paid_amount, selling_price.due 
  FROM selling_info 
  JOIN selling_price ON selling_info.invoice_id = selling_price.invoice_id
  WHERE $where_query) as selling_info";
 
// Table's primary key
$primaryKey = 'info_id';

$columns = array(
    array(
        'db' => 'invoice_id',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            return 'row_'.$d;
        }
    ),
    array( 
      'db' => 'created_at',   
      'dt' => 'created_at' ,
      'formatter' => function($d, $row) {
          return $row['created_at'];
      }
    ),
    array( 'db' => 'edit_counter', 'dt' => 'edit_counter' ),
    array(
        'db'        => 'invoice_id',
        'dt'        => 'invoice_id',
        'formatter' => function( $d, $row) {
            $o = $row['invoice_id'];   
            if ($row['edit_counter'] > 0) {
                $o .= ' <span class="fa fa-edit text-red" title="Edited: '.$row['edit_counter'].' time(s)"></span>';
            }         
            return $o;
        }
    ),
    array( 
      'db' => 'payable_amount',   
      'dt' => 'payable_amount',
      'formatter' => function($d, $row) {
        $pyable_amount = $row['payable_amount'];
        return currency_format($pyable_amount, 2);
      }
    ),
    array( 
      'db' => 'paid_amount',   
      'dt' => 'paid_amount',
      'formatter' => function($d, $row) {
        $pyable_amount = $row['paid_amount'];
        return currency_format($pyable_amount, 2);
      }
    ),
    array( 
      'db' => 'due',   
      'dt' => 'due' ,
      'formatter' => function($d, $row) {
          return currency_format($row['due']);
      }
    ),
    array( 
      'db' => 'invoice_id',   
      'dt' => 'btn_view' ,
      'formatter' => function($d, $row) {
          return '<a id="view-invoice" class="btn btn-sm btn-block btn-info" href="view_invoice.php?invoice_id=' . $row['invoice_id'] . '" title="View Invoice"><i class="fa fa-fw fa-eye"></i></button>';
      }
    )
);
 
$where_query = "1=1";
if ($customer_id) {
  $where_query .= "  AND customer_id = " . $customer_id;
}

// output for datatable
echo json_encode(
  SSP::complex( $request->get, $sql_details, $table, $primaryKey, $columns, null, $where_query)
);

$Hooks->do_action('After_Showing_Customer_Invoice_List');

/**
 *===================
 * END DATATABLE
 *===================
 */