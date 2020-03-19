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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_taxrate')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('taxrate');

// LOAD BOX MODEL
$taxrate_model = $registry->get('loader')->model('taxrate');

// Validate post data
function validate_request_data($request, $language) {

  // Taxrate name validation
  if (!validateString($request->post['taxrate_name'])) {
      throw new Exception($language->get('error_taxrate_name'));
  }

  // Taxrate code validation
  if (!validateString($request->post['taxrate_code'])) {
      throw new Exception($language->get('error_taxrate_code'));
  }

  // Taxrate validation
  if (!is_numeric($request->post['taxrate'])) {
      throw new Exception($language->get('error_taxrate'));
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

// Check taxrate existance by id
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if taxrate name exist or not
  $statement = $db->prepare("SELECT * FROM `taxrates` WHERE `taxrate_name` = ? AND `taxrate_id` != ?");
  $statement->execute(array($request->post['taxrate_name'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_taxrate_name_exist'));
  }

  // Check, if taxrate code exist or not
  $statement = $db->prepare("SELECT * FROM `taxrates` WHERE `taxrate_code` = ? AND `taxrate_id` != ?");
  $statement->execute(array($request->post['taxrate_code'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_taxrate_code_exist'));
  }
}

// Create taxrate
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_taxrate')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Taxrate');

    // Add taxrate
    $taxrate_id = $taxrate_model->addTaxrate($request->post);

    // Fetch the taxrate info
    $taxrate = $taxrate_model->getTaxrate($taxrate_id);

    $Hooks->do_action('After_Create_Taxrate', $taxrate);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $taxrate_id, 'taxrate' => $taxrate));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Update taxrate
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_taxrate')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate taxrate id
    if (empty($request->post['taxrate_id'])) {
      throw new Exception($language->get('error_taxrate_id'));
    }

    $id = $request->post['taxrate_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Taxrate', $request);
    
    // edit taxrate
    $taxrate = $taxrate_model->editTaxrate($id, $request->post);

    $Hooks->do_action('After_Update_Taxrate', $taxrate);
    
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

// Delete taxrate
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') {
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_taxrate')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate taxrate id
    if (empty($request->post['taxrate_id'])) {
      throw new Exception($language->get('error_taxrate_id'));
    }

    $id = $request->post['taxrate_id'];
    $new_taxrate_id = $request->post['new_taxrate_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate delete action
    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' && empty($new_taxrate_id)) {
      throw new Exception($language->get('error_delete_taxrate_name'));
    }

    $Hooks->do_action('Before_Delete_Taxrate', $request);

    // Update taxrate id for product
    $statement = $db->prepare("UPDATE `product_to_store` SET `taxrate_id` = ? WHERE `taxrate_id` = ?");
    $statement->execute(array($new_taxrate_id, $id));

    // Delete the taxrate
    $taxrate = $taxrate_model->deleteTaxrate($id);

    $Hooks->do_action('After_Delete_Taxrate', $taxrate);
    
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

// Taxrate create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  include 'template/taxrate_create_form.php';
  exit();
}

// Taxrate edit form
if (isset($request->get['taxrate_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') 
{
  // Fetch taxrate info
  $taxrate = $taxrate_model->getTaxrate($request->get['taxrate_id']);
  include 'template/taxrate_edit_form.php';
  exit();
}


// Taxrate delete form
if (isset($request->get['taxrate_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') 
{
  // Fetch taxrate info
  $taxrate = $taxrate_model->getTaxrate($request->get['taxrate_id']);
  $Hooks->do_action('Before_Taxrate_Delete_Form', $taxrate);
  include 'template/taxrate_del_form.php';
  $Hooks->do_action('After_Taxrate_Delete_Form', $taxrate);
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Taxrate_List');
 
// DB table to use
$table = "taxrates";
 
// Table's primary key
$primaryKey = 'taxrate_id';
$columns = array(
  array(
      'db' => 'taxrate_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'taxrate_id', 'dt' => 'taxrate_id' ),
  array( 
    'db' => 'taxrate_name',   
    'dt' => 'taxrate_name' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['taxrate_name']);
    }
  ),
  array( 
    'db' => 'taxrate_code',   
    'dt' => 'taxrate_code' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['taxrate_code']);
    }
  ),
  array( 'db' => 'taxrate',  'dt' => 'taxrate' ),
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
    'db'        => 'taxrate_id',
    'dt'        => 'btn_edit',
    'formatter' => function( $d, $row ) use($language) {
      if (DEMO && $row['taxrate_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
      }
      return '<button id="edit-taxrate" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array(
    'db'        => 'taxrate_id',
    'dt'        => 'btn_delete',
    'formatter' => function( $d, $row ) use($language) {
      if ($row['taxrate_id'] == 1) {
        return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash"></i></button>';
      }
      return '<button id="delete-taxrate" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  )
); 

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Taxrate_List');

/**
 *===================
 * END DATATABLE
 *===================
 */