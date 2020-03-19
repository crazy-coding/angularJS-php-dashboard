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
// if user have not reading permission an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_currency')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('currency');

// LOAD CURRENCY MODEL
$currency_model = $registry->get('loader')->model('currency');

// Validate post data
function validate_request_data($request, $language) 
{
  // Validate title
  if(!validateString($request->post['title'])) {
    throw new Exception($language->get('error_currency_title'));
  }

  // Validate code
  if(!validateString($request->post['code'])) {
    throw new Exception($language->get('error_currency_code'));
  }

  // Validate currency left/rightsymbol
  if(!validateString($request->post['symbol_left']) && !validateString($request->post['symbol_right'])) {
    throw new Exception($language->get('error_currency_symbol'));
  }

  // Validate decimal place
  if(!validateInteger($request->post['decimal_place'])) {
    throw new Exception($language->get('error_currency_decimal_place'));
  }

  // Validate currency_store
  if (!isset($request->post['currency_store']) || empty($request->post['currency_store'])) {
    throw new Exception($language->get('error_store'));
  }

  // Validate status
  if (!is_numeric($request->post['status'])) {
    throw new Exception($language->get('error_status'));
  }

  // sort order validation
  if (!is_numeric($request->post['sort_order'])) {
    throw new Exception($language->get('error_sort_order'));
  }
}

// Check currency existance by id
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check currency title, is exist?
  $statement = $db->prepare("SELECT * FROM `currency` WHERE (`title` = ? OR `code` = ?) AND `currency_id` != ?");
  $statement->execute(array($request->post['title'], $request->post['code'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_payment_code_or_title_exist'));
  }
}

// Create currency
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Create permission check
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_currency')) {
      throw new Exception($language->get('error_read_permission'));
    }
    
    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Currency');

    // insert currency into database    
    $currency_id = $currency_model->addCurrency($request->post);

    // get currency
    $currency = $currency_model->getCurrency($currency_id);

    $Hooks->do_action('After_Create_Currency', $currency);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $currency_id, 'currency' => $currency));
    exit();

  } catch(Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// update currency
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_currency')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate currency id
    if (empty($request->post['currency_id'])) {
      throw new Exception($language->get('error_currency_id'));
    }

    $id = $request->post['currency_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Currency', $request);
    
    // edit currency        
    $currency = $currency_model->editCurrency($id, $request->post);

    $Hooks->do_action('After_Update_Currency', $currency);

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

// delete currency
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_currency')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // validte currency id
    if (empty($request->post['currency_id'])) {
      throw new Exception($language->get('error_currency_id'));
    }

    $id = $request->post['currency_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // active currency can not be deleted
    if ($id == currency_id()) {
      throw new Exception($language->get('error_delete_active_currency'));
    }

    $Hooks->do_action('Before_Delete_Currency', $request);

    // delete currency
    $currency = $currency_model->deleteCurrency($id);

    $Hooks->do_action('After_Delete_Currency', $currency);
    
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

// Currency edit form
if (isset($request->get['currency_id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
    
    $currency_id = (int)$request->get['currency_id'];

    // fetch currency info
    $currency = $currency_model->getCurrency($currency_id);
    include 'template/currency_form.php';
    exit();
}


/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Currency_List');
 
$where_query = 'c2s.store_id = '.store_id();
 
// DB table to use
$table = "(SELECT currency.*, c2s.status, c2s.sort_order FROM currency 
  LEFT JOIN currency_to_store c2s ON (currency.currency_id = c2s.currency_id) 
  WHERE $where_query GROUP by currency.currency_id
  ) as currency";
 
// Table's primary key
$primaryKey = 'currency_id';

$columns = array(
  array(
      'db' => 'currency_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'currency_id', 'dt' => 'currency_id' ),
  array( 
    'db' => 'title',   
    'dt' => 'title' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['title']);
    }
  ),
  array( 'db' => 'code',  'dt' => 'code' ),
  array( 'db' => 'symbol_left',  'dt' => 'symbol_left' ),
  array( 'db' => 'symbol_right',  'dt' => 'symbol_right' ),
  array( 'db' => 'decimal_place',  'dt' => 'decimal_place' ),
  array( 
    'db' => 'status',   
    'dt' => 'status' ,
    'formatter' => function($d, $row) use($language) {
        return $row['status'] == 1 ? '<span class="label label-info">'.$language->get('text_enabled').'</span>' : '<span class="label label-warning">'.$language->get('text_disabled').'</span>';
    }
  ),
  array( 
    'db' => 'status',   
    'dt' => 'btn_edit' ,
    'formatter' => function($d, $row) use($language) {
      if (DEMO && $row['currency_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
      }
      return '<button id="edit-currency" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array( 
    'db' => 'status',   
    'dt' => 'btn_delete' ,
    'formatter' => function($d, $row) use($language) {
      if (DEMO && $row['currency_id'] == 1) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-trash"></i></button>';
      }
      if ($row['currency_id'] == currency_id()) {
        return '<button id="delete-currency" class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-fw fa-trash" title="'.$language->get('button_delete').'"></i></button>';
      }

      return '<button id="delete-currency" class="btn btn-sm btn-block btn-danger" type="button"><i class="fa fa-fw fa-trash" title="'.$language->get('button_delete').'"></i></button>';
    }
  ),
  array( 
    'db' => 'code',   
    'dt' => 'btn_activate' ,
    'formatter' => function($d, $row) use($currency, $language) {
        $button = "";
        if ($row['status'] == 1) {
            if ($currency->getCode() == $row['code']) {
                $button = '<button class="btn btn-sm  btn-block btn-info" type="button" disabled><i class="fa fa-fw fa-check"></i>'.$language->get('button_activated').'</button>';
            } else {
                $button = '<button  type="button" class="btn btn-sm btn-block btn-success currency-change" data-code="'.$row['code'].'" data-loading-text="Applying..."><i class="fa fa-fw fa-check"></i>'.$language->get('button_activate').'</button>';
            }
        }
        return $button;
    }
  )
); 

$where_query = '1=1';
if (isset($request->get['p_day']) && $p_day = (int)$request->get['p_day']) {
  $where_query .= ' AND DAY(inv_date) = '.$p_day;
}
if (isset($request->get['p_month']) && $p_month = (int)$request->get['p_month']) {
  $where_query .= ' AND MONTH(inv_date) = '.$p_month;
}
if (isset($request->get['p_year']) && $p_year = (int)$request->get['p_year']) {
  $where_query .= ' AND YEAR(inv_date) = '.$p_year;
}

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns, null, $where_query)
);

$Hooks->do_action('After_Showing_Currency_List');

/**
 *===================
 * END DATATABLE
 *===================
 */