<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if your logged in or not
// if user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_supplier')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('supplier');

// LOAD SUPPLIER MODEL
$supplier_model = $registry->get('loader')->model('supplier');

// Validate post data
function validate_request_data($request, $language) 
{
  // Validate supplier name
  if(!validateString($request->post['sup_name'])) {
    throw new Exception($language->get('error_sup_name'));
  }

  // Validate supplier email or mobile
  if (!validateEmail($request->post['sup_email']) && empty($request->post['sup_mobile'])) {
    throw new Exception($language->get('error_supplier_email_or_mobile'));
  }

  // Validate suppleir address
  if(empty($request->post['sup_address'])) {
    throw new Exception($language->get('error_sup_address'));
  }

  if (get_preference('invoice_view') == 'indian_gst') {
    // Validate supplier state
    if (!validateString($request->post['sup_state'])) {
      throw new Exception($language->get('error_sup_state'));
    }
  }

  // Validate store
  if (!isset($request->post['supplier_store']) || empty($request->post['supplier_store'])) {
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

// Check, if already exist or not
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if supplier name exist or not
  if (!empty($request->post['sup_name'])) {
    $statement = $db->prepare("SELECT * FROM `suppliers` WHERE `sup_name` = ? AND `sup_id` != ?");
    $statement->execute(array($request->post['sup_name'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_supplier_name_exist'));
    }
  }

  // Check, if email address exist or not
  if (!empty($request->post['sup_email'])) {
    $statement = $db->prepare("SELECT * FROM `suppliers` WHERE `sup_email` = ? AND `sup_id` != ?");
    $statement->execute(array($request->post['sup_email'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_email_exist'));
    }
  }

  // Check, if mobile number exist or not
  if (!empty($request->post['sup_mobile'])) {
    $statement = $db->prepare("SELECT * FROM `suppliers` WHERE `sup_mobile` = ? AND `sup_id` != ?");
    $statement->execute(array($request->post['sup_mobile'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_mobile_exist'));
    }
  }
}

// Create supplier
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_supplier')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language);
    
    $statement = $db->prepare("SELECT * FROM `suppliers` WHERE `sup_name` = ?");
    $statement->execute(array($request->post['sup_name']));
    $total = $statement->rowCount();
    if ($total>0) {
      throw new Exception($language->get('error_supplier_exist'));
    }

    $Hooks->do_action('Before_Create_Supplier', $request);

    // insert supplier into database
    $supplier_id = $supplier_model->addSupplier($request->post);

    // get supplier info
    $supplier = $supplier_model->getSupplier($supplier_id);

    $Hooks->do_action('After_Create_Supplier', $supplier);

    // SET OUTPUT CONTENT TYPE
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $supplier_id, 'supplier' => $supplier));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();

  }
} 


// update supplier
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_supplier')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate product id
    if (empty($request->post['sup_id'])) {
      throw new Exception($language->get('error_sup_id'));
    }

    $id = $request->post['sup_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Supplier', $request);

    // edit supplier
    $supplier = $supplier_model->editSupplier($id, $request->post);

    $Hooks->do_action('After_Update_Supplier', $supplier);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_update_success'), 'id' => $id));
    exit();
    
  } catch(Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// delete supplier
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_supplier')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate supplier id
    if (empty($request->post['sup_id'])) {
      throw new Exception($language->get('error_supplier_id'));
    }

    $id = $request->post['sup_id'];
    $new_sup_id = $request->post['new_sup_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate delete action
    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' && empty($new_sup_id)) {
      throw new Exception($language->get('error_supplier_name'));
    }

    $Hooks->do_action('Before_Delete_Supplier', $request);

    $belongs_stores = $supplier_model->getBelongsStore($id);
    foreach ($belongs_stores as $the_store) {

      // Check if relationship exist or not
      $statement = $db->prepare("SELECT * FROM `supplier_to_store` WHERE `sup_id` = ? AND `store_id` = ?");
      $statement->execute(array($new_sup_id, $the_store['store_id']));
      if ($statement->rowCount() > 0) continue;

      // Create relationship
      $statement = $db->prepare("INSERT INTO `supplier_to_store` SET `sup_id` = ?, `store_id` = ?");
      $statement->execute(array($new_sup_id, $the_store['store_id']));
    }

    if ($request->post['delete_action'] == 'insert_to') {

      // update product supplier
      $statement = $db->prepare("UPDATE `product_to_store` SET `sup_id` = ? WHERE `sup_id` = ?");
      $statement->execute(array($new_sup_id, $id));

      // update supplier in buying invoice
      $statement = $db->prepare("UPDATE `buying_info` SET `sup_id` = ? WHERE `sup_id` = ?");
      $statement->execute(array($new_sup_id, $id));
    } 

    // delete supplier
    $supplier = $supplier_model->deleteSupplier($id);

    $Hooks->do_action('After_Delete_Supplier', $supplier);
    
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

// supplier create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  $Hooks->do_action('Before_Supplier_Create_Form');
  include 'template/supplier_create_form.php';
  $Hooks->do_action('After_Supplier_Create_Form');
  exit();
}

// supplier edit form
if (isset($request->get['sup_id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
    
  // fetch supplier info
  $supplier = $supplier_model->getSupplier($request->get['sup_id']);
  $Hooks->do_action('Before_Supplier_Edit_Form', $supplier);
  include 'template/supplier_form.php';
  $Hooks->do_action('After_Supplier_Edit_Form', $supplier);
  exit();
}

// supplier delete form
if (isset($request->get['sup_id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') {

  // fetch supplier info
  $supplier = $supplier_model->getSupplier($request->get['sup_id']);
  $Hooks->do_action('Before_Supplier_Delete_Form');
  include 'template/supplier_del_form.php';
  $Hooks->do_action('Before_Supplier_Delete_Form');
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */
$Hooks->do_action('Before_Showing_Supplier_List');

$where_query = 's2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT suppliers.*, s2s.status, s2s.sort_order FROM suppliers 
  LEFT JOIN supplier_to_store s2s ON (suppliers.sup_id = s2s.sup_id) 
  WHERE $where_query GROUP by suppliers.sup_id
  ) as suppliers";
 
// Table's primary key
$primaryKey = 'sup_id';

$columns = array(
  array(
      'db' => 'sup_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'sup_id', 'dt' => 'sup_id' ),
  array( 
    'db' => 'sup_name',   
    'dt' => 'sup_name' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['sup_name']);
    }
  ),
  array( 'db' => 'sup_mobile',   'dt' => 'sup_mobile' ),
  array( 
    'db' => 'sup_id',   
    'dt' => 'total_product' ,
    'formatter' => function($d, $row) use($supplier_model) {
        return total_product_of_supplier($row['sup_id']);
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
    'dt' => 'status',
    'formatter' => function($d, $row) use($language) {
      return $row['status'] 
        ? '<span class="label label-success">'.$language->get('text_active').'</span>' 
        : '<span class="label label-warning">' .$language->get('text_inactive').'</span>';
    }
  ),
  array( 
    'db' => 'sup_id',   
    'dt' => 'btn_buy' ,
    'formatter' => function($d, $row) use($language) {

        if (total_product_of_supplier($row['sup_id']) <= 0) {
          return '<button class="btn btn-sm btn-block btn-default" disabled><i class="fa fa-fw fa-shopping-cart"></i></button>';
        }
        
        return '<button id="buy-btn" class="btn btn-sm btn-block btn-success" title="'.$language->get('button_buy_product').'"><i class="fa fa-fw fa-shopping-cart"></i></button>';
    }
  ),
  array( 
    'db' => 'sup_id',   
    'dt' => 'btn_view' ,
    'formatter' => function($d, $row) use($language) {
        return '<a id="view-supplier" class="btn btn-sm btn-block btn-info" href="supplier_profile.php?sup_id='.$row['sup_id'].'" title="'.$language->get('button_view_profile').'"><i class="fa fa-fw fa-user"></i></a>';
    }
  ),
  array( 
    'db' => 'sup_id',   
    'dt' => 'btn_edit' ,
    'formatter' => function($d, $row) use($language) {
      if (DEMO && $row['sup_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
      }
      return '<button id="edit-supplier" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array( 
    'db' => 'sup_id',   
    'dt' => 'btn_delete' ,
    'formatter' => function($d, $row) use($language) {
      if (DEMO && $row['sup_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-trash"></i></button>';
      }
      return '<button id="delete-supplier" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  )
);
 
$where_query = '1=1';

// output for datatable
echo json_encode(
  SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns, null, $where_query)
);

$Hooks->do_action('After_Showing_Supplier_List');

/**
 *===================
 * END DATATABLE
 *===================
 */