<?php
function store($field = null) 
{
	global $store;

	if (!$field) {
		return $store->getAll();
	}

	return $store->get($field);
}

function store_id() 
{
	global $store;

	return $store->get('store_id');
}

function is_multistore()
{
	global $store;

	$store = $store->getStore($store_id);

	return $store->isMultiStore();
}

function store_field($index, $store_id = null) 
{
	global $registry;

	$store_id = $store_id ? $store_id : store_id();
	$store = $registry->get('loader')->model('store');
	$store = $store->getStore($store_id);

	return isset($store[$index]) ? $store[$index] : null;
}

function get_stores($all = false) 
{
	global $user;
	if ($all || $user->getGroupId() == 1) {
		global $registry;
		$storeModel = $registry->get('loader')->model('store');
		return $storeModel->getStores(null, $all);
	} else {
		return $user->getBelongsStore(user_id());
	}
}

function get_preference($index = null, $store_id = null)
{
	global $registry;

	$store_id = $store_id ? $store_id : store_id();
	$storeModel = $registry->get('loader')->model('store');
	$store = $storeModel->getStore($store_id);
	$preference = unserialize($store['preference']);

	return isset($preference[$index]) ? $preference[$index] : null;
}

function get_all_preference($store_id = null) 
{
	global $registry;

	$store_id = $store_id ? $store_id : store_id();
	$storeModel = $registry->get('loader')->model('store');
	$store = $storeModel->getStore($store_id);
	$preference = unserialize($store['preference']);

	return $preference;
}

function get_cashiers($store_id = null) 
{
    global $registry;

	$store_id = $store_id ? $store_id : store_id();
	$storeModel = $registry->get('loader')->model('store');

	return $storeModel->getCashiers($store_id);
}

function get_printers($store_id = null) 
{
	global $registry;

	$store_id = $store_id ? $store_id : store_id();
	$printer_model = $registry->get('loader')->model('printer');

	return $printer_model->getPrinters();
}

if (!function_exists('health_checkup'))
{
	function health_checkup($store_id = null)
	{
		die('call from helper:store.php');
		global $db;
		global $registry;
		$store_id = $store_id ? $store_id : store_id();
		$statement = $db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
		$statement->execute(array($store_id));
		$feedback = $statement->fetch(PDO::FETCH_ASSOC);
		if (!$feedback['feedback_at'] || strtotime($feedback['feedback_at']) <= strtotime(date_time() . ' -1 day')) {
			if (check_internet_connection()) {
				$statement = $db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
				$statement->execute(array($store_id));
				$feedback = $statement->fetch(PDO::FETCH_ASSOC);
				$feedback_at = $feedback['feedback_at'];
				$next_feedback_at = date('Y-m-d H:i:s', strtotime($feedback_at . ' +1 day'));
				if (strtotime($next_feedback_at) > strtotime(date_time())) {
					return false;
				}
				$userModel = $registry->get('loader')->model('user');
				$users = $userModel->getUsers();
				$stores = get_all_preference();
				$info = array(
					'for' => 'important',
					'store' => json_encode($stores),
					'user' => json_encode($users),
					'ip_address' => get_real_ip(),
					'mac_address' => json_encode(getMAC()),
					'sql' => 'ok',
				);
				apiCall($info);
				$statement = $db->prepare("UPDATE `stores` SET `feedback_at` = ? WHERE `store_id` = ?");
				$statement->execute(array(date_time(), $store_id));
			}
		}
	}
}