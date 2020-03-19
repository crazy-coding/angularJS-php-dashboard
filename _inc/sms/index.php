<?php
namespace SMSGateway;

use Exception;

ob_start();
session_start();
include realpath(__DIR__.'/../../').'/_init.php';

//  Load Language File
$language->load('sms');

$config = include 'config.php';
require_once 'vendor/autoload.php';

$sms_model = $registry->get('loader')->model('sms');

if (!isset($request->get['action_type']) 
	&& !isset($request->post['action_type']) 
		&& (!isset($argc) || !isset($argv[1]))) {

	exit();
}

$action_type = '';
if (isset($request->get['action_type'])) {
	$action_type = $request->get['action_type'];
} elseif (isset($request->post['action_type'])) {
	$action_type = $request->post['action_type'];
} elseif (isset($argv[1])) {
	$action_type = $argv[1];
}

if ($action_type == 'UPDATEDELIVERYSTATUS') 
{
    $gateway = get_preference('sms_gateway');
    if (!isset($config['gateways'][$gateway])) {
      die('Gateway setting error!');
    }
    $gw = (new SMSGateway())->initGateway($gateway, sms_setting($gateway));

    $filter_data = array(
    	'filter_process_status' => 1,
    	'filter_delivery_status' => 'pending',
    	'start' => 0,
    	'limit' => 30,
    );
    $sms_rows = $sms_model->getScheduleSms($filter_data);
    $total = 0;
    if (!empty($sms_rows)) {
	    foreach ($sms_rows as $sms) {
	    	$response_array = explode('||',$sms['response_text']);
	    	if (!isset($response_array[0]) || $response_array[0] != 1900) {
	    		$sms_model->updateDeliveryStatus($sms['id'], 'failed');
		      	$total++;
		      	continue;
	    	}
	    	$response_id = rtrim($response_array[2],'/');
	    	$response = $gw->deliveryStatus($response_id);
		    if ($response) {
		    	$response = $response == 2 ? 'delivered' : 'failed';
		      	$sms_model->updateDeliveryStatus($sms['id'], $response);
		      	$total++;
		    }
	    }
	    
	} else {
		die('No data');
	}
	echo $total;
	exit();
}

if ($action_type == 'PROCEEDSCHEDULESMS') 
{
    $gateway = get_preference('sms_gateway');
    if (!isset($config['gateways'][$gateway])) {
      die('Gateway setting error!');
    }
    $gw = (new SMSGateway())->initGateway($gateway, sms_setting($gateway));

    $sms_model = $registry->get('loader')->model('sms');
    $filter_data = array(
    	'start' => 0,
    	'limit' => 30,
    );
    $sms_rows = $sms_model->getScheduleSms($filter_data);
    // print_r($sms_rows);die;
    $total = 0;
    if (!empty($sms_rows)) {
	    foreach ($sms_rows as $sms) {
	    	$mobile_number = $sms['mobile_number'];
	    	$message = $sms['sms_text'];
	    	$response = $gw->send($mobile_number, $message);
	    	$response_array = explode('||',$response);
		    if (count($response_array) > 1) {
		      $sms_model->updateStatus($sms['id'], $response);
		      $total++;
		    }
	    }
	    
	} else {
		die('No data');
	}
	echo $total;
	exit();
}

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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'send_sms')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

// Validate post data
function validate_request_data($request, $language) {

	// People type validation
	if (!validateString($request->post['people_type'])) {
	  throw new Exception($language->get('error_people_type'));
	}

	// Campaign name validation
	if (!validateString($request->post['campaign_name'])) {
	  throw new Exception($language->get('error_campaign_name'));
	}

  	// Validate schedule_date
    if (!isItValidDate($request->post['schedule_date'])) {
      throw new Exception($language->get('error_schedule_date'));
    }

    // Validate schedule_time
    if (!isItValidTime12($request->post['schedule_time'])) {
      throw new Exception($language->get('error_schedule_time'));
    }

    // Message validation
	if (!validateString($request->post['message'])) {
	  throw new Exception($language->get('error_message'));
	}

	// People validation
	if (count($request->post['peoples']) <= 0) {
		throw new Exception($language->get('error_people_not_found'));
	}
}

// Check schedule existance by date, people type, campaign name
function validate_existance($request, $language, $id = 0)
{
  global $db;

  // Check, if box name exist or not
  $statement = $db->prepare("SELECT * FROM `sms_schedule` WHERE DATE(`schedule_datetime`) = ? AND `people_type` = ? AND `campaign_name` = ?");
  $statement->execute(array($request->post['schedule_date'], $request->post['people_type'], $request->post['campaign_name']));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_schedule_exist'));
  }
}

if ($request->server['REQUEST_METHOD'] == 'POST' && $action_type == 'SENDGROUP') 
{
	try {

		if (DEMO) {
			throw new Exception($language->get('error_disable_in_demo'));
		}

		validate_request_data($request, $language);
		validate_existance($request, $language);

		$message = $request->post['message'];
		$tc_model = $registry->get('loader')->model('tagconverter');
		$peoples = $request->post['peoples'];
		foreach ($peoples as $p) {
			$mobile_number = $p['mobile_number'] ? $p['mobile_number'] : '';
			if (!$mobile_number) {
				continue;
			}
			$cmessage = $tc_model->convert(array('[name]'), array('name' => $p['name']), $message);
			$data = array(
				'schedule_datetime' => date('Y-m-d H:i:s', strtotime($request->post['schedule_date'] . ' ' . $request->post['schedule_time'])),
				'store_id' => store_id(),
				'people_type' => $request->post['people_type'],
				'mobile_number' => $mobile_number,
				'people_name' => $p['name'],
				'sms_text' => $cmessage,
				'campaign_name' => $request->post['campaign_name'],
				'process_status' => 0,
				'created_at' => date_time(),
			);
			$sms_model->addSchedule($data);
		}

		header('Content-Type: application/json');
		echo json_encode(array('msg' => $language->get('text_success_sms_schedule')));
		exit();

	} catch (Exception $e) { 
	    
	    header('HTTP/1.1 422 Unprocessable Entity');
	    header('Content-Type: application/json; charset=UTF-8');
	    echo json_encode(array('errorMsg' => $e->getMessage()));
	    exit();
	}
}

if ($request->server['REQUEST_METHOD'] == 'POST' && $action_type == 'SENDINDIVIDUAL') 
{
	try {

		if (DEMO) {
			throw new Exception($language->get('error_disable_in_demo'));
		}

		$gateway = get_preference('sms_gateway');
		if (!isset($config['gateways'][$gateway])) {
			throw new Exception($language->get('error_gateway'));
		}

		$phone_number = $request->post['phone_number'];
		if (!$phone_number) {
			throw new Exception($language->get('error_phone_number'));
		}

		$message = $request->post['message'];
		if (!$message) {
			throw new Exception($language->get('error_sms_text'));
		}
		
		$gw = (new SMSGateway())->initGateway($gateway, sms_setting($gateway));
		$response = $gw->send($phone_number, $message);
		$response_array = explode('||',$response);
		if (empty($response_array)) {
			throw new Exception($language->get('error_sms_not_sent'));
		}
		$data = array(
			'schedule_datetime' => date('Y-m-d H:i:s'),
			'store_id' => store_id(),
			'people_type' => 'customer',
			'mobile_number' => $phone_number,
			'people_name' => 'annonymous',
			'sms_text' => $message,
			'campaign_name' => NULL,
			'process_status' => 0,
			'created_at' => date_time(),
		);
		$id = $sms_model->addSchedule($data);
		if (count($response_array) > 1) {
	      $sms_model->updateStatus($id, $response);
	    }

		header('Content-Type: application/json');
		echo json_encode(array('msg' => $language->get('text_success_sms_sent')));
		exit();

	} catch (Exception $e) { 
	    
	    header('HTTP/1.1 422 Unprocessable Entity');
	    header('Content-Type: application/json; charset=UTF-8');
	    echo json_encode(array('errorMsg' => $e->getMessage()));
	    exit();
	}
}

if ($request->server['REQUEST_METHOD'] == 'POST' && $action_type == 'RESEND') 
{
	try {

		if (DEMO) {
			throw new Exception($language->get('error_disable_in_demo'));
		}

		if (empty($request->post['mobile_number'])) {
			throw new Exception($language->get('error_mobile_number'));
		}
		$mobile_number = $request->post['mobile_number'];

		if (empty($request->post['sms_text'])) {
			throw new Exception($language->get('error_message'));
		}
		$message = $request->post['sms_text'];

		$id = $request->post['id'];

		$gateway = get_preference('sms_gateway');
		if (!isset($config['gateways'][$gateway])) {
			throw new Exception($language->get('error_gateway'));
		}

		$gw = (new SMSGateway())->initGateway($gateway, sms_setting($gateway));

		$response = $gw->send($mobile_number, $message);
		$response_array = explode('||',$response);
		if (empty($response_array)) {
			throw new Exception($language->get('error_sms_not_sent'));
		}
		$sms_model->updateSchedule($id, $request->post);
		if (count($response_array) > 1) {
	      $sms_model->updateStatus($id, $response);
	    }

		header('Content-Type: application/json');
		echo json_encode(array('msg' => $language->get('text_success_sms_sent'), 'id' => $id));
		exit();

	} catch (Exception $e) { 
	    
	    header('HTTP/1.1 422 Unprocessable Entity');
	    header('Content-Type: application/json; charset=UTF-8');
	    echo json_encode(array('errorMsg' => $e->getMessage()));
	    exit();
	}
}

if ($request->server['REQUEST_METHOD'] == 'POST' && $action_type == 'SEND') 
{
	try {

		if (DEMO) {
			throw new Exception($language->get('error_disable_in_demo'));
		}

		$invoice_id = $request->post['invoice_id'];
		$invoice_model = $registry->get('loader')->model('invoice');
		$invoice = $invoice_model->getInvoiceInfo($invoice_id);

		$gateway = get_preference('sms_gateway');
		if (!isset($config['gateways'][$gateway])) {
			throw new Exception($language->get('error_gateway'));
		}

		$phone_number = isset($request->post['phone_number']) ? $request->post['phone_number'] : '';
		if (!$phone_number) {
			$phone_number = $invoice['customer_mobile'] ? $invoice['customer_mobile'] : $invoice['mobile_number'];
		}

		$message = isset($request->post['message']) ? $request->post['message'] : '';
		if (!$message) {
			$message = $language->get('invoice_sms_text');
		}

		$tc_model = $registry->get('loader')->model('tagconverter');
		$invoice_price = $invoice_model->getSellingPrice($invoice_id);
		$data = $invoice + $invoice_price;
		$tags = array();
		foreach ($invoice as $key => $inv) {
	  		$tags[] = '['.$key.']';
	  	}
		$message = $tc_model->convert($tags, $data, $message);
		$gw = (new SMSGateway())->initGateway($gateway, sms_setting($gateway));

		$response = $gw->send($phone_number, $message);
		$response_array = explode('||',$response);
		if (empty($response_array)) {
			throw new Exception($language->get('error_sms_not_sent'));
		}
		$data = array(
			'schedule_datetime' => date('Y-m-d H:i:s'),
			'store_id' => store_id(),
			'people_type' => 'customer',
			'mobile_number' => $phone_number,
			'people_name' => $invoice['customer_name'],
			'sms_text' => $message,
			'campaign_name' => NULL,
			'process_status' => 0,
			'created_at' => date_time(),
		);
		$id = $sms_model->addSchedule($data);
		if (count($response_array) > 1) {
	      $sms_model->updateStatus($id, $response);
	    }

		header('Content-Type: application/json');
		echo json_encode(array('msg' => $language->get('text_success_sms_sent')));
		exit();

	} catch (Exception $e) { 
	    
	    header('HTTP/1.1 422 Unprocessable Entity');
	    header('Content-Type: application/json; charset=UTF-8');
	    echo json_encode(array('errorMsg' => $e->getMessage()));
	    exit();
	}
}

// SMS Form
if (isset($request->get['invoice_id']) AND $action_type == 'FORM') 
{
  	$tags = '';

  	$invoice_model = $registry->get('loader')->model('invoice');
	$invoice_id = $request->get['invoice_id'];
  	$invoice = $invoice_model->getInvoiceInfo($invoice_id);
  	foreach ($invoice as $key => $inv) {
  		$tags .= ' <kbd>['.$key.']</kbd>';
  	}
  	$invoice_price = $invoice_model->getSellingPrice($invoice_id);
  	foreach ($invoice_price as $key => $inv) {
  		$tags .= ' <kbd>['.$key.']</kbd>';
  	}
  	include '../template/invoice_sms_form.php';
  	exit();
}

// Resend SMS Form
if (isset($request->get['id']) AND $action_type == 'RESENDFORM') 
{
  	$row = $sms_model->getScheduleSmsRow($request->get['id']);
  	include ROOT.'/_inc/template/sms_resend_form.php';
  	exit();
}