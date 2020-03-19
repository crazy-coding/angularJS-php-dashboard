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
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'pos_print')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_print_permission')));
  exit();
}

//  Load Language File
$language->load('pos');

// LOAD ESCPOS LIBRARY
$escpos = new Escpos();

// PRINT DATA
$data = json_decode($_GET['data']);
// dd($data);
$escpos->load($data->printer);
$escpos->print_receipt($data);