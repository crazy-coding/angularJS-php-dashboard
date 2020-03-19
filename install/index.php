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

// Check PHP version
if (phpversion() < "7.0") {
	$errors[] = 'You are running PHP old version!';
} else {
	$phpversion = phpversion();
	$success[] = ' You are running PHP '.$phpversion;
}

// Check Mysql PHP extension
if(!extension_loaded('mysqli')) {
	$errors[] = 'Mysqli PHP extension unloaded!';
} else {
	$success[] = 'Mysqli PHP extension loaded.';
}

// Check PDO PHP extension
if (!defined('PDO::ATTR_DRIVER_NAME')) {
	$errors[] = 'PDO PHP extention is unloaded!';
} else {
	$success[] = 'PDO PHP extention loaded.';
}

// Check MBString PHP extension
if(!extension_loaded('mbstring')) {
	$errors[] = 'MBString PHP extension unloaded!';
} else {
	$success[] = 'MBString PHP extension loaded.';
}

// Check GD PHP extension
if(!extension_loaded('gd')) {
	$errors[] = 'GD PHP extension unloaded!';
} else {
	$success[] = 'GD PHP extension loaded.';
}

// Check CURL PHP extension
if(!extension_loaded('curl')) {
	$errors[] = 'CURL PHP extension unloaded!';
} else {
	$success[] = 'CURL PHP extension loaded.';
}

// Check Internet Connection
if(checkInternetConnection()) {
	$success[] = 'Internet connection OK';
} else {
	$errors[] = 'Internet connection problem!';
}

include '../_inc/template/install/index.php'; 

include("footer.php");