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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_sms_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('sms');

// LOAD BOX MODEL
$sms_model = $registry->get('loader')->model('sms');


/**
 *===================
 * START DATATABLE
 *===================
 */
 
// DB table to use
$where_query = 'store_id = ' . store_id();

if (isset($request->get['type'])) {
  switch ($request->get['type']) {
    case 'pending':
      $where_query .= " AND process_status = 0";
      break;
    case 'failed':
      $where_query .= " AND delivery_status = 'failed'";
      break;
    case 'delivered':
      $where_query .= " AND delivery_status = 'delivered'";
      break;
    default:
      # code...
      break;
  }
}

$from = from();
$to = to();
$from = $from ? $from : date('Y-m-d');
$to = $to ? $to : date('Y-m-d');
if (($from && ($to == false)) || ($from == $to)) {
  $day = date('d', strtotime($from));
  $month = date('m', strtotime($from));
  $year = date('Y', strtotime($from));
  $where_query .= " AND DAY(`sms_schedule`.`schedule_datetime`) = $day";
  $where_query .= " AND MONTH(`sms_schedule`.`schedule_datetime`) = $month";
  $where_query .= " AND YEAR(`sms_schedule`.`schedule_datetime`) = $year";
} else {
  $from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
  $to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
  $where_query .= " AND sms_schedule.schedule_datetime >= '{$from}' AND sms_schedule.schedule_datetime <= '{$to}'";
}

$table = "(SELECT * FROM sms_schedule WHERE $where_query) as sms_schedule";
 
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
  array( 'db' => 'schedule_datetime', 'dt' => 'schedule_datetime' ),
  array( 'db' => 'campaign_name', 'dt' => 'campaign_name' ),
  array( 'db' => 'people_name', 'dt' => 'people_name' ),
  array( 'db' => 'mobile_number', 'dt' => 'mobile_number' ),
  array( 
    'db' => 'delivery_status',   
    'dt' => 'delivery_status' ,
    'formatter' => function($d, $row) {
        return ucfirst($row['delivery_status']);
    }
  ),
  array( 
    'db' => 'response_text',   
    'dt' => 'response_text' ,
    'formatter' => function($d, $row) {
        return $row['response_text'];
    }
  ),
  array( 
    'db' => 'process_status',   
    'dt' => 'process_status' ,
    'formatter' => function($d, $row) {
      if ($row['delivery_status'] == 'delivered') {
        return '<span class="label label-warning">Completed</span>';
      } elseif ($row['process_status'] && $row['delivery_status'] != 'failed') {
        return '<span class="label label-info">Processing</span>';
      } elseif ($row['response_text']) {
        return '<span class="label label-danger">Error</span>';
      } else {
        return '<span class="label label-warning">Processing...</span>';
      }
    }
  ),
  array( 
    'db' => 'delivery_status',   
    'dt' => 'delivery_status' ,
    'formatter' => function($d, $row) {
      if ($row['delivery_status'] == 'delivered') {
        return '<span class="label label-success">Delivered</span>';
      } elseif ($row['process_status'] && $row['delivery_status'] != 'failed') {
        return '';
      } elseif ($row['response_text']) {
        return '<span class="label label-danger">Failed</span>';
      } else {
        return '';
      }
    }
  ),
  array( 
      'db' => 'id',   
      'dt' => 'button_resend' ,
      'formatter' => function($d, $row) use($language) {
        if (($row['delivery_status'] == 'delivered' || !$row['response_text']) 
              || ($row['process_status'] && $row['delivery_status'] != 'failed')) {
          return '';
        }
        return '<button id="resend-sms" class="btn btn-info">'.$language->get('button_resend').'</button>';
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