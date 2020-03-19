<?php 
ob_start();
session_start();
include ("_init.php");

if (defined('INSTALLED')) {
	header('Location: ../index.php');
}

//if (!check_pcode()) {
//	redirect('index.php');
//}

include("header.php"); 

$errors = array();
$success = array();
$info = array();

$errors['host'] 	= null;
$errors['user'] 	= null;
$errors['password'] = null;
$errors['database'] = null;
$errors['database_import'] = null;

function database_import() {

	global $request, $errors, $success, $info;

	ini_set('display_errors', 'On');

	$dbhost = trim($request->post['host']);
	$dbname = trim($request->post['database']);
	$dbuser = trim($request->post['user']);
	$dbpass = $request->post['password'];

	$info['username'] = get_pusername();
	$info['purchase_code'] = get_pcode();
	$info['domain'] = root_url();
	$info['ip'] = get_real_ip();
	$info['mac'] = json_encode(getMAC());
	$info['action'] = 'install';

    $apiCall = apiCall($info);
	if($apiCall->status == false) {
		$errors['database_import'] = 'Check internet connection.';
		return false;
	}

	if(empty($apiCall->schema)) {
		$errors['database_import'] = 'Schema not found.';
		return false;
	}

	$schemes = $apiCall->schema;

	$mysqli = @new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	$query = $schemes;
	// Check for errors
    if (mysqli_connect_errno()) {
    	$errors['database_import'] = 'Oop!, Something went wrong. Please check your input';
		return false;
    }
    $mysqli->multi_query($query);
    do {} while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli));

    $mysqli->close();

	// update db config
	$config_path = ROOT . '/config.php';

	@chmod($config_path, 0777);
	if (is_writable($config_path) === false) {
		$errors['database_import'] = 'Config file is unwritable';
		return false;
	} else {
		$file = $config_path;

		$line_host 	= "'host' => '". $dbhost ."',";
		$line_db 	= "'db' => '". $dbname ."',";
		$line_user 	= "'user' => '". $dbuser ."',";
		$line_pass 	= "'pass' => '". $dbpass ."'";

		$fileArray = array(6 => $line_host, 7 => $line_db, 8 => $line_user, 9 => $line_pass);

		replace_lines($file, $fileArray);
		@chmod($config_path, 0644);
		return true;
	}

	return true;
}

if ($request->server['REQUEST_METHOD'] == 'POST') {

	if (empty($request->post['host'])) {
		$errors['host'] = 'Host field is required.';
	}

	if (empty($request->post['user'])) {
		$errors['user'] = 'Username field is required.';
	}

	if (empty($request->post['database'])) {
		$errors['database'] = 'Database field is required.';
	}

	if(!$errors['host'] 
		&& !$errors['user'] 
		&& !$errors['password'] 
		&& !$errors['database'] 
		&& database_import()) {

		redirect('timezone.php');
	}
}
?>

<?php include '../_inc/template/install/database.php'; ?>

<?php include("footer.php"); ?>
