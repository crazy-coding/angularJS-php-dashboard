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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_pmethod')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('pmethod');

// LOAD PAYMENTMETHOD MODAL
$pmethod_model = $registry->get('loader')->model('pmethod');

// Validate post data
function validate_request_data($request, $language) 
{
  // Validate name
  if (!validateString($request->post['pmethod_name'])) {
    throw new Exception($language->get('error_pmethod_name'));
  }

  // Validate store
  if (!isset($request->post['pmethod_store']) || empty($request->post['pmethod_store'])) {
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

// Check, if pmethod exist or not
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if pmethod is exist or not
  $statement = $db->prepare("SELECT * FROM `pmethods` WHERE `name` = ? AND `pmethod_id` != ?");
  $statement->execute(array($request->post['pmethod_name'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_pmethod_exist'));
  }
}

// Create pmethod
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Create permission check
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_pmethod')) {
      throw new Exception($language->get('error_read_permission'));
    }
    
    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_PMethod');

    // Insert new pmethod into database
    $pmethod_id = $pmethod_model->addPmethod($request->post);

    // get pmethod
    $pmethod = $pmethod_model->getPmethod($pmethod_id);

    $Hooks->do_action('After_Create_PMethod', $pmethod);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $pmethod_id, 'pmethod' => $pmethod));
    exit();
  }
  catch(Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// update pmethod
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check delete permision
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_pmethod')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate product id
    if (empty($request->post['pmethod_id'])) {
      throw new Exception($language->get('error_pmethod_id'));
    }

    $id = $request->post['pmethod_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_PMethod', $request);
    
    // edit pmethod
    $pmethod = $pmethod_model->editPmethod($id, $request->post);

    $Hooks->do_action('After_Update_PMethod', $pmethod);

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


// delete pmethod
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_pmethod')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate pmethod id
    if (!validateInteger($request->post['pmethod_id'])) {
      throw new Exception($language->get('error_pmethod_id'));
    }

    $id = $request->post['pmethod_id'];
    $new_pmethod_id = $request->post['new_pmethod_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    if ($request->post['delete_action'] == 'insert_to' && !validateInteger($new_pmethod_id)) {
      throw new Exception($language->get('error_pmethod_name'));
    }

    $Hooks->do_action('Before_Delete_PMethod', $request);

    $belongs_stores = $pmethod_model->getBelongsStore($id);
    foreach ($belongs_stores as $the_store) {

      // Check if relationship exist or not
      $statement = $db->prepare("SELECT * FROM `pmethod_to_store` WHERE `ppmethod_id` = ? AND `store_id` = ?");
      $statement->execute(array($new_pmethod_id, $the_store['store_id']));
      if ($statement->rowCount() > 0) continue;

      // Create relationship
      $statement = $db->prepare("INSERT INTO `pmethod_to_store` SET `ppmethod_id` = ?, `store_id` = ?");
      $statement->execute(array($new_pmethod_id, $the_store['store_id']));
    }

    if ($request->post['delete_action'] == 'insert_to') {
      // update invoice pmethod
      $statement = $db->prepare("UPDATE `payments` SET `pmethod_id` = ? WHERE `pmethod_id` = ?");
      $statement->execute(array($new_pmethod_id, $id));
    }

    // delete pmethod
    $pmethod = $pmethod_model->deletePmethod($id);

    $Hooks->do_action('Before_Delete_PMethod', $pmethod);
    
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

// pmethod edit form
if (isset($request->get['pmethod_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
    // fetch pmethod
    $pmethod = $pmethod_model->getPmethod($request->get['pmethod_id']);
    include 'template/pmethod_form.php';
    exit();
}

// pmethod delete form
if (isset($request->get['pmethod_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') {
    // fetch pmethod
    $pmethod = $pmethod_model->getPmethod($request->get['pmethod_id']);
    $Hooks->do_action('Before_PMethod_Delete_Form', $pmethod);
    include 'template/pmethod_del_form.php';
    $Hooks->do_action('After_PMethod_Delete_Form', $pmethod);
    exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_PMethod_List');

$where_query = 'pm2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT pmethods.*, pm2s.status, pm2s.sort_order FROM pmethods 
  LEFT JOIN pmethod_to_store pm2s ON (pmethods.pmethod_id = pm2s.ppmethod_id) 
  WHERE $where_query GROUP by pmethods.pmethod_id
  ) as pmethods";
 
// Table's primary key
$primaryKey = 'pmethod_id';
 
$columns = array(
  array(
      'db' => 'pmethod_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'pmethod_id', 'dt' => 'pmethod_id' ),
  array( 
    'db' => 'name',   
    'dt' => 'name' ,
    'formatter' => function($d, $row) {
        return $row['name'];
    }
  ),
  array( 'db' => 'sort_order', 'dt' => 'sort_order' ),
  array( 'db' => 'details', 'dt' => 'details' ),
  array( 
    'db' => 'status',   
    'dt' => 'status' ,
    'formatter' => function($d, $row) use($language) {
        return $row['status'] == 1 ? '<span class="label label-info">'.$language->get('text_active').'</span>' : '<span class="label label-warning">In-Active</span>';
    }
  ),
  array( 
    'db' => 'status',   
    'dt' => 'btn_edit' ,
    'formatter' => function($d, $row) use($language) {
      if (DEMO && $row['pmethod_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
      }
      return '<button id="edit-pmethod" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array( 
    'db' => 'status',   
    'dt' => 'btn_delete' ,
    'formatter' => function($d, $row) use($language) {
      if (DEMO && $row['pmethod_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-trash"></i></button>';
      }
      return '<button id="delete-pmethod" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  )
);

// output for datatable
echo json_encode(
  SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_PMethod_List');

/**
 *===================
 * END DATATABLE
 *===================
 */