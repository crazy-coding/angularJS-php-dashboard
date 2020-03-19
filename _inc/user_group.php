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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_usergroup')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('usergroup');

// LOAD USERGROUP MODEL 
$usergroup_model = $registry->get('loader')->model('usergroup');

// Validate post data
function validate_request_data($request, $language) 
{  
  if (!validateString($request->post['name'])) {
    throw new Exception($language->get('error_user_group_name'));
  }
}

// Validate, if exist or not
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check Email address
  $statement = $db->prepare("SELECT * FROM `user_group` WHERE `name` = ? AND `group_id` != ?");
  $statement->execute(array($request->post['name'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_group_exist'));
  }
}

// Create usergroup
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_usergroup')) {
      throw new Exception($language->get('error_read_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Usergroup');

    // fetch usergroup
    $usergroup_id = $usergroup_model->addUsergroup($request->post);

    // get usergroup
    $usergroup = $usergroup_model->getUsergroup($usergroup_id);

    $Hooks->do_action('After_Create_Usergroup', $usergroup);
    
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $usergroup_id, 'usergroup' => $usergroup));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// update usergroup
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_usergroup') || DEMO) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate product id
    if (empty($request->post['group_id'])) {
      throw new Exception($language->get('error_user_group_id'));
    }

    $id = $request->post['group_id'];

    if (DEMO && ($id == 1 || $id == 2 || $id == 3)) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Usergroup', $request);

    $permission = array();
    if (isset($request->post['access']) && $request->post['access']) {
      $permission['access'] = $request->post['access'];
    }
    if (isset($request->post['modify']) && $request->post['modify']) {
      $permission['modify'] = $request->post['modify'];
    }

    $permission = array();
    if (isset($request->post['access']) && $request->post['access']) {
      $permission['access'] = $request->post['access'];
    }
    if (isset($request->post['modify']) && $request->post['modify']) {
      $permission['modify'] = $request->post['modify'];
    }

    // update usergroup
    $usergroup = $usergroup_model->editUsergroup($id, $request->post, $permission);

    $Hooks->do_action('After_Update_Usergroup', $usergroup);
    
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_update_success'), 'id' => $id));
    exit();

  } catch (Exception $e) { 

    $error_message = $e->getMessage();
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $error_message));
   exit();
  }
} 

// delete usergroup
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_usergroup') || DEMO) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate group id
    if (empty($request->post['group_id'])) {
      throw new Exception($language->get('error_usergroup_id'));
    }

    $id = $request->post['group_id'];

    if (DEMO && ($id == 1 || $id == 2 || $id == 3)) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // own group can not be deleted
    if ($user->getGroupId() == $id) {
      throw new Exception($language->get('error_won_group_delete'));
    }

    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' && empty($request->post['new_group_id'])) {
      throw new Exception($language->get('error_group_name'));
    }

    $Hooks->do_action('Before_Delete_Usergroup', $request);

    if ($request->post['delete_action'] == 'insert_to') {
      // update selling invoice creator
      $statement = $db->prepare("UPDATE `users` SET `group_id` = ? WHERE `group_id` = ?");
      $statement->execute(array($request->post['new_group_id'], $id));
    }

    if ($request->post['delete_action'] == 'delete_all') {
      // get all users belongs to the group
      $statement = $db->prepare("SELECT * FROM `users` WHERE `group_id` = ?");
      $statement->execute(array($id));
      $users = $statement->fetchAll(PDO::FETCH_ASSOC);
      foreach ($users as $the_user) {
        // update selling invoice creator
        $statement = $db->prepare("UPDATE `selling_info` SET `created_by` = ? WHERE `created_by` = ?");
        $statement->execute(array($user->getId(), $the_user['id']));

        // update buying invoice creator
        $statement = $db->prepare("UPDATE `buying_info` SET `creator` = ? WHERE `creator` = ?");
        $statement->execute(array($user->getId(), $the_user['id']));

        // delete all users of the group
        $statement = $db->prepare("DELETE FROM `users` WHERE `id` = ? LIMIT 1");
        $statement->execute(array($the_user['id']));
      }
    }

    // delete usergrouup
    $usergroup = $usergroup_model->deleteUsergroup($id);

    $Hooks->do_action('After_Delete_Usergroup', $usergroup);
    
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

// usergroup create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  include 'template/user_group_create_form.php';
  exit();
}

// usergroup edit form
if (isset($request->get['group_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
  
  // fetch usergroup
  $usergroup = $usergroup_model->getUsergroup($request->get['group_id']);
  include 'template/user_group_form.php';
  exit();

}

// usergroup delete form
if (isset($request->get['group_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') {
  
  // fetch usergroup
  $usergroup = $usergroup_model->getUsergroup($request->get['group_id']);
  $Hooks->do_action('Before_Usergroup_Delete_Form', $usergroup);
  include 'template/user_group_del_form.php';
  $Hooks->do_action('After_Usergroup_Delete_Form', $usergroup);
  exit();

}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Usergroup_List');
 
// DB table to use
$table = 'user_group';
 
// Table's primary key
$primaryKey = 'group_id';
 
$columns = array(
  array(
      'db' => 'group_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'group_id', 'dt' => 'group_id' ),
  array( 
    'db' => 'name',   
    'dt' => 'name' ,
    'formatter' => function($d, $row) {
        return $row['name'];
    }
  ),
  array( 
    'db' => 'group_id',   
    'dt' => 'total_user' ,
    'formatter' => function($d, $row) {
        return get_usergroup_user_count($row['group_id']);
    }
  ),
  array(
      'db' => 'group_id',
      'dt' => 'btn_edit',
      'formatter' => function( $d, $row ) use($language) {
        if (DEMO && ($row['group_id'] == 2 || $row['group_id'] == 3 ||$row['group_id'] == 1)) {          
          return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-pencil"></i></button>';
        }
        return '<button id="edit-user-group" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
      }
  ),
  array(
      'db' => 'group_id',
      'dt' => 'btn_delete',
      'formatter' => function( $d, $row ) use($language, $user) {
        if ((DEMO && ($row['group_id'] == 2 || $row['group_id'] == 3)) || $row['group_id'] == 1 || $user->getGroupId() == $row['group_id']) {          
          return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash"></i></button>';
        }
        return '<button id="delete-user-group" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
      }
  )
);
 
echo json_encode(
    SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Usergroup_List');

/**
 *===================
 * END DATATABLE
 *===================
 */