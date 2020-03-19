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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_printer')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('printer');

// LOAD PRINTER MODEL
$printer_model = $registry->get('loader')->model('printer');

// Validate post data
function validate_request_data($request, $language) 
{
  // Validate printer name
  if (!validateString($request->post['title'])) {
    throw new Exception($language->get('error_title'));
  }

  // Validate printer type
  if (!in_array($request->post['type'], array('network','windows','linux'))) {
    throw new Exception($language->get('error_type'));
  }

  // Validate character per line
  if (!validateInteger($request->post['char_per_line'])) {
    throw new Exception($language->get('error_char_per_line'));
  }

  // Validate path
  if (!validateString($request->post['path']) && !validateString($request->post['ip_address']) && !validateString($request->post['port'])) {
    throw new Exception($language->get('error_path_ip_or_port'));
  }

  // Validate store
  if (!isset($request->post['printer_store']) || empty($request->post['printer_store'])) {
    throw new Exception($language->get('error_printer_store'));
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

// Check printer existance by id
function validate_existance($request, $language, $printer_id = 0)
{
  global $db;

  // Check title, is exist?
  if (!empty($request->post['title'])) {
    $statement = $db->prepare("SELECT * FROM `printers` WHERE `title` = ? AND `printer_id` != ?");
    $statement->execute(array($request->post['title'], $printer_id));
    if ($statement->rowCount() > 0) {
      throw new Exception($language->get('error_printer_exist'));
    }
  }
}

// Create printer
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Create permission check
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_printer')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);
    
    // validte existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_printer');

    // insert new printer into databtase
    $printer_id = $printer_model->addprinter($request->post);

    // fetch printer info
    $printer = $printer_model->getprinter($printer_id);

    $Hooks->do_action('After_Create_Printer', $printer);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $printer_id, 'printer' => $printer));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// update printer
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_printer')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate product id
    if (empty($request->post['printer_id'])) {
      throw new Exception($language->get('error_printer_id'));
    }

    $printer_id = $request->post['printer_id'];

    if (DEMO && $printer_id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // validte existance
    validate_existance($request, $language, $printer_id);

    $Hooks->do_action('Before_Update_Printer', $request);
    
    // edit printer
    $printer = $printer_model->editprinter($printer_id, $request->post);

    $Hooks->do_action('After_Update_Printer', $printer);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_update_success'), 'id' => $printer_id));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// delete printer
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_printer')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate printer id
    if (empty($request->post['printer_id'])) {
      throw new Exception($language->get('error_printer_id'));
    }

    $printer_id = $request->post['printer_id'];
    $the_printer = $printer_model->getprinter($printer_id);
    if (!$the_printer) {
      throw new Exception($language->get('error_printer_id'));
    }

    if (DEMO && $printer_id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    $Hooks->do_action('Before_Delete_printer', $request);

    // delete printer
    $printer = $printer_model->deleteprinter($printer_id);

    $Hooks->do_action('After_Delete_printer', $printer);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_delete_success'), 'id' => $printer_id));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// printer create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{
  include 'template/printer_create_form.php';
  exit();
}

// printer edit form
if (isset($request->get['printer_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
  
  // fetch supplier info
  $printer = $printer_model->getprinter($request->get['printer_id']);
  include 'template/printer_edit_form.php';
  exit();
}

// printer delete form
if (isset($request->get['printer_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') {
  
  // fetch supplier info
  $printer = $printer_model->getprinter($request->get['printer_id']);
  $Hooks->do_action('Before_printer_Delete_Form', $printer);
  include 'template/printer_delete_form.php';
  $Hooks->do_action('After_printer_Delete_Form', $printer);
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_printer_List');

$where_query = 'p2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT printers.*, p2s.path, p2s.ip_address, p2s.port, p2s.status, p2s.sort_order FROM printers 
  LEFT JOIN printer_to_store p2s ON (printers.printer_id = p2s.pprinter_id) 
  WHERE $where_query GROUP by printers.printer_id
  ) as printers";
 
// Table's primary key
$primaryKey = 'printer_id';

$columns = array(
  array(
      'db' => 'printer_id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'printer_id', 'dt' => 'printer_id' ),
  array( 
    'db' => 'title',   
    'dt' => 'title',
    'formatter' => function($d, $row) {
      return $row['title'];
    }
  ),
  array( 
    'db' => 'type',   
    'dt' => 'type',
    'formatter' => function($d, $row) {
      return $row['type'];
    }
  ),
  array( 
    'db' => 'path',   
    'dt' => 'path',
    'formatter' => function($d, $row) {
      return $row['path'];
    }
  ),
  array( 
    'db' => 'ip_address',   
    'dt' => 'ip_address',
    'formatter' => function($d, $row) {
      return $row['ip_address'];
    }
  ),
  array( 
    'db' => 'port',   
    'dt' => 'port',
    'formatter' => function($d, $row) {
      return $row['port'];
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
      'db'        => 'printer_id',
      'dt'        => 'btn_view',
      'formatter' => function( $d, $row ) use($language) {
        return '<button class="btn btn-sm btn-block btn-primary delete-row" onClick="swal(\'Attention!\', \'this action is under development\')" type="button" title="'.$language->get('button_view').'"><i class="fa fa-eye"></i></button>';
      }
  ),
  array(
      'db'        => 'printer_id',
      'dt'        => 'btn_edit',
      'formatter' => function( $d, $row ) use($language) {
        if (DEMO && $row['printer_id'] == 1) {          
          return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
        }
        return '<button id="edit-printer" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
      }
  ),
  array(
      'db'        => 'printer_id',
      'dt'        => 'btn_delete',
      'formatter' => function( $d, $row ) use($language) {
        if (DEMO && $row['printer_id'] == 1) {          
          return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-trash"></i></button>';
        }
        return '<button id="delete-printer" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
      }
  )
); 

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_printer_List');

/**
 *===================
 * END DATATABLE
 *===================
 */