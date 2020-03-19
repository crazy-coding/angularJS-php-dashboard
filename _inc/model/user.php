<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	Modern POS
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
class ModelUser extends Model 
{
	public function addUser($data) 
	{
    	$statement = $this->db->prepare("INSERT INTO `users` (username, email, mobile, password, raw_password, group_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['username'], $data['email'], $data['mobile'], md5($data['password']), $data['password'], (int)$data['group_id'], date('Y-m-d H:i:s')));

    	$id = $this->db->lastInsertId();

    	if (isset($data['user_store'])) {
			foreach ($data['user_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `user_to_store` SET `user_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$id, (int)$store_id));
			}
		}

		$this->updateStatus($id, $data['status']);
		$this->updateSortOrder($id, $data['sort_order']);
    
    	return $id;    
	}

	public function updateStatus($id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `user_to_store` SET `status` = ? WHERE `store_id` = ? AND `user_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$id));
	}

	public function updateSortOrder($id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `user_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `user_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$id));
	}		

	public function editUser($id, $data) 
	{    	
    	$statement = $this->db->prepare("UPDATE `users` SET `username` = ?, `email` = ?, `mobile` = ?, `group_id` = ? WHERE `id` = ? ");
    	$statement->execute(array($data['username'], $data['email'], $data['mobile'], (int)$data['group_id'], $id));


    	// delete store data balongs to the user
    	$statement = $this->db->prepare("DELETE FROM `user_to_store` WHERE `user_id` = ?");
    	$statement->execute(array($id));
		
		// insert user into store
    	if (isset($data['user_store'])) {
			foreach ($data['user_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `user_to_store` SET `user_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$id, (int)$store_id));
			}
		}

		$this->updateStatus($id, $data['status']);
		$this->updateSortOrder($id, $data['sort_order']);
    
    	return $id;
	}

	public function deleteUser($id) 
	{    	
    	$statement = $this->db->prepare("DELETE FROM `users` WHERE `id` = ? LIMIT 1");
    	$statement->execute(array($id));

    	$statement = $this->db->prepare("DELETE FROM `user_to_store` WHERE `user_id` = ?");
    	$statement->execute(array($id));

        return $id;
	}

	public function getUser($id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT `users`.*, `ug`.`slug` as `group_name`, `ug`.`sort_order` FROM `users`
			LEFT JOIN `user_to_store` as u2s ON (`users`.`id` = `u2s`.`user_id`)  
			LEFT JOIN `user_group` as ug ON (`users`.`group_id` = `ug`.`group_id`)  
	    	WHERE `u2s`.`store_id` = ? AND `users`.`id` = ?");
	  	$statement->execute(array($store_id, $id));
		$user = $statement->fetch(PDO::FETCH_ASSOC);

		// fetch stores related to users
	    $statement = $this->db->prepare("SELECT `store_id` FROM `user_to_store` WHERE `user_id` = ?");
	    $statement->execute(array($id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $user['stores'] = $stores;

	    return $user;
	}

	public function getUsers($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `users` LEFT JOIN `user_to_store` as `u2s` ON (`users`.`id` = `u2s`.`user_id`) 
			WHERE `u2s`.`store_id` = ? AND `u2s`.`status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `username` LIKE '" . $data['filter_name'] . "%'";
		}

		if (isset($data['filter_email'])) {
			$sql .= " AND `email` LIKE '" . $data['filter_email'] . "%'";
		}

		if (isset($data['filter_mobile'])) {
			$sql .= " AND `mobile` LIKE '" . $data['filter_mobile'] . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND `u2s`.`status` = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY `id`";

		$sort_data = array(
			'username'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `id`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$statement = $this->db->prepare($sql);
		$statement->execute(array($store_id, 1));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}