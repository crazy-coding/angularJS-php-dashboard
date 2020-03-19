<?php 
ob_start();
session_start();
include ("_init.php");

if(!check_pcode()) {
	redirect("purchase_code.php");
}


include("header.php"); 

$errors = array();
$success = array();
$info = array();

$errors['timezone'] = null;
$errors['index_validation'] = null;

if($session->data['admin_username'] && $session->data['password']) {

	if ($request->server['REQUEST_METHOD'] == 'POST') {
		$session->destroy();
		header('Location: ../index.php');
	}

} else {
	redirect("site.php");
}
?>

<?php include '../_inc/template/install/done.php'; ?>

<?php include("footer.php"); ?>
