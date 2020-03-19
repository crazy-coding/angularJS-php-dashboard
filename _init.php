<?php
	// $tmezone should be at line 3 
	$timezone = 'Asia/Dhaka';

// date_default_timezone_set($timezone);
if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);

/*
 *---------------------------------------------------------------
 * SYSTEM ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 */
define('ENVIRONMENT', 'production');
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;
}

// Check PHP Version Number
if (version_compare(phpversion(), '5.6.0', '<') == true) {
	exit('PHP5.6+ Required');
}

// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Check If SSL or Not
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

// Load Config File
require_once __DIR__.DIRECTORY_SEPARATOR.'config.php';

// Auto Load Library
function autoload($class) {
	$file = DIR_INCLUDE . 'lib/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (file_exists($file)) {
		include($file);

		return true;
	} else {
		return false;
	}
}
spl_autoload_register('autoload');
spl_autoload_extensions('.php');

// Hooking System
require_once DIR_VENDOR . 'php-hooks/src/voku/helper/Hooks.php';

// Load Register
$registry = new Registry();

// Loader
$log = new Log('log.txt');
$registry->set('log', $log);

// Loader
$loader = new Loader($registry);
$registry->set('loader', $loader);

// DB CONFIG.
$dbhost = $sql_details['host'];
$dbname = $sql_details['db'];
$dbuser = $sql_details['user'];
$dbpass = $sql_details['pass'];

// Helper Functions
require_once DIR_HELPER . 'setting.php';
require_once DIR_HELPER . 'common.php';
require_once DIR_HELPER . 'countries.php';
require_once DIR_HELPER . 'file.php';
require_once DIR_HELPER . 'network.php';
require_once DIR_HELPER . 'pos.php';
require_once DIR_HELPER . 'box.php';
require_once DIR_HELPER . 'currency.php';
require_once DIR_HELPER . 'expense.php';
require_once DIR_HELPER . 'customer.php';
require_once DIR_HELPER . 'invoice.php';
require_once DIR_HELPER . 'pmethod.php';
require_once DIR_HELPER . 'product.php';
require_once DIR_HELPER . 'report.php';
require_once DIR_HELPER . 'store.php';
require_once DIR_HELPER . 'supplier.php';
require_once DIR_HELPER . 'user.php';
require_once DIR_HELPER . 'usergroup.php';
require_once DIR_HELPER . 'validator.php';
require_once DIR_HELPER . 'category.php';
require_once DIR_HELPER . 'expense_category.php';
require_once DIR_HELPER . 'unit.php';
require_once DIR_HELPER . 'taxrate.php';
require_once DIR_HELPER . 'giftcard.php';
require_once DIR_HELPER . 'banking.php';
require_once DIR_HELPER . 'bankaccount.php';
require_once DIR_HELPER . 'loan.php';

if (!defined('INSTALLED')) {
	header('Location: '.root_url().'/install/index.php', true, 302);
}

if (file_exists(ROOT.DIRECTORY_SEPARATOR.'.maintenance') && current_nav() != 'maintenance') {
	header('Location: '.root_url().'/maintenance.php', true, 302);
}

// Database Connection
try {
	// $db = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8",$dbuser,$dbpass);
	$db = new Database("mysql:host={$dbhost};dbname={$dbname};charset=utf8",$dbuser,$dbpass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
	die('Connection error: '.$e->getMessage());
}
$registry->set('db', $db);

// Request
$request = new Request();
$registry->set('request', $request);

// Session
$session = new Session($registry);
$registry->set('session', $session);

// Store
$store = new Store($registry);
$registry->set('store', $store);

// Timezone
$timezone = get_preference('timezone') ? get_preference('timezone') : $timezone;
if (!ini_get('date.timezone')) {
	if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
}

// User
$user = new User($registry);
$registry->set('user', $user);

// Language
$active_lang 	= $user->getPreference('language', 'english');
$language 	= new Language($active_lang);
$registry->set('language', $language);
$language->load('default');

// Set Curernct Store By Query String
if (isset($request->get['active_store_id'])) {

	try {

		$store_id = $request->get['active_store_id'];
		$belongsStores = $user->getBelongsStore();
		$store_ids = array();
		foreach ($belongsStores as $the_store) {
			$store_ids[] = $the_store['store_id'];
		}
	    if ($user->getGroupId() != 1 && !in_array($store_id, $store_ids)) {
	      throw new Exception($language->get('error_activate_permission'));
	      exit();
	    }
		$store->openTheStore($store_id);

		header('Content-Type: application/json');
	    echo json_encode(array('msg' => $language->get('text_activate_success')));
	    exit();

	} catch (Exception $e) { 

		header('HTTP/1.1 422 Unprocessable Entity');
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode(array('errorMsg' => $e->getMessage()));
		exit();
	}
}

// Set Curernct Store By Query String
if (isset($request->get['lang'])) {
	$preference = $user->getAllPreference();
	unset($preference['language']);
	$preference['language'] = $request->get['lang'];
	// dd($preference);
	$statement = $db->prepare("UPDATE `users` SET `preference` = ? WHERE `id` = ? ");
    $statement->execute(array(serialize($preference), user_id()));
    redirect(root_url());
}

// Functions
include ('functions.php');

// Device Detection
$detect = new mobiledetect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

// Document
$document = new Document($registry);
$document->setBodyClass();
$registry->set('document', $document);

// Currency
$currency = new Currency($registry);
$registry->set('currency', $currency);

// Datatable
require_once DIR_LIBRARY . 'ssp.class.php';