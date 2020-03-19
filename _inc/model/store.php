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
class ModelStore extends Model 
{
	public function addStore($data) 
	{
		$statement = $this->db->prepare("INSERT INTO `stores` (name, mobile, country, vat_reg_no, zip_code, cashier_id, address, logo, favicon, sound_effect, status, sort_order, receipt_printer, remote_printing, auto_print, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$statement->execute(array($data['name'], $data['mobile'], $data['country'], $data['vat_reg_no'], $data['zip_code'], $data['cashier_id'], $data['address'], $data['logo'], $data['favicon'], $data['sound_effect'], $data['status'], $data['sort_order'], $data['receipt_printer'], $data['remote_printing'], $data['auto_print'], date_time()));

		return $this->db->lastInsertId();    
	}

	public function editStore($store_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `stores` SET `name` = ?, `mobile` = ?, `country` = ?, `vat_reg_no` = ?,  `zip_code` = ?, `cashier_id` = ?, `address` = ?, `sound_effect` = ?, `status` = ?, `sort_order` = ?, `receipt_printer` = ?, `remote_printing` = ?, `auto_print` = ? WHERE `store_id` = ? ");
    	$statement->execute(array($data['name'], $data['mobile'], $data['country'], $data['vat_reg_no'], $data['zip_code'], $data['cashier_id'], $data['address'], $data['sound_effect'], $data['status'], $data['sort_order'], $data['receipt_printer'], $data['remote_printing'], $data['auto_print'], $store_id));

    	return $store_id;
	}

	public function editPreference($store_id, $preference = array())
	{
		if (empty($preference)) {
			$preference = array();
		}
		
		// Update timezone in _init.php

		$timezone = $preference['timezone'];
		$index_path = ROOT . '/_init.php';
		@chmod($index_path, 0777);
		if (is_writable($index_path) === false) {
			throw new Exception($language->get('timezone_not_change_in_init.php'));
			return false;
		} else {
			$file = $index_path;
			$filecontent = "$" . "timezone = '". $timezone ."';";
			$fileArray = array(3 => $filecontent);
			replace_lines($file, $fileArray);
			@chmod($index_path, 0644);
		}

		$statement = $this->db->prepare("UPDATE `stores` SET `preference` = ? WHERE `store_id` = ?");
    	$statement->execute(array(serialize($preference), $store_id));

    	return $store_id;
	}

	public function deleteStore($store_id) 
	{
    	$statement = $this->db->prepare("DELETE FROM `stores` WHERE `store_id` = ? LIMIT 1");
    	$statement->execute(array($store_id));

        return $store_id;
	}

	public function getStore($store_id) 
	{
		$statement = $this->db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
	  	$statement->execute(array($store_id));

	  	return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getStores($data = array()) 
	{
		$sql = "SELECT * FROM `stores`";

		if (isset($data['filter_name'])) {
			$sql .= " AND `name` LIKE '" . $data['filter_name'] . "%'";
		}

		$sql .= " GROUP BY `store_id`";

		$sort_data = array(
			'name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `store_id`";
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
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function total() 
	{
		$statement = $this->db->prepare("SELECT * FROM `stores` WHERE `status` = ?");
		$statement->execute(array(1));

		return $statement->rowCount();
	}

	public function getCashiers($store_id) 
	{
		$statement = $this->db->prepare("SELECT * FROM `users`
		LEFT JOIN `user_to_store` as u2s ON (`users`.`id` = `u2s`.`user_id`) 
		LEFT JOIN `user_group` as ug ON (`users`.`group_id` = `ug`.`group_id`) 
		 WHERE `store_id` = ? AND `ug`.`slug` = ?");
	  	$statement->execute(array($store_id, 'cashier'));

	  	return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}