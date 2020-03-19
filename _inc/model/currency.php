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
class ModelCurrency extends Model 
{
	public function addCurrency($data) 
	{
	   	$statement = $this->db->prepare("INSERT INTO `currency` (title, code, symbol_left, symbol_right, decimal_place, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['title'], $data['code'], $data['symbol_left'], $data['symbol_right'], $data['decimal_place'], date('Y-m-d H:i:s')));

    	$currency_id = $this->db->lastInsertId();

    	if (isset($data['currency_store'])) {
			foreach ($data['currency_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `currency_to_store` SET `currency_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$currency_id, (int)$store_id));
			}
		}

		$this->updateStatus($currency_id, $data['status']);
		$this->updateSortOrder($currency_id, $data['sort_order']);

    	return $currency_id;     
	}

	public function updateStatus($currency_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `currency_to_store` SET `status` = ? WHERE `store_id` = ? AND `currency_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$currency_id));
	}

	public function updateSortOrder($currency_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `currency_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `currency_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$currency_id));
	}

	public function editCurrency($currency_id, $data) 
	{ 
    	$statement = $this->db->prepare("UPDATE `currency` SET `title` = ?, `code` = ?, `symbol_left` = ?, `symbol_right` = ?, `decimal_place` = ? WHERE `currency_id` = ? ");
    	$statement->execute(array($data['title'], $data['code'], $data['symbol_left'], $data['symbol_right'], $data['decimal_place'], $currency_id));

    	// delete store data balongs to the currency
    	$statement = $this->db->prepare("DELETE FROM `currency_to_store` WHERE `currency_id` = ?");
    	$statement->execute(array($currency_id));
		
		// insert currency into store
    	if (isset($data['currency_store'])) {
			foreach ($data['currency_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `currency_to_store` SET `currency_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$currency_id, (int)$store_id));
			}
		}

		$this->updateStatus($currency_id, $data['status']);
		$this->updateSortOrder($currency_id, $data['sort_order']);

    	return $currency_id;
	}

	public function deleteCurrency($currency_id) 
	{    	
    	$statement = $this->db->prepare("DELETE FROM `currency` WHERE `currency_id` = ? LIMIT 1");
    	$statement->execute(array($currency_id));

    	$statement = $this->db->prepare("DELETE FROM `currency_to_store` WHERE `currency_id` = ?");
    	$statement->execute(array($currency_id));	

        return $currency_id;
	}

	public function getCurrency($currency_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

	    $statement = $this->db->prepare("SELECT `currency`.*, `c2s`.`status`, `c2s`.`sort_order` 
	    	FROM `currency` 
	    	LEFT JOIN `currency_to_store` as c2s ON (`currency`.`currency_id` = `c2s`.`currency_id`)  
	    	WHERE `c2s`.`store_id` = ? AND `currency`.`currency_id` = ?");
	    $statement->execute(array($store_id, $currency_id));
	    $currency = $statement->fetch(PDO::FETCH_ASSOC);

	    // fetch stores related to currency
	    $statement = $this->db->prepare("SELECT `store_id` FROM `currency_to_store` WHERE `currency_id` = ?");
	    $statement->execute(array($currency_id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $currency['stores'] = $stores;

	    return $currency;

	}

	public function getCurrencies($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `currency` LEFT JOIN `currency_to_store` c2s ON (`currency`.`currency_id` = `c2s`.`currency_id`) WHERE `c2s`.`store_id` = ? AND `c2s`.`status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `title` LIKE '" . $data['filter_name'] . "%'";
		}

		$sql .= " GROUP BY `currency`.`currency_id`";

		$sort_data = array(
			'title'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY title";
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