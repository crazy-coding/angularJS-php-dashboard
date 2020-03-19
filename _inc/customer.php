<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission an alert message
if ($user->getGroupId() != 1 AND !$user->hasPermission('access', 'read_customer')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('customer');

// LOAD CUSTOMER MODEL
$customer_model = $registry->get('loader')->model('customer');

// Validate post data
function validate_request_data($request, $language) 
{
  // Validate customer name
  if (!validateString($request->post['customer_name'])) {
    throw new Exception($language->get('error_customer_name'));
  }

  // Validate customer email and mobile
  if (!validateEmail($request->post['customer_email']) 
    AND (empty($request->post['customer_mobile']) 
      || !valdateMobilePhone($request->post['customer_mobile']))) {

    throw new Exception($language->get('error_customer_email_or_mobile'));
  }

  // Validate customer sex
  if (!validateInteger($request->post['customer_sex'])) {
    throw new Exception($language->get('error_customer_sex'));
  }

  // Validate customer age
  if (!validateInteger($request->post['customer_age']) || $request->post['customer_age'] <= 0) {
    throw new Exception($language->get('error_customer_age'));
  }

  if (get_preference('invoice_view') == 'indian_gst') {
    // Validate customer state
    if (!validateString($request->post['customer_state'])) {
      throw new Exception($language->get('error_customer_state'));
    }
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

// Check customer existance by id
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check email address, if exist or not?
  if (!empty($request->post['customer_email'])) {
    $statement = $db->prepare("SELECT * FROM `customers` WHERE `customer_email` = ? AND `customer_id` != ?");
    $statement->execute(array($request->post['customer_email'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_email_exist'));
    }
  }

  // Check Mobile phone, is exist?
  if (!empty($request->post['customer_mobile'])) {
    $statement = $db->prepare("SELECT * FROM `customers` WHERE `customer_mobile` = ? AND `customer_id` != ?");
    $statement->execute(array($request->post['customer_mobile'], $id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_mobile_exist'));
    }
  }
}

// Create customer
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'CREATE')
{
  try {

    // Create permission check
    if ($user->getGroupId() != 1 AND !$user->hasPermission('access', 'create_customer')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);
    
    // validte existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Customer');

    // insert new customer into databtase
    $customer_id = $customer_model->addCustomer($request->post);

    // fetch customer info
    $customer = $customer_model->getCustomer($customer_id);
    $contact = $customer['customer_mobile'] ? $customer['customer_mobile'] : $customer['customer_email'];

    $Hooks->do_action('After_Create_Customer', $customer);

    header('Content-Type: application/json');
    $due_amount = $customer['balance'] < 0 ? currency_format($customer['balance']) : 0;
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $customer_id, 'customer_name' => $customer['customer_name'], 'customer_contact' => $contact, 'due_amount' => $due_amount));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// update customer
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 AND !$user->hasPermission('access', 'update_customer')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate product id
    if (empty($request->post['customer_id'])) {
      throw new Exception($language->get('error_customer_id'));
    }

    $id = $request->post['customer_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // validte existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Customer', $request);
    
    // edit customer
    $customer = $customer_model->editCustomer($id, $request->post);

    $Hooks->do_action('After_Update_Customer', $customer);

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

// delete customer
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) AND $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 AND !$user->hasPermission('access', 'delete_customer')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate customer id
    if (empty($request->post['customer_id'])) {
      throw new Exception($language->get('error_customer_id'));
    }

    $id = $request->post['customer_id'];
    $the_customer = $customer_model->getCustomer($id);

    if (!$the_customer) {
      throw new Exception($language->get('error_customer_id'));
    }

    $new_customer_id = $request->post['new_customer_id'];

    // walking customer can not be deleted
    if ($request->post['customer_id'] == 1) {
      throw new Exception($language->get('error_delete_unable'));
    }

    // validte delete action
    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' AND empty($new_customer_id)) {
      throw new Exception($language->get('error_new_customer_name'));
    }

    $Hooks->do_action('Before_Delete_Customer', $request);

    // replace customer with new
    if ($request->post['delete_action'] == 'insert_to') {
      $customer_model->replaceWith($new_customer_id, $id);
    } 

    // delete customer
    $customer = $customer_model->deleteCustomer($id);

    $Hooks->do_action('After_Delete_Customer', $customer);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_delete_success'), 'id' => $id));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Customer create form
if (isset($request->get['action_type']) AND $request->get['action_type'] == 'CREATE') 
{
  include 'template/customer_create_form.php';
  exit();
}

// Customer edit form
if (isset($request->get['customer_id']) AND isset($request->get['action_type']) AND $request->get['action_type'] == 'EDIT') {
  
  // fetch supplier info
  $customer = $customer_model->getCustomer($request->get['customer_id']);
  include 'template/customer_form.php';
  exit();
}

// Customer delete form
if (isset($request->get['customer_id']) AND isset($request->get['action_type']) AND $request->get['action_type'] == 'DELETE') {
  
  // fetch supplier info
  $customer = $customer_model->getCustomer($request->get['customer_id']);
  $Hooks->do_action('Before_Customer_Delete_Form', $customer);
  include 'template/customer_del_form.php';
  $Hooks->do_action('After_Customer_Delete_Form', $customer);
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */
$Hooks->do_action('Before_Showing_Customer_List');

$where_query = 'c2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT customers.*, c2s.balance, c2s.status, c2s.sort_order FROM customers 
  LEFT JOIN customer_to_store c2s ON (customers.customer_id = c2s.customer_id) 
  WHERE $where_query GROUP by customers.customer_id
  ) as customers";
 
// Table's primary key
$primaryKey = 'customer_id';

$columns = array(
  array(
      'db' => 'customer_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'customer_id', 'dt' => 'customer_id' ),
  array( 
    'db' => 'customer_name',   
    'dt' => 'customer_name' ,
    'formatter' => function($d, $row) {
        return $row['customer_name'];
    }
  ),
  array( 'db' => 'customer_email',  'dt' => 'customer_email' ),
  array(
      'db'        => 'customer_sex',
      'dt'        => 'customer_sex',
      'formatter' => function( $d, $row ) use($language) {
        $sex = $language->get('label_others');
        if ($d == 1) {
          $sex = $language->get('label_male');
        } else if ($d == 2) {
          $sex = $language->get('label_female');
        }
        return $sex;
      }
  ),
  array( 
    'db' => 'customer_address',   
    'dt' => 'customer_address' ,
    'formatter' => function($d, $row) {
        return limit_char($row['customer_address'], 30);
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
    'db' => 'balance',   
    'dt' => 'balance' ,
    'formatter' => function($d, $row) {
      return currency_format($row['balance']);
    }
  ),
  array(
      'db'        => 'customer_id',
      'dt'        => 'btn_pos',
      'formatter' => function( $d, $row ) use($language) {

        if (!$row['status']) {
          return '<a href="#" class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-shopping-cart"></i></a>';
        }
        
        return '<a href="pos.php?customer_id='.$row['customer_id'].'" id="sell-product" class="btn btn-sm btn-block btn-success" type="button" title="'.$language->get('button_sell').'"><i class="fa fa-shopping-cart"></i></a>';
      }
  ),
  array(
      'db'        => 'customer_id',
      'dt'        => 'btn_profile',
      'formatter' => function( $d, $row ) use($language) {
        return '<a href="customer_profile.php?customer_id='.$row['customer_id'].'" id="sell-product" class="btn btn-sm btn-block btn-warning" type="button" title="'.$language->get('button_view_profile').'"><i class="fa fa-user"></i></a>';
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
      'db'        => 'customer_id',
      'dt'        => 'btn_edit',
      'formatter' => function( $d, $row ) use($language) {
        if (DEMO && $row['customer_id'] == 1) {          
          return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
        }
        return '<button id="edit-customer" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
      }
  ),
  array(
      'db'        => 'customer_id',
      'dt'        => 'btn_delete',
      'formatter' => function( $d, $row ) use($language) {
        if ($row['customer_id'] == 1) {
          return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash"></i></button>';
        }
        return '<button id="delete-customer" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
      }
  )
); 

$where_query = '1=1';
if (isset($request->get['p_day']) AND $p_day = (int)$request->get['p_day']) {
  $where_query .= ' AND DAY(inv_date) = '.$p_day;
}
if (isset($request->get['p_month']) AND $p_month = (int)$request->get['p_month']) {
  $where_query .= ' AND MONTH(inv_date) = '.$p_month;
}
if (isset($request->get['p_year']) AND $p_year = (int)$request->get['p_year']) {
  $where_query .= ' AND YEAR(inv_date) = '.$p_year;
}

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns, null, $where_query)
);

$Hooks->do_action('After_Showing_Customer_List');

/**
 *===================
 * END DATATABLE
 *===================
 */