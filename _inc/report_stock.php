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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_stock_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('report_stock');

$store_id = store_id();
$user_id = user_id();
$report_model = $registry->get('loader')->model('report');
$sup_id = isset($request->get['sup_id']) ? $request->get['sup_id'] : '';
$where_query = "p2s.store_id = $store_id";
if ($sup_id) {
  $where_query .= " AND p2s.sup_id = $sup_id";
}

//===========================
// Datatable start
//===========================

// DB table to use
$table = "(SELECT products.p_id, products.p_name, p2s.store_id, p2s.sup_id, p2s.quantity_in_stock, p2s.sell_price as sell_price FROM products 
  LEFT JOIN product_to_store p2s ON (products.p_id = p2s.product_id)
  WHERE $where_query
  GROUP BY products.p_id, p2s.sup_id
  ORDER BY p2s.sup_id ASC) as products";

// Table's primary key
$primaryKey = 'p_id';

// indexes
$columns = array(
    array( 'db' => 'p_id',  'dt' => 'p_id'),
    array( 
      'db' => 'sup_id',  
      'dt' => 'sl',
      'formatter' => function( $d, $row ) {
        return '';
      }
    ),
    array( 
      'db' => 'sup_id',  
      'dt' => 'sup_name',
      'formatter' => function( $d, $row ) {
        return '<a style="color:#fff;" href="supplier_profile.php?sup_id='.$row['sup_id'].'">'.get_the_supplier($row['sup_id'], 'sup_name') . ' <small>[' .total_product_of_supplier($row['sup_id']) .' Product(s)]</small></a>';
      }
    ),
    array( 
      'db' => 'p_name',  
      'dt' => 'product_name',
      'formatter' => function( $d, $row ) {
        return '<a href="product_details.php?p_id='.$row['p_id'].'">'.$row['p_name'].'</a>';
      }
    ),
    array( 
      'db' => 'quantity_in_stock',  
      'dt' => 'available',
      'formatter' => function( $d, $row ) {
        return $row['quantity_in_stock'];
      }
    ),
    array( 
      'db' => 'sell_price',  
      'dt' => 'total',
      'formatter' => function( $d, $row ) {
        return currency_format($row['sell_price'] * $row['quantity_in_stock']);
      }
    ),
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

echo json_encode(
    SSP::simple( $request->get, $sql_details, $table, $primaryKey, $columns )
);