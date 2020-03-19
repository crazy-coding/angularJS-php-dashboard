<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// If user is not logged in then return error
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// If user have not reading permission return error
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_giftcard_topup')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('giftcard');

// LOAD GIFTCARD MODEL
$giftcard_model = $registry->get('loader')->model('giftcard');

// Delete giftcard_topup
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') {
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_giftcard_topup')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate topup id
    if (empty($request->post['id'])) {
      throw new Exception($language->get('error_topup_id'));
    }
    $id = $request->post['id'];

    // Validate topup card_no
    if (empty($request->post['card_no'])) {
      throw new Exception($language->get('error_topup_card_no'));
    }

    // Validate topup amount
    if (empty($request->post['amount'])) {
      throw new Exception($language->get('error_topup_amount'));
    }
    $topup_amount = (float)str_replace(',','',$request->post['amount']);

    $card_no = $request->post['card_no'];
    $giftcard = $giftcard_model->getGiftcard($card_no);
    if (!$giftcard) {
      throw new Exception($language->get('error_card_not_found'));
    }

    if ($giftcard['balance'] < $request->post['amount']) {
      throw new Exception($language->get('error_insufficient_balance'));
    }

    // Delete and update card balance (decreate card balance)
    $statement = $db->prepare("UPDATE `gift_cards` SET `balance` = `balance`-$topup_amount WHERE `card_no` = ?");
    $statement->execute(array($card_no));

    $statement = $db->prepare("DELETE FROM `gift_card_topups` WHERE `id` = ? LIMIT 1");
    $statement->execute(array($id));

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_topup_delete_success')));
    exit();

  } catch(Exception $e) { 
    
    $error_message = $e->getMessage();
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $error_message));
    exit();
  }
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Giftcard_Topup_List');

$where_query = "1=1";

if (from()) {
  $from = from();
  $to = to();
  $where_query .= date_range_giftcard_topup_filter($from, $to);
}

// DB table to use
$table = "(SELECT gift_card_topups.* FROM gift_card_topups 
        WHERE $where_query) as gift_card_topups";
 
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
  array( 
    'db' => 'date',   
    'dt' => 'date' ,
    'formatter' => function($d, $row) {
        return $row['date'];
    }
  ),
  array( 
    'db' => 'card_id',   
    'dt' => 'card_no' ,
    'formatter' => function($d, $row) {
        return $row['card_id'];
    }
  ),
  array( 
    'db' => 'amount',   
    'dt' => 'amount' ,
    'formatter' => function($d, $row) {
        return currency_format($row['amount']);
    }
  ),
  array( 
    'db' => 'created_by',   
    'dt' => 'created_by' ,
    'formatter' => function($d, $row) {
        return get_the_user($row['created_by'], 'username');
    }
  ),
  array(
    'db'        => 'id',
    'dt'        => 'btn_delete',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="delete-giftcard-topup" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  ),
); 

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Giftcard_Topup_List');

/**
 *===================
 * END DATATABLE
 *===================
 */