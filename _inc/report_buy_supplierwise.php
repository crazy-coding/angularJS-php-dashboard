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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_buy_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

$where_query = "buying_info.inv_type != 'expense' AND buying_item.store_id = " . store_id();
$from = from();
$to = to();
$where_query .= date_range_filter2($from, $to);

// DB table to use
$table = "(SELECT buying_info.*, suppliers.sup_name, buying_item.item_quantity, SUM(buying_item.item_total) as buy_price, SUM(buying_item.item_quantity) as total_stock, SUM(buying_price.paid_amount) as paid_amount FROM buying_info 
      LEFT JOIN suppliers ON (buying_info.sup_id = suppliers.sup_id)
      LEFT JOIN buying_item ON (buying_info.invoice_id = buying_item.invoice_id)
      LEFT JOIN buying_price ON (buying_info.invoice_id = buying_price.invoice_id)
      WHERE $where_query
      GROUP BY buying_info.sup_id
      ORDER BY total_stock DESC) as buying_info";

// Table's primary key
$primaryKey = 'info_id';
$columns = array(
    array( 'db' => 'sup_id', 'dt' => 'sup_id' ),
    array( 
      'db' => 'created_at',  
      'dt' => 'created_at',
      'formatter' => function( $d, $row ) {
        return $row['created_at'];
      }
    ),
    array( 
      'db' => 'sup_name',  
      'dt' => 'sup_name',
      'formatter' => function( $d, $row ) {
        return $row['sup_name'];
      }
    ),
    array( 'db' => 'total_stock', 'dt' => 'total_item' ),
    array( 
      'db' => 'buy_price',  
      'dt' => 'buy_price',
      'formatter' => function( $d, $row ) use($currency) {
        $total = $row['buy_price'];
        return currency_format($total);
      }
    ),
    array( 
      'db' => 'paid_amount',  
      'dt' => 'paid_amount',
      'formatter' => function( $d, $row ) use($currency) {
        $total = $row['paid_amount'];
        return currency_format($total);
      }
    )
);
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
echo json_encode(
    SSP::simple( $request->get, $sql_details, $table, $primaryKey, $columns )
);