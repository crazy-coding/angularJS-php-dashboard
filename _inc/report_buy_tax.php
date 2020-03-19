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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_buy_tax_report')) {
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


/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Buy_Tax_Report');

$where_query = "buying_price.item_tax > 0 AND buying_info.is_visible = 1 AND buying_info.store_id = $store_id";

$from = from();
$to = to();
$where_query .= date_range_filter2($from, $to);

// DB table to use
$table = "(SELECT buying_info.*, buying_price.item_tax FROM `buying_info` LEFT JOIN `buying_price` ON (buying_info.invoice_id = buying_price.invoice_id) WHERE $where_query) as buying_info";

// Table's primary key
$primaryKey = 'info_id';

$columns = array(
    array(
      'db' => 'info_id',
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
    array(
        'db' => 'invoice_id',
        'dt' => 'invoice_id',
        'formatter' => function( $d, $row) {
            $o = $row['invoice_id'];         
            return $o;
        }
    ),
    array(
        'db' => 'item_tax',
        'dt' => 'tax_amount',
        'formatter' => function($d, $row) {
            return currency_format($row['item_tax']);
        }
    ),
);

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Buy_Tax_Report');

/**
 *===================
 * END DATATABLE
 *===================
 */