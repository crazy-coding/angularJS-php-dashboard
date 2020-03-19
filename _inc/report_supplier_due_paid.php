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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_supplier_due_paid_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('report');
$store_id = store_id();

// fetch invoice 
if ($request->server['REQUEST_METHOD'] == 'GET' && isset($request->get['invoice_id']))
{
    try {

        if (empty($request->get['invoice_id'])) {
            throw new Exception($language->get('error_invoice_id'));
        }

        $invoice_id = $request->get['invoice_id'];

        // fetch invoice info
        $statement = $db->prepare("SELECT buying_payments.* FROM `buying_payments` 
            LEFT JOIN `buying_price` ON (`buying_payments`.`invoice_id` = `buying_price`.`invoice_id`) 
            WHERE `buying_payments`.`invoice_id` = ? AND `buying_payments`.`store_id` = ?");
        $statement->execute(array($invoice_id, $store_id));
        $invoice = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($invoice)) {
            throw new Exception($language->get('error_buying_payments_not_found'));
        }
        
        // fetch invoice item
        $statement = $db->prepare("SELECT * FROM `buying_item` WHERE invoice_id = ?");
        $statement->execute(array($invoice_id));
        $buying_items = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($buying_items)) {
            throw new Exception($language->get('error_buying_item'));
        }

        $invoice['items'] = $buying_items;

        header('Content-Type: application/json');
        echo json_encode(array('msg' => $language->get('text_success'), 'invoice' => $invoice));
        exit();

    }
    catch(Exception $e) { 

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

$where_query = 'buying_payments.store_id = ' . $store_id . ' AND buying_payments.type = "due_paid"';

$from = from();
$to = to();
$where_query .= date_range_buying_payments_filter($from, $to);

// DB table to use
$table = "(SELECT buying_payments.* FROM buying_payments 
  LEFT JOIN buying_price ON (buying_payments.invoice_id = buying_price.invoice_id) 
  WHERE $where_query) as customers";

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array( 'db' => 'created_at', 'dt' => 'created_at' ),
    array( 'db' => 'invoice_id', 'dt' => 'invoice_id' ),
    array(
        'db'        => 'pmethod_id',
        'dt'        => 'pmethod_name',
        'formatter' => function($d, $row) {
            return get_the_pmethod($row['pmethod_id'], 'name');
        }
    ),
    array(
        'db'        => 'created_by',
        'dt'        => 'created_by',
        'formatter' => function($d, $row) {
            return get_the_user($row['created_by'], 'username');
        }
    ),
    array(
        'db'        => 'amount',
        'dt'        => 'amount',
        'formatter' => function($d, $row) {
            return currency_format($row['amount']);
        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 'btn_view',
        'formatter' => function($d, $row) {
            return '<a class="btn btn-sm btn-block btn-info" href="#"><i class="fa fa-eye"></i></a>';
        }
    ),
);

// output for datatable
echo json_encode(
    SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns)
);

/**
 *===================
 * END DATATABLE
 *===================
 */