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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_pos_receipt_template')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

// Load Language File
$language->load('pos');
$store_id = store_id();
$user_id = user_id();

// Template edit form
if (isset($request->get['template_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') 
{
  // $statement = $db->prepare("SELECT `content` FROM `pos_receipt_template` WHERE `store_id` = ? AND `is_active` = ?");
  // $statement->execute(array($store_id, 1));
  // $template = $statement->fetch(PDO::FETCH_ASSOC);
  // echo $template['content'];

  require(DIR_INCLUDE.'template/receipt_template/classic.php');
  exit();
}