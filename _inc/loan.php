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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_loan')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('loan');
$store_id = store_id();

// LOAD LOAN MODEL
$loan_model = $registry->get('loader')->model('loan');

// Validate post data
function validate_request_data($request, $language) {

  // Validate loan from
  if (!validateString($request->post['loan_from'])) {
      throw new Exception($language->get('error_loan_from'));
  }

  // Validate title
  if (!validateString($request->post['title'])) {
      throw new Exception($language->get('error_loan_headline'));
  }

  // Validate store
  if (!isset($request->post['loan_store']) || empty($request->post['loan_store'])) {
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

// Check loan existance by id
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if loan name exist or not
  $statement = $db->prepare("SELECT * FROM `loans` WHERE (`title` = ?) AND `loan_id` != ?");
  $statement->execute(array($request->post['title'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_loan_title_exist'));
  }

  // Check, if loan name exist or not
  $statement = $db->prepare("SELECT * FROM `loans` WHERE (`ref_no` = ?) AND `loan_id` != ?");
  $statement->execute(array($request->post['ref_no'], $id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_ref_no_exist'));
  }
}

// take loan
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'TAKE')
{
  try {

    // Check take permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'take_loan')) {
      throw new Exception($language->get('error_take_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate date
    if (!isItValidDate($request->post['date'])) {
      throw new Exception($language->get('error_date'));
    }

    // Validate amount
    if (!validateFloat($request->post['amount'])) {
        throw new Exception($language->get('error_loan_amount'));
    }

    // Validate interest
    if (empty($request->post['interest'])) {
        throw new Exception($language->get('error_loan_interest'));
    }

    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Loan');

    // add loan
    $loan_id = $loan_model->addLoan($request->post);

    // fetch the loan info
    $loan = $loan_model->getLoan($loan_id);

    $Hooks->do_action('After_Create_Loan', $loan);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_take_loan_success'), 'id' => $loan_id, 'loan' => $loan));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// update loan
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_loan')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate loan id
    if (empty($request->post['loan_id'])) {
      throw new Exception($language->get('error_loan_id'));
    }

    $id = $request->post['loan_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate existance
    validate_existance($request, $language, $id);

    $Hooks->do_action('Before_Update_Loan', $request);
    
    // edit loan
    $loan = $loan_model->editLoan($id, $request->post);

    $Hooks->do_action('After_Update_Loan', $loan);
    
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

// delete loan
if ($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') {
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_loan')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate loan id
    if (empty($request->post['loan_id'])) {
      throw new Exception($language->get('error_loan_id'));
    }

    $id = $request->post['loan_id'];

    if (DEMO && $id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    $Hooks->do_action('Before_Delete_Loan', $request);

    // delete the loan
    $loan = $loan_model->deleteLoan($id);

    $Hooks->do_action('After_Delete_Loan', $loan);
    
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

// Create loan
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'PAID')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'loan_pay')) {
      throw new Exception($language->get('error_loan_pay_permission'));
    }

    // Validate loan id
    if (!validateInteger($request->post['loan_id'])) {
        throw new Exception($language->get('error_loan_id'));
    }

    // Validate paid
    if (empty($request->post['paid'])) {
        throw new Exception($language->get('error_paid_amount'));
    }

    // fetch the loan info
    $loan = $loan_model->getLoan($request->post['loan_id']);
    if ($loan['due'] < $request->post['paid']) {
      throw new Exception($language->get('error_pay_amount_greater_than_due_amount'));
    }

    $Hooks->do_action('Before_Loan_Pay');

    // add loan
    $loan_id = $loan_model->addLoanPay($request->post);

    $Hooks->do_action('After_Loan_Pay', $loan);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_loan_paid_success'), 'id' => $loan_id, 'loan' => $loan));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// loan create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'TAKE') 
{
  include 'template/loan_take_form.php';
  exit();
}

// loan edit form
if (isset($request->get['loan_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') 
{
  // fetch loan info
  $loan = $loan_model->getLoan($request->get['loan_id']);
  include 'template/loan_edit_form.php';
  exit();
}

// loan delete form
if (isset($request->get['loan_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') 
{
  // fetch loan info
  $loan = $loan_model->getLoan($request->get['loan_id']);
  $Hooks->do_action('Before_Loan_Delete_Form', $loan);
  include 'template/loan_del_form.php';
  $Hooks->do_action('After_Loan_Delete_Form', $loan);
  exit();
}

// loan view
if (isset($request->get['loan_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'VIEW') 
{
  // fetch loan info
  $loan = $loan_model->getLoan($request->get['loan_id']);
  $payments = $loan_model->getLoanPayments($request->get['loan_id']);
  include 'template/loan_view.php';
  exit();
}

// loan pay form
if (isset($request->get['loan_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'PAY') 
{
  // fetch loan info
  $loan = $loan_model->getLoan($request->get['loan_id']);
  // print_r($loan);die;
  include 'template/loan_pay_form.php';
  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */
$where_query = "l2s.store_id = $store_id";
if (from()) {
  $from = from();
  $to = to();
  $where_query .= date_range_loan_filter($from, $to);
}
if (isset($request->get['type'])) {
  switch ($request->get['type']) {
    case 'paid':
      $where_query .= " AND loans.due = 0";
      break;
    case 'due':
      $where_query .= " AND loans.due > 0";
      break;
    case 'disabled':
      $where_query .= " AND l2s.status = 0";
      break;
    default:
      $where_query .= " AND l2s.status = 1";
      // $where_query .= " AND loans.due > 0";
      break;
  }
}
// DB table to use
$table = "(SELECT loans.*, l2s.status, l2s.sort_order FROM loans 
  LEFT JOIN loan_to_store l2s ON (loans.loan_id = l2s.lloan_id) 
  WHERE $where_query GROUP by loans.loan_id
  ) as loans";
 
// Table's primary key
$primaryKey = 'loan_id';

// indexes
$columns = array(
    array(
        'db' => 'loan_id',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            return 'row_'.$d;
        }
    ),
    array( 'db' => 'loan_id', 'dt' => 'loan_id' ),
    array( 'db' => 'ref_no', 'dt' => 'ref_no' ),
    array( 'db' => 'created_at', 'dt' => 'created_at' ),
    array( 'db' => 'title', 'dt' => 'title' ),
    array( 
      'db' => 'loan_from',   
      'dt' => 'loan_from' ,
      'formatter' => function($d, $row) {
        return ucfirst($row['loan_from']);
      }
    ),
    array( 
      'db' => 'amount',   
      'dt' => 'amount' ,
      'formatter' => function($d, $row) {
        return currency_format($row['amount']);
      }
    ),
    array( 
      'db' => 'interest',   
      'dt' => 'interest' ,
      'formatter' => function($d, $row) {
        return currency_format($row['interest']);
      }
    ),
    array( 
      'db' => 'payable',   
      'dt' => 'payable' ,
      'formatter' => function($d, $row) {
        return currency_format($row['payable']);
      }
    ),
    array( 
      'db' => 'paid',   
      'dt' => 'paid' ,
      'formatter' => function($d, $row) {
        return currency_format($row['paid']);
      }
    ),
    array( 
      'db' => 'due',   
      'dt' => 'due' ,
      'formatter' => function($d, $row) {
        return currency_format($row['due']);
      }
    ),
    array( 
      'db' => 'loan_id',   
      'dt' => 'btn_pay' ,
      'formatter' => function($d, $row) use($language) {
        return '<button id="loan-pay" class="btn btn-sm btn-block btn-success edit-row" type="button" title="'.$language->get('button_pay').'"><i class="fa fa-money"></i></button>';
      }
    ),
    array( 
      'db' => 'loan_id',   
      'dt' => 'btn_view' ,
      'formatter' => function($d, $row) use($language) {
        return '<button id="view" class="btn btn-sm btn-block btn-warning edit-row" type="button" title="'.$language->get('button_view').'"><i class="fa fa-eye"></i></button>';
      }
    ),
    array( 
      'db' => 'loan_id',   
      'dt' => 'btn_edit' ,
      'formatter' => function($d, $row) use($language) {
        return '<button id="edit-loan" class="btn btn-sm btn-block btn-info edit-row" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-edit"></i></button>';
      }
    ),
    array( 
      'db' => 'loan_id',   
      'dt' => 'btn_delete' ,
      'formatter' => function($d, $row) use($language) {
        return '<button id="delete-loan" class="btn btn-sm btn-block btn-danger edit-row" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-trash"></i></button>';
      }
    ),
);

$loans = SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns);
echo json_encode($loans);

/**
 *===================
 * END DATATABLE
 *===================
 */
 