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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_box')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('box');

// LOAD BOX MODEL
$box_model = $registry->get('loader')->model('box');

// Validate post data
function validate_request_data($request, $language) {

  // box name validation
  if (!validateString($request->post['box_name'])) {
      throw new Exception($language->get('error_box_name'));
  }

  // store validation
  if (!isset($request->post['box_store']) || empty($request->post['box_store'])) {
    throw new Exception($language->get('error_store'));
  }

  // status validation
  if (!is_numeric($request->post['status'])) {
    throw new Exception($language->get('error_status'));
  }

  // sort order validation
  if (!is_numeric($request->post['sort_order'])) {
    throw new Exception($language->get('error_sort_order'));
  }
}

// Check box existance by id
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if box name exist or not
  $statement = $db->prepare("SELECT * FROM `boxes` WHERE `box_name` = ? AND `box_id` != ?");
  $statement->execute(array($request->post['box_name'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_box_name_exist'));
  }
}

// Create box
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_box')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Box');

    // add box
    $box_id = $box_model->addBox($request->post);

    // fetch the box info
    $box = $box_model->getBox($box_id);

    $Hooks->do_action('After_Create_Box', $box);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $box_id, 'box' => $box));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// update box
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_box')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate box id
    if (empty($request->post['box_id'])) {
      throw new Exception($language->get('error_box_id'));
    }

    $id = $request->post['box_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Box', $request);
    
    // edit box
    $box = $box_model->editBox($id, $request->post);

    $Hooks->do_action('After_Update_Box', $box);
    
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

// delete box
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') {
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_box')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate box id
    if (empty($request->post['box_id'])) {
      throw new Exception($language->get('error_box_id'));
    }

    $id = $request->post['box_id'];
    $new_box_id = $request->post['new_box_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate delete action
    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' && empty($new_box_id)) {
      throw new Exception($language->get('error_delete_box_name'));
    }

    $Hooks->do_action('Before_Delete_Box', $request);

    $belongs_stores = $box_model->getBelongsStore($id);
    foreach ($belongs_stores as $the_store) {

      // Check if relationship exist or not
      $statement = $db->prepare("SELECT * FROM `box_to_store` WHERE `box_id` = ? AND `store_id` = ?");
      $statement->execute(array($new_box_id, $the_store['store_id']));
      if ($statement->rowCount() > 0) continue;

      // Create relationship
      $statement = $db->prepare("INSERT INTO `box_to_store` SET `box_id` = ?, `store_id` = ?");
      $statement->execute(array($new_box_id, $the_store['store_id']));
    }

    // update box id for product
    $statement = $db->prepare("UPDATE `product_to_store` SET `box_id` = ? WHERE `box_id` = ?");
    $statement->execute(array($new_box_id, $id));

    // delete the box
    $box = $box_model->deleteBox($id);

    $Hooks->do_action('After_Delete_Box', $box);
    
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

// box create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  include 'template/box_create_form.php';
  exit();
}

// box edit form
if (isset($request->get['box_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') 
{
  // fetch box info
  $box = $box_model->getBox($request->get['box_id']);
  include 'template/box_form.php';
  exit();
}


// box delete form
if (isset($request->get['box_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') 
{
  // fetch box info
  $box = $box_model->getBox($request->get['box_id']);
  $Hooks->do_action('Before_Box_Delete_Form', $box);
  include 'template/box_del_form.php';
  $Hooks->do_action('After_Box_Delete_Form', $box);
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Box_List');

$where_query = 'b2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT boxes.*, b2s.status, b2s.sort_order FROM boxes 
  LEFT JOIN box_to_store b2s ON (boxes.box_id = b2s.box_id) 
  WHERE $where_query GROUP by boxes.box_id
  ) as boxes";
 
// Table's primary key
$primaryKey = 'box_id';
$columns = array(
  array(
      'db' => 'box_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'box_id', 'dt' => 'box_id' ),
  array( 
    'db' => 'box_name',   
    'dt' => 'box_name' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['box_name']);
    }
  ),
  array( 
    'db' => 'box_id',   
    'dt' => 'total_product' ,
    'formatter' => function($d, $row) use($box_model) {
        return $box_model->totalProduct($row['box_id']);
    }
  ),
  array( 'db' => 'box_details',  'dt' => 'box_details' ),
  array( 
    'db' => 'status',   
    'dt' => 'status',
    'formatter' => function($d, $row) use($language) {
      return $row['status'] 
        ? '<span class="label label-success">'.$language->get('text_active').'</span>' 
        : '<span class="label label-warning">' .$language->get('text_inactive').'</span>';
    }
  ),
  array(
    'db'        => 'box_id',
    'dt'        => 'btn_edit',
    'formatter' => function( $d, $row ) use($language) {
      if (DEMO && $row['box_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
      }
      return '<button id="edit-box" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array(
    'db'        => 'box_id',
    'dt'        => 'btn_delete',
    'formatter' => function( $d, $row ) use($language) {
      if ($row['box_id'] == 1) {
        return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash"></i></button>';
      }
      return '<button id="delete-box" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  )
); 

$where_query = '1=1';
if (isset($request->get['p_day']) && $p_day = (int)$request->get['p_day']) {
  $where_query .= ' AND DAY(inv_date) = ' . $p_day;
}
if (isset($request->get['p_month']) && $p_month = (int)$request->get['p_month']) {
  $where_query .= ' AND MONTH(inv_date) = ' . $p_month;
}
if (isset($request->get['p_year']) && $p_year = (int)$request->get['p_year']) {
  $where_query .= ' AND YEAR(inv_date) = ' . $p_year;
}

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns, null, $where_query)
);

$Hooks->do_action('After_Showing_Box_List');

/**
 *===================
 * END DATATABLE
 *===================
 */