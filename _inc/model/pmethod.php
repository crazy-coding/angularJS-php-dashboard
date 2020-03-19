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
class ModelPmethod extends Model 
{
	public function addPmethod($data) 
	{
    	$statement = $this->db->prepare("INSERT INTO `pmethods` (name, details, created_at) VALUES (?, ?, ?)");
    	$statement->execute(array($data['pmethod_name'], $data['pmethod_details'], date_time()));

    	$pmethod_id = $this->db->lastInsertId();

    	if (isset($data['pmethod_store'])) {
			foreach ($data['pmethod_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `pmethod_to_store` SET `ppmethod_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$pmethod_id, (int)$store_id));
			}
		}

		$this->updateStatus($pmethod_id, $data['status']);
		$this->updateSortOrder($pmethod_id, $data['sort_order']);

    	return $pmethod_id;
	}

	public function updateStatus($pmethod_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `pmethod_to_store` SET `status` = ? WHERE `store_id` = ? AND `ppmethod_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$pmethod_id));
	}

	public function updateSortOrder($pmethod_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `pmethod_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `ppmethod_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$pmethod_id));
	}

	public function editPmethod($pmethod_id, $data) 
	{    	
    	$statement = $this->db->prepare("UPDATE `pmethods` SET `name` = ?, `details` = ? WHERE `pmethod_id` = ? ");
    	$statement->execute(array($data['pmethod_name'], $data['pmethod_details'], $pmethod_id));

    	// Delete store data balongs to the pmethod
    	$statement = $this->db->prepare("DELETE FROM `pmethod_to_store` WHERE `ppmethod_id` = ?");
    	$statement->execute(array($pmethod_id));
		
		// Insert pmethod into store
    	if (isset($data['pmethod_store'])) {
			foreach ($data['pmethod_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `pmethod_to_store` SET `ppmethod_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$pmethod_id, (int)$store_id));
			}
		}

		$this->updateStatus($pmethod_id, $data['status']);
		$this->updateSortOrder($pmethod_id, $data['sort_order']);

    	return $pmethod_id;
	}

	public function deletePmethod($pmethod_id) 
	{    	
    	$statement = $this->db->prepare("DELETE FROM `pmethods` WHERE `pmethod_id` = ? LIMIT 1");
    	$statement->execute(array($pmethod_id));	

    	$statement = $this->db->prepare("DELETE FROM `pmethod_to_store` WHERE `ppmethod_id` = ?");
    	$statement->execute(array($pmethod_id));

        return $pmethod_id;
	}

	public function getPmethod($pmethod_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

	    $statement = $this->db->prepare("SELECT `pmethods`.*, `pay2s`.`status`, `pay2s`.`sort_order` 
	    	FROM `pmethods` 
	    	LEFT JOIN `pmethod_to_store` as pay2s ON (`pmethods`.`pmethod_id` = `pay2s`.`ppmethod_id`)  
	    	WHERE `pay2s`.`store_id` = ? AND `pmethods`.`pmethod_id` = ?");
	    $statement->execute(array($store_id, $pmethod_id));
	    $pmethod = $statement->fetch(PDO::FETCH_ASSOC);

	    // Fetch stores related to pmethods
	    $statement = $this->db->prepare("SELECT `store_id` FROM `pmethod_to_store` WHERE `ppmethod_id` = ?");
	    $statement->execute(array($pmethod_id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $pmethod['stores'] = $stores;

	    return $pmethod;
	}

	public function getPmethods($data = array(), $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `pmethods` LEFT JOIN `pmethod_to_store` pay2s ON (`pmethods`.`pmethod_id` = `pay2s`.`ppmethod_id`) WHERE `pay2s`.`store_id` = ? AND `pay2s`.`status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `name` LIKE '" . $data['filter_name'] . "%'";
		}

		$sql .= " GROUP BY `pmethods`.`pmethod_id`";

		$sort_data = array(
			'name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `pay2s`.`sort_order`";
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

	public function getBelongsStore($pmethod_id)
	{
		$statement = $this->db->prepare("SELECT * FROM `pmethod_to_store` WHERE `ppmethod_id` = ?");
		$statement->execute(array($pmethod_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);

	}
}