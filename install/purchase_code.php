<?php 
ob_start();
session_start();
include ("_init.php");

if (defined('INSTALLED')) {
	header('Location: ../index.php');
}

include("header.php"); 

$errors = array();
$success = array();
$info = array();

$errors['internet_connection'] = null;
$errors['purchase_username'] = null;
$errors['purchase_code'] = null;
$errors['config_error'] = null;

$config_path = ROOT . '/config.php';

// // purchage name validation
// function pname_validation() {

// 	global $request, $config_path, $errors, $success, $info;

// 	if (empty($request->post['purchase_username'])) {
// 		$errors['purchase_username'] = 'Purchase username is required';
// 		return false;
// 	}

// 	$file = DIR_INCLUDE.'config/purchase.php';

// 	if (is_writable($config_path) === false) {
// 		$errors['config_error'] = 'config.php is not writable!';
// 	}

// 	$info['username'] = trim($request->post['purchase_username']);
// 	$info['purchase_code'] = trim($request->post['purchase_code']);

// 	$apiCall = apiCall($info);

// 	if($apiCall->status == 'error') {
// 		if($apiCall->for == 'username') {
// 			$errors['purchase_username'] = $apiCall->message;
// 			return false;
// 		}
// 		return true;
// 	}
// 	return true;
// }



function pcode_validation() {

	global $request, $config_path, $errors, $success, $info;

	if (empty($request->post['purchase_username'])) {
		$errors['purchase_username'] = 'Purchase username is required';
		return false;
	}

	if (empty($request->post['purchase_code'])) {
		$errors['purchase_code'] = 'Purchase code is required';
		return false;
	}

	$file = DIR_INCLUDE.'config/purchase.php';

	if (is_writable($config_path) === false) {
		$errors['config_error'] = 'config.php is not writable!';
		return false;
	}

	$info['username'] = trim($request->post['purchase_username']);
	$info['purchase_code'] = trim($request->post['purchase_code']);

	$apiCall = apiCall($info);
    if($apiCall->status === 'error') {
		$errors['purchase_code'] = $apiCall->message;
		return false;
	} else {
		$uac = json_encode(array(trim($request->post['purchase_username']), trim($request->post['purchase_code'])));
		@chmod($file, FILE_WRITE_MODE);
		$purchase_file = file_get_contents($file);
		write_file($file, $uac);
		return true;
	}
}



if ($request->server['REQUEST_METHOD'] == 'POST') {

	if(!checkInternetConnection()) {
		$errors['internet_connection'] = 'Internet connection problem!';
	}

	// pname_validation();
	pcode_validation();

	if(!$errors['config_error'] && !$errors['internet_connection'] && !$errors['purchase_username'] && !$errors['purchase_code']) {

		redirect('database.php');
	}
}
?>

<?php include '../_inc/template/install/purchase_code.php'; ?>

<?php include("footer.php"); ?>
