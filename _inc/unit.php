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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_unit')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('unit');

// LOAD BOX MODEL
$unit_model = $registry->get('loader')->model('unit');

// Validate post data
function validate_request_data($request, $language) {

  // Unit name validation
  if (!validateString($request->post['unit_name'])) {
      throw new Exception($language->get('error_unit_name'));
  }

  // store validation
  if (!isset($request->post['unit_store']) || empty($request->post['unit_store'])) {
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

// Check unit existance by id
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if unit name exist or not
  $statement = $db->prepare("SELECT * FROM `units` WHERE `unit_name` = ? AND `unit_id` != ?");
  $statement->execute(array($request->post['unit_name'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_unit_name_exist'));
  }
}

// Create unit
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_unit')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Unit');

    // Add unit
    $unit_id = $unit_model->addUnit($request->post);

    // Fetch the unit info
    $unit = $unit_model->getUnit($unit_id);

    $Hooks->do_action('After_Create_Unit', $unit);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $unit_id, 'unit' => $unit));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Update unit
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_unit')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate unit id
    if (empty($request->post['unit_id'])) {
      throw new Exception($language->get('error_unit_id'));
    }

    $id = $request->post['unit_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Unit', $request);
    
    // edit unit
    $unit = $unit_model->editUnit($id, $request->post);

    $Hooks->do_action('After_Update_Unit', $unit);
    
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

// Delete unit
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') {
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_unit')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate unit id
    if (empty($request->post['unit_id'])) {
      throw new Exception($language->get('error_unit_id'));
    }

    $id = $request->post['unit_id'];
    $new_unit_id = $request->post['new_unit_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate delete action
    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' && empty($new_unit_id)) {
      throw new Exception($language->get('error_delete_unit_name'));
    }

    $Hooks->do_action('Before_Delete_Unit', $request);

    $belongs_stores = $unit_model->getBelongsStore($id);
    foreach ($belongs_stores as $the_store) {

      // Check if relationship exist or not
      $statement = $db->prepare("SELECT * FROM `unit_to_store` WHERE `uunit_id` = ? AND `store_id` = ?");
      $statement->execute(array($new_unit_id, $the_store['store_id']));
      if ($statement->rowCount() > 0) continue;

      // Create relationship
      $statement = $db->prepare("INSERT INTO `unit_to_store` SET `uunit_id` = ?, `store_id` = ?");
      $statement->execute(array($new_unit_id, $the_store['store_id']));
    }

    // Delete the unit
    $unit = $unit_model->deleteUnit($id);

    $Hooks->do_action('After_Delete_Unit', $unit);
    
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

// Unit create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  include 'template/unit_create_form.php';
  exit();
}

// Unit edit form
if (isset($request->get['unit_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') 
{
  // Fetch unit info
  $unit = $unit_model->getUnit($request->get['unit_id']);
  include 'template/unit_edit_form.php';
  exit();
}


// Unit delete form
if (isset($request->get['unit_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') 
{
  // Fetch unit info
  $unit = $unit_model->getUnit($request->get['unit_id']);
  $Hooks->do_action('Before_Unit_Delete_Form', $unit);
  include 'template/unit_del_form.php';
  $Hooks->do_action('After_Unit_Delete_Form', $unit);
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Unit_List');

$where_query = 'unit2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT units.*, unit2s.status, unit2s.sort_order FROM units 
  LEFT JOIN unit_to_store unit2s ON (units.unit_id = unit2s.uunit_id) 
  WHERE $where_query GROUP by units.unit_id
  ) as units";
 
// Table's primary key
$primaryKey = 'unit_id';
$columns = array(
  array(
      'db' => 'unit_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'unit_id', 'dt' => 'unit_id' ),
  array( 
    'db' => 'unit_name',   
    'dt' => 'unit_name' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['unit_name']);
    }
  ),
  array( 
    'db' => 'unit_id',   
    'dt' => 'total_product' ,
    'formatter' => function($d, $row) use($unit_model) {
        return $unit_model->totalProduct($row['unit_id']);
    }
  ),
  array( 'db' => 'unit_details',  'dt' => 'unit_details' ),
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
    'db'        => 'unit_id',
    'dt'        => 'btn_edit',
    'formatter' => function( $d, $row ) use($language) {
      if (DEMO && $row['unit_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
      }
      return '<button id="edit-unit" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array(
    'db'        => 'unit_id',
    'dt'        => 'btn_delete',
    'formatter' => function( $d, $row ) use($language) {
      if ($row['unit_id'] == 1) {
        return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash"></i></button>';
      }
      return '<button id="delete-unit" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
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

$Hooks->do_action('After_Showing_Unit_List');

/**
 *===================
 * END DATATABLE
 *===================
 */