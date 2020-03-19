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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_buy_payment_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

$where_query = "buying_payments.store_id = " . store_id();
$from = from();
$to = to();
$where_query .= date_range_buying_payments_filter($from, $to);

// DB table to use
$table = "(SELECT buying_payments.*, SUM(amount) as totalAmount FROM buying_payments 
        WHERE $where_query GROUP BY `invoice_id`) as buying_payments";

// Table's primary key
$primaryKey = 'id';
$columns = array(
  array( 'db' => 'id', 'dt' => 'id' ),
  array( 'db' => 'created_at', 'dt' => 'created_at' ),
  array( 
      'db' => 'type',  
      'dt' => 'type',
      'formatter' => function( $d, $row ) {
        return '<span class="label label-warning">'.ucfirst(str_replace('_',' ',$row['type'])).'</span>';
      }
    ),
  array( 'db' => 'invoice_id', 'dt' => 'ref_no' ),
  array( 'db' => 'details', 'dt' => 'details' ),
  array( 
    'db' => 'pmethod_id',   
    'dt' => 'pmethod_name' ,
    'formatter' => function($d, $row) {
      $o = '<b>'.get_the_pmethod($row['pmethod_id'], 'name').'</b>';
      $details = unserialize($row['details']);
      if (!empty($details)) {
        $o .= '<ul>';
        foreach ($details as $key => $value) {
          $o .= '<li>'. str_replace('_',' ', strtoupper($key)) . ' = '.$value.'</li>';
        }
        $o .= '</ul>';
      }
      return $o;
    }
  ),
  array( 
      'db' => 'note',  
      'dt' => 'note',
      'formatter' => function( $d, $row ) {
        return $row['note'];
      }
    ),
  array( 
      'db' => 'totalAmount',  
      'dt' => 'amount',
      'formatter' => function( $d, $row ) {
        return currency_format($row['totalAmount']);
      }
    ),
);
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);