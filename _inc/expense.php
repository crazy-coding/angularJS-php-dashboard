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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_expense')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('expense');

$store_id = store_id();
$user_id = user_id();

$ref_prefix = 'EXP';

// Ualidate post data
function validate_request_data($request, $language) 
{
    // Ualidate date
    if (!isItValidDate($request->post['date'])) {
      throw new Exception($language->get('error_date'));
    }

    // Ualidate time
    if (!isItValidTime12($request->post['time'])) {
      throw new Exception($language->get('error_time'));
    }

    // Ualidate category id
    if (!validateInteger($request->post['category_id'])) {
      throw new Exception($language->get('error_category_id'));
    }

    // Ualidate title
    if (!validateString($request->post['title'])) {
      throw new Exception($language->get('error_title'));
    }

    // Ualidate amount
    if (!validateFloat($request->post['amount'])) {
      throw new Exception($language->get('error_amount'));
    }
}

// Create expence
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_expense')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Ualidate post data
    validate_request_data($request, $language);

    $Hooks->do_action('Before_Create_Expense');

    $reference_no = $request->post['reference_no'] ? $ref_prefix . $request->post['reference_no'] : $ref_prefix . unique_id();
    $created_at = $request->post['date'] . ' ' . date("H:i", strtotime($request->post['time']));
    $category_id = $request->post['category_id'];
    $title = $request->post['title'];
    $amount = $request->post['amount'];
    $note = $request->post['note'];

    // Check for dublicate
    $statement = $db->prepare("SELECT * FROM `expenses` WHERE `reference_no` = ?");
    $statement->execute(array($reference_no));
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        throw new Exception($language->get('error_reference_no_alrady_exist'));
    }

    // Insert into buying info
    $statement = $db->prepare("INSERT INTO `expenses` (store_id, reference_no, category_id, title, amount, note, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $statement->execute(array($store_id, $reference_no, $category_id, $title, $amount, $note, $user_id, $created_at));
    $id = $db->lastInsertId();

    $Hooks->do_action('After_Create_Expense', $id);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $id));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// Update expense
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_expense')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Ualidate category id
    if (!validateInteger($request->post['category_id'])) {
      throw new Exception($language->get('error_category_id'));
    }

    // Ualidate title
    if (!validateString($request->post['title'])) {
      throw new Exception($language->get('error_title'));
    }

    // Ualidate amount
    if (!validateFloat($request->post['amount'])) {
      throw new Exception($language->get('error_amount'));
    }

    $id = $request->post['id'];
    if (empty($id)) {
        throw new Exception($language->get('error_id'));
    }

    $category_id = $request->post['category_id'];
    $title = $request->post['title'];
    $amount = $request->post['amount'];
    $note = $request->post['note'];

    $Hooks->do_action('Before_Update_Expense', $request);

    // Update expense
    $statement = $db->prepare("UPDATE `expenses` SET `category_id` = ?, `title` = ?, `amount` = ?, `note` = ?, `created_by` = ? WHERE `id` = ?");
    $statement->execute(array($category_id, $title, $amount, $note, $user_id, $id));

    $Hooks->do_action('After_Update_Expense', $id);

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

// Delete expense
if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE') 
{
  try {

    // Check delete permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_expense')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate invoice id
    if (empty($request->post['id'])) {
      throw new Exception($language->get('error_id'));
    }

    $Hooks->do_action('Before_Delete_Expense', $request);

    $id = $request->post['id'];

    // Delete invoice info
    $statement = $db->prepare("DELETE FROM  `expenses` WHERE `store_id` = ? AND `id` = ? LIMIT 1");
    $statement->execute(array($store_id, $id));

    $Hooks->do_action('After_Delete_Expense', $id);
    
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

// View expense
if (isset($request->get['action_type']) && $request->get['action_type'] == 'SUMMARY') 
{
  $to = date('Y-m-d H:i:s');

  // Summary
    $statement = $db->prepare("SELECT `expenses`.`created_at`, `expenses`.`category_id`, SUM(`expenses`.`amount`) as total, `expense_categorys`.`category_name`, `expense_categorys`.`category_slug`, `expense_categorys`.`category_details`, `expense_categorys`.`parent_id` FROM `expenses` LEFT JOIN `expense_categorys` ON (`expenses`.`category_id` = `expense_categorys`.`category_id`) GROUP BY `expenses`.`category_id` ORDER BY `total` DESC");
    $statement->execute(array());
    $summary = $statement->fetchAll(PDO::FETCH_ASSOC);

  // This Week
    $from = date('Y-m-d H:i:s',strtotime(date("Y-m-d", time()) . " - 7 day"));
    $statement = $db->prepare("SELECT `expenses`.`created_at`, `expenses`.`category_id`, SUM(`expenses`.`amount`) as total, `expense_categorys`.`category_name`, `expense_categorys`.`category_slug`, `expense_categorys`.`category_details`, `expense_categorys`.`parent_id` FROM `expenses` LEFT JOIN `expense_categorys` ON (`expenses`.`category_id` = `expense_categorys`.`category_id`) WHERE `expenses`.`created_at` >= '{$from}' AND `expenses`.`created_at` <= '{$to}' GROUP BY `expenses`.`category_id` ORDER BY `total` DESC");
    $statement->execute(array());
    $week_summary = $statement->fetchAll(PDO::FETCH_ASSOC);

  // This Month
    $from = date('Y-m-d H:i:s',strtotime(date("Y-m-d", time()) . " - 30 day"));
    $statement = $db->prepare("SELECT `expenses`.`created_at`, `expenses`.`category_id`, SUM(`expenses`.`amount`) as total, `expense_categorys`.`category_name`, `expense_categorys`.`category_slug`, `expense_categorys`.`category_details`, `expense_categorys`.`parent_id` FROM `expenses` LEFT JOIN `expense_categorys` ON (`expenses`.`category_id` = `expense_categorys`.`category_id`) WHERE `expenses`.`created_at` >= '{$from}' AND `expenses`.`created_at` <= '{$to}' GROUP BY `expenses`.`category_id` ORDER BY `total` DESC");
    $statement->execute(array());
    $month_summary = $statement->fetchAll(PDO::FETCH_ASSOC);

  // This Year
    $from = date('Y-m-d H:i:s',strtotime(date("Y-m-d", time()) . " - 365 day"));
    $statement = $db->prepare("SELECT `expenses`.`created_at`, `expenses`.`category_id`, SUM(`expenses`.`amount`) as total, `expense_categorys`.`category_name`, `expense_categorys`.`category_slug`, `expense_categorys`.`category_details`, `expense_categorys`.`parent_id` FROM `expenses` LEFT JOIN `expense_categorys` ON (`expenses`.`category_id` = `expense_categorys`.`category_id`) WHERE `expenses`.`created_at` >= '{$from}' AND `expenses`.`created_at` <= '{$to}' GROUP BY `expenses`.`category_id` ORDER BY `total` DESC");
    $statement->execute(array());
    $year_summary = $statement->fetchAll(PDO::FETCH_ASSOC);


    include 'template/expense_summary.php';
    exit();
}

// view expense
if (isset($request->get['id']) && isset($request->get['action_type']) && $request->get['action_type'] == 'VIEW') 
{
    $id = $request->get['id'];
    $statement = $db->prepare("SELECT * FROM `expenses` WHERE `id` = ?");
    $statement->execute(array($id));
    $expense = $statement->fetch(PDO::FETCH_ASSOC);
    include 'template/expense_view.php';
    exit();
}

// Expense edit form
if (isset($request->get['id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
    try {
        $id = $request->get['id'];
        if (empty($id)) {
            throw new Exception($language->get('error_id'));
        }
        $statement = $db->prepare("SELECT * FROM `expenses` WHERE `id` = ?");
        $statement->execute(array($id));
        $expense = $statement->fetch(PDO::FETCH_ASSOC);
        include 'template/expense_edit_form.php';
        exit();

    } catch (Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
      }
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Expenses_List');

$where_query = 'status=1';
if (from()) {
  $from = from();
  $to = to();
  $where_query .= date_range_expense_filter($from, $to);
}

// DB table to use
$table = "(SELECT * FROM expenses 
  WHERE $where_query GROUP by id
  ) as expenses";
 
// Table's primary key
$primaryKey = 'id';

$columns = array(
  array(
      'db' => 'id',
      'dt' => 'DT_RowId',
      'formatter' => function( $d, $row ) {
          return 'row_'.$d;
      }
  ),
  array( 'db' => 'id', 'dt' => 'id' ),
  array( 
    'db' => 'category_id',   
    'dt' => 'category_name',
    'formatter' => function($d, $row) {
        $parent = '';
        $category = get_the_expense_category($row['category_id']);
        if ($category['parent_id']) {
            $parent = get_the_expense_category($category['parent_id']);
            $parent = $parent['category_name'] .  ' > ';
        }
        $category = get_the_expense_category($row['category_id']);
        return $parent . $category['category_name'];
    }
  ),
  array( 'db' => 'reference_no', 'dt' => 'reference_no' ),
  array( 'db' => 'title', 'dt' => 'title' ),
  array( 
    'db' => 'amount',   
    'dt' => 'amount',
    'formatter' => function($d, $row) use($language) {
      return currency_format($row['amount']);
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
    'db'        => 'id',
    'dt'        => 'btn_view',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="view-expense-btn" class="btn btn-sm btn-block btn-info" type="button" title="'.$language->get('button_viefw').'"><i class="fa fa-fw fa-eye"></i></button>';
    }
  ),
  array(
    'db'        => 'id',
    'dt'        => 'btn_edit',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="edit-expense-btn" class="btn btn-sm btn-block btn-primary" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-fw fa-pencil"></i></button>';
    }
  ),
  array(
    'db'        => 'id',
    'dt'        => 'btn_delete',
    'formatter' => function( $d, $row ) use($language) {
      return '<button id="delete-expense-btn" class="btn btn-sm btn-block btn-danger" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-fw fa-trash"></i></button>';
    }
  )
); 

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

$Hooks->do_action('After_Showing_Expenses_List');

/**
 *===================
 * END DATATABLE
 *===================
 */