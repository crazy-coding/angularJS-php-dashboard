<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then return error
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return error
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_loan')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('loan');
$store_id = store_id();

// LOAD LOAN MODEL
$loan_model = $registry->get('loader')->model('loan');

/**
 *===================
 * START DATATABLE
 *===================
 */
$where_query = "1=1";
if (from()) {
  $from = from();
  $to = to();
  $where_query .= date_range_loan_payment_filter($from, $to);
}
$table = 'loan_payments';
$table = "(SELECT loan_payments.* FROM loan_payments WHERE $where_query) as loan_payments";
// Table's primary key
$primaryKey = 'id';

// indexes
$columns = array(
    array(
        'db' => 'id',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            return 'row_'.$d;
        }
    ),
    array( 'db' => 'lloan_id', 'dt' => 'loan_id' ),
    array( 'db' => 'ref_no', 'dt' => 'ref_no' ),
    array( 'db' => 'created_at', 'dt' => 'created_at' ),
    array( 
      'db' => 'created_by',   
      'dt' => 'created_by' ,
      'formatter' => function($d, $row) {
        return get_the_user($row['created_by'],'username');
      }
    ),
    array( 'db' => 'note', 'dt' => 'note' ),
    array( 
      'db' => 'paid',   
      'dt' => 'paid' ,
      'formatter' => function($d, $row) {
        return currency_format($row['paid']);
      }
    ),
);

$loans = SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns);
echo json_encode($loans);

/**
 *===================
 * END DATATABLE
 *===================
 */
 