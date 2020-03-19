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

$errors['timezone'] = null;
$errors['index_validation'] = null;

if(!checkDBConnection()) {
	redirect("database.php");
}

function set_timezone($timezone) {

	global $request;

	$index_path = ROOT . '/_init.php';

	@chmod($index_path, 0777);
	if (is_writable($index_path) === false) {
		$errors['index_validation'] = 'Init file is unwritable';
		return false;
	} else {
		$file = $index_path;
		$filecontent = "$" . "timezone = '". $timezone ."';";
		$fileArray = array(3 => $filecontent);
		replace_lines($file, $fileArray);
		@chmod($index_path, 0644);
		return true;
	}
}

if ($request->server['REQUEST_METHOD'] == 'POST') {

	if (!isset($request->post['timezone']) || empty($request->post['timezone'])) {

		$errors['timezone'] = 'Timezone field is required.';

	} else {

		$timezone = $request->post['timezone'];
		set_timezone($timezone);

		if(!$errors['timezone'] || !$errors['index_validation']) {
			$session->data['timezone'] = $timezone;
			redirect('site.php');
		}
	}
}
?>

<?php include '../_inc/template/install/timezone.php'; ?>

<?php include("footer.php"); ?>
