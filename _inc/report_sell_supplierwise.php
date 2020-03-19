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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_sell_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

$report_model = $registry->get('loader')->model('report');
$store_id = store_id();

$where_query = "selling_info.store_id = '$store_id'";
$from = from();
$to = to();
$where_query .= date_range_filter($from, $to);

// DB table to use
$table = "(SELECT selling_info.invoice_id, selling_info.created_at, selling_item.id, selling_item.sup_id, selling_item.item_name, SUM(selling_item.item_quantity) as total_item, SUM(selling_item.item_discount) as discount, SUM(selling_item.item_tax) as tax, SUM(selling_item.total_buying_price) as buy_price, SUM(selling_item.item_total) as sell_price FROM selling_item 
  LEFT JOIN selling_info ON (selling_item.invoice_id = selling_info.invoice_id)
  LEFT JOIN selling_price ON (selling_item.invoice_id = selling_price.invoice_id)
  WHERE $where_query
  GROUP BY selling_item.sup_id
  ORDER BY sell_price DESC) as selling_item";

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array( 'db' => 'sup_id', 'dt' => 'id' ),
    array( 'db' => 'invoice_id', 'dt' => 'invoice_id' ),
    array( 
      'db' => 'created_at',
      'dt' => 'selling_date',
      'formatter' => function( $d, $row ) {
        return $row['created_at'];
      }
    ),
    array( 
      'db' => 'sup_id',  
      'dt' => 'sup_name',
      'formatter' => function( $d, $row ) {
        return get_the_supplier($row['sup_id'], 'sup_name');
      }
    ),
    array( 'db' => 'total_item', 'dt' => 'total_item' ),
    array( 
      'db' => 'buy_price',  
      'dt' => 'buy_price',
      'formatter' => function( $d, $row ) {
        $total = $row['buy_price'];
        return currency_format($total);
      }
    ),
    array( 
      'db' => 'sell_price',  
      'dt' => 'sell_price',
      'formatter' => function( $d, $row ) use($report_model) {
        $discount = $report_model->getTotalDiscountAmountBy('itemwise', $row['invoice_id'], from(), to(), store_id());
        $total = $row['sell_price'] - $discount;
        return currency_format($total);

      }
    ),
    array( 
      'db' => 'tax',  
      'dt' => 'tax',
      'formatter' => function( $d, $row ) {
        return currency_format($row['tax']);
      }
    ),
    array( 
      'db' => 'discount',  
      'dt' => 'discount',
      'formatter' => function( $d, $row ) use($report_model) {
        $discount = $report_model->getTotalDiscountAmountBy('itemwise', $row['invoice_id'], from(), to(), store_id());
        return currency_format($discount);

      }
    ),
    array( 
      'db' => 'sell_price',
      'dt' => 'profit',
      'formatter' => function( $d, $row ) use($report_model) {
        $discount = $report_model->getTotalDiscountAmountBy('itemwise', $row['invoice_id'], from(), to(), store_id());
        $total = ($row['sell_price'] - $row['buy_price']) - $discount;
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