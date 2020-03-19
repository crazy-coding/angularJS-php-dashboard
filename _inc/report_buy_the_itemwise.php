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
if ($request->get['pid']) {
  $where_query .= " AND item_id = " . $request->get['pid'];
}
if (from()) {
  $from = from();
  $to = to();
  $where_query .= date_range_filter2($from, $to);
}

// DB table to use
$table = "(SELECT buying_info.info_id, buying_info.invoice_id, buying_info.created_at, buying_item.item_buying_price, buying_item.item_selling_price, buying_item.item_quantity, buying_item.total_sell, buying_item.status FROM buying_info 
      LEFT JOIN buying_item ON (buying_info.invoice_id = buying_item.invoice_id)
      WHERE $where_query
      ORDER BY buying_item.item_quantity DESC) as buying_info";

// Table's primary key
$primaryKey = 'info_id';
$columns = array(
    array( 'db' => 'info_id', 'dt' => 'info_id' ),
    array( 
      'db' => 'created_at',  
      'dt' => 'created_at',
      'formatter' => function( $d, $row ) {
        return $row['created_at'];
      }
    ),
    array( 
      'db' => 'invoice_id',  
      'dt' => 'invoice_id',
      'formatter' => function( $d, $row ) {
        return $row['invoice_id'];
      }
    ),
    array( 
      'db' => 'item_buying_price',  
      'dt' => 'buy',
      'formatter' => function( $d, $row ) {
        return currency_format($row['item_buying_price']);
      }
    ),
    array( 
      'db' => 'item_selling_price',  
      'dt' => 'sell',
      'formatter' => function( $d, $row ) {
        return currency_format($row['item_selling_price']);
      }
    ),
    array( 
      'db' => 'item_quantity',  
      'dt' => 'quantity',
      'formatter' => function( $d, $row ) {
        return $row['item_quantity'];
      }
    ),
    array( 
      'db' => 'total_sell',  
      'dt' => 'sold',
      'formatter' => function( $d, $row ) {
        return $row['total_sell'];
      }
    ),
    array( 
      'db' => 'invoice_id',  
      'dt' => 'available',
      'formatter' => function( $d, $row ) {
        return $row['item_quantity'] - $row['total_sell'];
      }
    ),
    array( 
      'db' => 'status',  
      'dt' => 'status',
      'formatter' => function( $d, $row ) {
        if ($row['status'] == 'active') {
          return '<span class="label label-success">'.$row['status'].'</span>';
        } elseif ($row['status'] == 'stock') {
          return '<span class="label label-info">'.$row['status'].'</span>';
        } else {
          return '<span class="label label-danger">'.$row['status'].'</span>';
        }
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