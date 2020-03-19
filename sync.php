<?php
require_once("config.php");
require_once("_inc/helper/common.php");
require_once("_inc/helper/file.php");
require_once("_inc/helper/network.php");

if (isLocalhost()) {
	echo json_encode(array(
		'status' => false,
		'message' => 'Invalid Action',
		'for' => 'invalid',
	));
	exit();
};

$post_data 		= json_decode(file_get_contents('php://input'), true);
$action 		= isset($post_data['action']) ? $post_data['action'] : null;
$query_data 	= isset($post_data['data']) ? json_decode($post_data['data'],true) : null;

switch ($action) {
	case 'sync':

		$db = pdo_start();

	    foreach ($query_data as $sql) {
	      $statement = $db->prepare($sql['sql']);
	      $statement->execute($sql['args']);
	    }

		echo json_encode(array(
			'status' => true,
			'message' => 'sync successful',
			'for' => 'sync',
		));
		break;

	default:
		echo json_encode(array(
			'status' => false,
			'message' => 'Invalid Action',
			'for' => 'invalid',
		));
		break;
}
