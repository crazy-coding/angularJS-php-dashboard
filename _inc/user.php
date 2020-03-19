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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_user')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('user');

// LOAD USER MODEL
$user_model = $registry->get('loader')->model('user');

// Validate post data
function validate_request_data($request, $language) 
{
  // Validate username
  if (!validateString($request->post['username'])) {
    throw new Exception($language->get('error_user_name'));
  }

  // Validate customer email & mobile
  if (!validateEmail($request->post['email']) && empty($request->post['mobile'])) {
    throw new Exception($language->get('error_user_email_or_mobile'));
  }

  // Validate user group id
  if(!validateInteger($request->post['group_id'])) {
    throw new Exception($language->get('error_user_group'));
  } 

  if (!isset($request->post['user_store']) || empty($request->post['user_store'])) {
    throw new Exception($language->get('error_store'));
  }

  // Validate status
  if (!is_numeric($request->post['status'])) {
    throw new Exception($language->get('error_status'));
  }

  // Validate sort order
  if (!is_numeric($request->post['sort_order'])) {
    throw new Exception($language->get('error_sort_order'));
  }
}

// Check, if exist or not
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check email address
  if ($request->post['email']) {
    $statement = $db->prepare("SELECT * FROM `users` WHERE `email` = ? && `id` != ?");
    $statement->execute(array($request->post['email'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_email_exist'));
    }
  }

  // Check mobile number
  if ($request->post['mobile']) {
    $statement = $db->prepare("SELECT * FROM `users` WHERE `mobile` = ? && `id` != ?");
    $statement->execute(array($request->post['mobile'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_mobile_exist'));
    } 
  }
}

// Create user
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

     // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_user')) {
      throw new Exception($language->get('error_read_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

     // Validate existance
    validate_existance($request, $language);

    // Validate password
    if(!validateAlphanumeric($request->post['password'])) {
      throw new Exception($language->get('error_user_password'));
    }

    // password length check
    if(strlen($request->post['password']) < 6) {
      throw new Exception($language->get('error_user_password_length'));
    }

    // password matching
    if($request->post['password'] !== $request->post['password1']) {
      throw new Exception($language->get('error_user_password_match'));
    }

    $Hooks->do_action('Before_Create_User');

    // edit user
    $user_id = $user_model->addUser($request->post);

    // get user
    $the_user = $user_model->getUser($user_id);

    $Hooks->do_action('After_Create_User', $the_user);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $user_id, 'user' => $the_user));
    exit();

  }  catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// update user
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_user') || DEMO) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate user id
    if (empty($request->post['id'])) {
      throw new Exception($language->get('error_user_id'));
    }

    $id = $request->post['id'];

    if (DEMO && ($id == 1 || $id == 2 || $id == 3)) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    // for current user current store link can not remove
    if (user_id() == $id && !in_array(store_id(), $request->post['user_store'])) {
      throw new Exception($language->get('error_active_store_not_remove'));
    }

    $Hooks->do_action('Before_Update_User', $request);

    // edit esuer
    $the_user = $user_model->editUser($id, $request->post);

    $Hooks->do_action('After_Update_User', $the_user);

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

// delete user
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_user') || DEMO) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate user id
    if (!validateInteger($request->post['id'])) {
      throw new Exception($language->get('error_user_id'));
    }

    $id = $request->post['id'];

    if (DEMO && ($id == 1 || $id == 2 || $id == 3)) {
      throw new Exception($language->get('error_delete_permission'));
    }

    if ($id == 1) {
      throw new Exception($language->get('error_unable_to_delete'));
    }

    // Validate delete action
    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' && empty($request->post['new_user_id'])) {
      throw new Exception($language->get('error_user_name'));
    }

    $Hooks->do_action('Before_Delete_User', $request);

    if ($request->post['delete_action'] == 'soft_delete') {
      
      // update selling invoice created_by
      $statement = $db->prepare("UPDATE `selling_info` SET `created_by` = ? WHERE `created_by` = ?");
      $statement->execute(array($user->getId(), $id));

      // update buying invoice created_by
      $statement = $db->prepare("UPDATE `buying_info` SET `created_by` = ? WHERE `created_by` = ?");
      $statement->execute(array($user->getId(), $id));
    }

    if ($request->post['delete_action'] == 'insert_to') {
      
      // update selling invoice created_by
      $statement = $db->prepare("UPDATE `selling_info` SET `created_by` = ? WHERE `created_by` = ?");
      $statement->execute(array($request->post['new_user_id'], $id));

      // update buying invoice created_by
      $statement = $db->prepare("UPDATE `buying_info` SET `created_by` = ? WHERE `created_by` = ?");
      $statement->execute(array($request->post['new_user_id'], $id));
    }
    
    // delete user
    $the_user = $user_model->deleteUser($id);

    $Hooks->do_action('After_Delete_User', $the_user);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_delete_success')));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// user create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  include 'template/user_create_form.php';
  exit();
}

// user edit form
if (isset($request->get['id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
  
  // fetch user
  $the_user = $user_model->getUser($request->get['id']);
  include 'template/user_form.php';
  exit();
}

// user delete form
if (isset($request->get['id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') {
  
  // fetch user
  $the_user = $user_model->getUser($request->get['id']);
  $Hooks->do_action('Before_User_Delete_Form', $the_user);
  include 'template/user_del_form.php';
  $Hooks->do_action('After_User_Delete_Form', $the_user);
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_User_List');
 
// DB table to use
$where_query = 'u2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT users.*, u2s.status, u2s.sort_order FROM users 
  LEFT JOIN user_to_store u2s ON (users.id = u2s.user_id) 
  WHERE $where_query GROUP by users.id
  ) as users";
 
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
    'db' => 'username',   
    'dt' => 'username' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['username']);
    }
  ),
  array( 'db' => 'email',  'dt' => 'email' ),
  array( 'db' => 'mobile',   'dt' => 'mobile' ),
  array( 'db' => 'group_id',   'dt' => 'group' ),
  array( 
    'db' => 'group_id',   
    'dt' => 'group' ,
    'formatter' => function($d, $row) use($db) {
        $statement = $db->prepare('SELECT name FROM `user_group` WHERE group_id = ?');
        $statement->execute(array($row['group_id']));
        $group = $statement->fetch(PDO::FETCH_ASSOC);
        return ucfirst($group['name']);
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
    'db' => 'status',   
    'dt' => 'status' ,
    'formatter' => function($d, $row) use($db, $language) {
        return $row['status'] 
          ? '<span class="label label-success">'.$language->get('text_active').'</span>' 
          : '<span class="label label-warning">' .$language->get('text_inactive').'</span>';
    }
  ),
  array( 
    'db' => 'id',   
    'dt' => 'btn_edit' ,
    'formatter' => function($d, $row) use($user, $language) {
      if (DEMO && ($row['id'] == 2 || $row['id'] == 3 || $row['id'] == 1)) {
        return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-pencil"></i></button>';
      } 
      return '<button id="edit-user" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array( 
    'db' => 'id',   
    'dt' => 'btn_delete' ,
    'formatter' => function($d, $row) use($user, $language) {
        if ((DEMO && ($row['id'] == 2 || $row['id'] == 3)) || $row['id'] == 1 || $row['id'] == $user->getId()) {
          return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash"></i></button>';
        } 
        return '<button id="delete-user" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  )
); 
 
echo json_encode(
    SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_User_List');

/**
 *===================
 * END DATATABLE
 *===================
 */
