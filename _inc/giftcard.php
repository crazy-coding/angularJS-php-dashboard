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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_giftcard')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('giftcard');

// LOAD GIFTCARD MODEL
$giftcard_model = $registry->get('loader')->model('giftcard');

// Validate post data
function validate_request_data($request, $language) {

  // Giftcard name validation
  if (empty($request->post['card_no'])) {
      throw new Exception($language->get('error_card_no'));
  }

  // Giftcard value
  if (!isset($request->post['giftcard_value'])) {
      throw new Exception($language->get('error_giftcard_value'));
  }

  // Validate expiry date
  if (!isItValidDate($request->post['expiry'])) {
    throw new Exception($language->get('error_expiry_date'));
  }

  // Validate expiry date
  if (!validateExpireDate($request->post['expiry'])) {
    throw new Exception($language->get('error_expiry_date'));
  }
}

// Check giftcard existance by id
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if giftcard name exist or not
  $statement = $db->prepare("SELECT * FROM `gift_cards` WHERE `card_no` = ? AND `id` != ?");
  $statement->execute(array($request->post['card_no'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_card_no_exist'));
  }
}

// Create giftcard
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'add_giftcard')) {
      throw new Exception($language->get('error_add_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Giftcard code validation
    if (!validateInteger($request->post['customer_id'])) {
        throw new Exception($language->get('error_customer'));
    }

    $statement = $db->prepare("SELECT * FROM `gift_cards` WHERE `customer_id` = ?");
    $statement->execute(array($request->post['customer_id']));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_customer_gift_card_exist'));
    }

    // Giftcard balance
    if (!is_numeric($request->post['balance'])) {
        throw new Exception($language->get('error_balance'));
    }

    // Giftcard balance
    if ($request->post['balance'] < 0) {
        throw new Exception($language->get('error_balance'));
    }

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Giftcard');

    // Add giftcard
    $id = $giftcard_model->addGiftcard($request->post);

    // Fetch the giftcard info
    $giftcard = $giftcard_model->getGiftcard($id);

    $Hooks->do_action('After_Create_Giftcard', $giftcard);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $id, 'giftcard' => $giftcard));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Update giftcard
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_giftcard')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate giftcard id
    if (empty($request->post['id'])) {
      throw new Exception($language->get('error_id'));
    }

    $id = $request->post['id'];

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Giftcard', $request);
    
    // edit giftcard
    $giftcard = $giftcard_model->editGiftcard($id, $request->post);

    $Hooks->do_action('After_Update_Giftcard', $giftcard);
    
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_update_success'), 'id' => $id));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// Delete giftcard
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') {
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_giftcard')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate giftcard id
    if (empty($request->post['id'])) {
      throw new Exception($language->get('error_id'));
    }

    $id = $request->post['id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    $Hooks->do_action('Before_Delete_Giftcard', $request);

    // Delete the giftcard
    $giftcard = $giftcard_model->deleteGiftcard($id);

    $Hooks->do_action('After_Delete_Giftcard', $giftcard);
    
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_delete_success')));
    exit();

  } catch(Exception $e) { 
    
    $error_message = $e->getMessage();
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $error_message));
    exit();
  }
}

// Create giftcard
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'TOPUP')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'giftcard_topup')) {
      throw new Exception($language->get('error_topup_permission'));
    }

    $giftcard = $giftcard_model->getGiftcard($request->post['id']);
    if (!$giftcard) {
      throw new Exception($language->get('error_giftcard_not_found'));
    }

    // Topup amount validation
    if (!validateFloat($request->post['amount'])) {
        throw new Exception($language->get('error_amount'));
    }

    if ($request->post['amount'] <= 0) {
      throw new Exception($language->get('error_amount'));
    }

    // Validate expiry date
    if (!isItValidDate($request->post['expiry'])) {
      throw new Exception($language->get('error_expiry_date'));
    }

    // Validate expiry date
    if (!validateExpireDate($request->post['expiry'])) {
      throw new Exception($language->get('error_expiry_date'));
    }

    // Add giftcard
    $id = $giftcard_model->topupGiftcard($giftcard['card_no'], $request->post['amount'], $request->post['expiry']);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_topup_success'), 'id' => $id));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Giftcard topup
if (isset($request->get['id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'TOPUP') 
{
  // Fetch giftcard info
  $giftcard = $giftcard_model->getGiftcard($request->get['id']);
  include 'template/giftcard_topup_form.php';
  exit();
}

// Giftcard create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  include 'template/giftcard_create_form.php';
  exit();
}

// Giftcard view
if (isset($request->get['card_no']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'VIEW') 
{
  // Fetch giftcard info
  $giftcard = $giftcard_model->getGiftcard($request->get['card_no']);
  include 'template/giftcard_view.php';
  exit();
}

// Giftcard edit form
if (isset($request->get['id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') 
{
  // Fetch giftcard info
  $giftcard = $giftcard_model->getGiftcard($request->get['id']);
  include 'template/giftcard_edit_form.php';
  exit();
}


// Giftcard delete form
if (isset($request->get['id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') 
{
  // Fetch giftcard info
  $giftcard = $giftcard_model->getGiftcard($request->get['id']);
  $Hooks->do_action('Before_Giftcard_Delete_Form', $giftcard);
  include 'template/giftcard_del_form.php';
  $Hooks->do_action('After_Giftcard_Delete_Form', $giftcard);
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Giftcard_List');
 
// DB table to use
$table = "gift_cards";
 
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
    'db' => 'card_no',   
    'dt' => 'card_no' ,
    'formatter' => function($d, $row) {
        return $row['card_no'];
    }
  ),
  array( 
    'db' => 'value',   
    'dt' => 'value' ,
    'formatter' => function($d, $row) {
        return currency_format($row['value']);
    }
  ),
  array( 
    'db' => 'balance',   
    'dt' => 'balance' ,
    'formatter' => function($d, $row) {
        return currency_format($row['balance']);
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
    'db' => 'customer_id',   
    'dt' => 'customer' ,
    'formatter' => function($d, $row) {
        return get_the_customer($row['customer_id'], 'customer_name');
    }
  ),
  array( 
    'db' => 'expiry',   
    'dt' => 'expiry' ,
    'formatter' => function($d, $row) {
        return $row['expiry'];
    }
  ),
  array(
    'db'        => 'id',
    'dt'        => 'btn_view',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="view-giftcard" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-eye"></i></button>';
    }
  ),
  array(
    'db'        => 'id',
    'dt'        => 'btn_topup',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="topup-giftcard" class="btn btn-sm btn-block btn-info" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-money"></i></button>';
    }
  ),
  array(
    'db'        => 'id',
    'dt'        => 'btn_edit',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="edit-giftcard" class="btn btn-sm btn-block btn-warning" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array(
    'db'        => 'id',
    'dt'        => 'btn_delete',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="delete-giftcard" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  )
); 

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Giftcard_List');

/**
 *===================
 * END DATATABLE
 *===================
 */