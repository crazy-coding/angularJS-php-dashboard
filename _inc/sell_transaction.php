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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_sell_transaction')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('transaction');

$store_id = store_id();
$user_id = user_id();

// View transaction
if (isset($request->get['id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'VIEW') 
{
    $id = $request->get['id'];
    $statement = $db->prepare("SELECT * FROM `customer_transactions` WHERE `id` = ?");
    $statement->execute(array($id));
    $transaction = $statement->fetch(PDO::FETCH_ASSOC);
    include 'template/sell_transaction_view.php';
    exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Transactions_List');

$where_query = "1=1";
if (isset($request->get['customer_id']) && $request->get['customer_id'] != 'null') {
  $where_query .= " AND customer_transactions.customer_id=".$request->get['customer_id'];
}

if (from()) {
  $from = from();
  $to = to();
  $where_query .= date_range_sell_transaction_filter($from, $to);
}

// DB table to use
$table = "(SELECT * FROM customer_transactions 
  WHERE $where_query GROUP by id
  ) as expenses";
 
// Table's primary key
$primaryKey = 'id';

$columns = array(
  array(
      'db' => 'id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'id', 'dt' => 'id' ),
  array( 'db' => 'reference_no', 'dt' => 'reference_no' ),
  array( 
    'db' => 'type',   
    'dt' => 'type',
    'formatter' => function($d, $row) use($language) {
      if ($row['type'] == 'due') {
        return '<span class="label label-danger">'.str_replace('_', ' ', ucfirst($row['type'])).'</span>';
      } elseif ($row['type'] == 'due_paid') {
        return '<span class="label label-success">'.str_replace('_', ' ', ucfirst($row['type'])).'</span>';
      } else {
        return '<span class="label label-warning">Sell</span>';
      }
    }
  ),
  array( 
    'db' => 'customer_id',   
    'dt' => 'category_name',
    'formatter' => function($d, $row) {
        return get_the_customer($row['customer_id'], 'customer_name');
    }
  ),
  array( 
    'db' => 'pmethod_id',   
    'dt' => 'pmethod',
    'formatter' => function($d, $row) {
      return get_the_pmethod($row['pmethod_id'], 'name');
    }
  ),
  array( 
    'db' => 'amount',   
    'dt' => 'amount',
    'formatter' => function($d, $row) use($language) {
      return currency_format($row['amount']);
    }
  ),
  array( 
    'db' => 'created_by',   
    'dt' => 'created_by',
    'formatter' => function($d, $row) use($language) {
     return get_the_user($row['created_by'], 'username');
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
    'db'        => 'id',
    'dt'        => 'btn_view',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="view-transaction-btn" class="btn btn-sm btn-block btn-info" type="button" title="'.$language->get('button_view').'"><i class="fa fa-fw fa-eye"></i></button>';
    }
  ),
); 

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Transactions_List');

/**
 *===================
 * END DATATABLE
 *===================
 */