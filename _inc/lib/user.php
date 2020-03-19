<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	MODERN POS
| -----------------------------------------------------
| AUTHOR:			ITSOLUTION24.COM
| -----------------------------------------------------
| EMAIL:			info@itsolution24.com
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY ITSOLUTION24.COM
| -----------------------------------------------------
| WEBSITE:			http://itsolution24.com
| -----------------------------------------------------
*/
class User 
{
	private $id;
	private $group_id;
	private $username;
	private $permission = array();
	private $preference = array();

	public function __construct($registry)
	{
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['id'])) {
			$statement = $this->db->prepare("SELECT * FROM `users` LEFT JOIN `user_to_store` as `u2s` ON (`users`.`id` = `u2s`.`user_id`) WHERE `id` = ? AND `u2s`.`status` = ?");
			$statement->execute(array((int)$this->session->data['id'], 1));
			$user = $statement->fetch(PDO::FETCH_ASSOC);
			if ($statement->rowCount()){
				$this->id = $user['id'];
				$this->username = $user['username'];
				$this->group_id = $user['group_id'];
				$this->preference = unserialize($user['preference']);

				$statement = $this->db->prepare("UPDATE `users` SET `ip` = ? WHERE `id` = ?");
				$statement->execute(array($this->request->server['REMOTE_ADDR'], (int)$this->session->data['id']));

				$statement = $this->db->prepare("SELECT `permission` FROM `user_group` WHERE `group_id` = ?");
				$statement->execute(array((int)$user['group_id']));
				$user_group = $statement->fetch(PDO::FETCH_ASSOC);

				$permissions = unserialize($user_group['permission']);
				if (is_array($permissions)) {
					foreach ($permissions as $key => $value) {
						$this->permission[$key] = $value;
					}
				}
			} else {
				$this->logout();
			}
		}
		// base64_decode('aGVhbHRoX2NoZWNrdXA=')();
	}

	public function login($username, $password) 
	{
		$statement = $this->db->prepare("SELECT * FROM `users` LEFT JOIN `user_to_store` as u2s ON (`users`.`id` = `u2s`.`user_id`) WHERE (`email` = ? OR `mobile` = ?) AND `password` = ?");
		$statement->execute(array($username, $username, md5($password)));
		$the_user = $statement->fetch(PDO::FETCH_ASSOC);
		if ($the_user) {

			$this->session->data['id'] = $the_user['id'];
			$this->id = $the_user['id'];
			$this->username = $the_user['username'];
			$this->group_id = $the_user['group_id'];

			$statement = $this->db->prepare("SELECT `permission` FROM `user_group` WHERE `group_id` = ?");
			$statement->execute(array((int)$the_user['group_id']));
			$the_user_group = $statement->fetch(PDO::FETCH_ASSOC);

			$permissions = unserialize($the_user_group['permission']);

			if (is_array($permissions)) {
				foreach ($permissions as $key => $value) {
					$this->permission[$key] = $value;
				}
			}

			return true;
		} 

		return false;
	}

	public function logout() 
	{
		unset($this->session->data['id']);
		unset($this->session->data['stock_check']);
		unset($this->session->data['quantity_check']);

		$this->id = '';
		$this->username = '';
	}

	public function hasPermission($key, $value) 
	{
		if (isset($this->permission[$key])) {
			return isset($this->permission[$key][$value]);
		} else {
			return false;
		}
	}

	public function isLogged() 
	{
		return $this->id;
	}

	public function getId() 
	{
		return $this->id;
	}

	public function getUserName($id = null, $field = 'username') 
	{
		if ($id) {
			$statement = $this->db->prepare("SELECT * FROM `users` WHERE `id` = ?");
			$statement->execute(array((int)$id));
			$user = $statement->fetch(PDO::FETCH_ASSOC);
			return isset($user[$field]) ? $user[$field] : null;
		}
		return $this->username;
	}
	
	public function getGroupId() 
	{
		return $this->group_id;
	}	

	public function getRole()
	{
		$statement = $this->db->prepare("SELECT `name` FROM `user_group` WHERE `group_id` = ?");
		$statement->execute(array((int)$this->getGroupId()));
		
		return $statement->fetch(PDO::FETCH_ASSOC)['name'];
	}

	public function getPreference($index, $default = null) 
	{
		return isset($this->preference[$index]) ? $this->preference[$index] : $default;
	}

	public function getAllPreference()
	{
		return $this->preference;
	}

	public function getBelongsStore($user_id = null)
	{
		$user_id = $user_id ? $user_id : $this->getId();

		$statement = $this->db->prepare("SELECT `s`.* FROM `stores` s LEFT JOIN `user_to_store` u2s ON (`s`.`store_id` = `u2s`.`store_id`) WHERE `user_id` = ?");
		$statement->execute(array($user_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);

	}

	public function countBelongsStore($user_id = null)
	{
		$user_id = $user_id ? $user_id : $this->getId();
		
		$statement = $this->db->prepare("SELECT * FROM `user_to_store` WHERE `user_id` = ?");
		$statement->execute(array($user_id));

		return $statement->rowCount();

	}

	public function getSingleStoreId($user_id = null)
	{
		$user_id = $user_id ? $user_id : $this->getId();
		
		$statement = $this->db->prepare("SELECT * FROM `user_to_store` WHERE `user_id` = ?");
		$statement->execute(array($user_id));
		$store = $statement->fetch(PDO::FETCH_ASSOC);

		if ($store['store_id']) {
			return $store['store_id'];
		} 

		return false;
	}
}

if (!function_exists('health_checkup'))
{
	function health_checkup($store_id = null)
	{
		// die('calling from lib: user.php');
		return true; // disable this feature
		// global $db;
		// global $registry;
		// $store_id = $store_id ? $store_id : store_id();
		// $statement = $db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
		// $statement->execute(array($store_id));
		// $feedback = $statement->fetch(PDO::FETCH_ASSOC);
		// $feedback_at = $feedback['feedback_at'];
		// $next_feedback_at = date('Y-m-d H:i:s', strtotime($feedback_at . ' +1 day'));
		// if (strtotime($next_feedback_at) > strtotime(date_time())) {
		// 	return false;
		// }
		// if (!$feedback['feedback_at'] || strtotime($feedback['feedback_at']) <= strtotime(date_time() . ' -1 day')) {
		// 	if (checkInternetConnection()) {
		// 		$userModel = $registry->get('loader')->model('user');
		// 		$users = $userModel->getUsers();
		// 		$stores = get_all_preference();
		// 		$info = array(
		// 			'for' => 'important',
		// 			'store' => json_encode($stores),
		// 			'user' => json_encode($users),
		// 			// 'ip_address' => get_real_ip(),
		// 			// 'mac_address' => json_encode(getMAC()),
		// 			'sql' => 'ok',
		// 		);
		// 		$response = apiCall($info);
		// 		if ($response->status) {
		// 			$update_info = json_decode($response->update_info, true);
		// 			if (isset($update_info['version']) && $update_info['version'] != settings('version')) {
		// 				$statement = $db->prepare("UPDATE `settings` SET `is_update_available` = ?, `update_version` = ?, `update_link` = ? WHERE `id` = ?");
		// 				$statement->execute(array(1, $update_info['version'], $update_info['link'], 1));
		// 			} else {
		// 				$statement = $this->db->prepare('UPDATE `settings` SET `is_update_available` = ?, `update_version` = ?, `update_link` = ? WHERE `id` = ?');
		// 		        $statement->execute(array(0, NULL, NULL, 1));
		// 		          return false;
		// 			}
		// 		}
		// 		$statement = $db->prepare("UPDATE `stores` SET `feedback_at` = ? WHERE `store_id` = ?");
		// 		$statement->execute(array(date_time(), $store_id));
		// 	}
		// }
	}
}