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
class ModelUnit extends Model 
{
	public function addUnit($data) 
	{		
    	$statement = $this->db->prepare("INSERT INTO `units` (unit_name, unit_details) VALUES (?, ?)");
    	$statement->execute(array($data['unit_name'], $data['unit_details']));

    	$unit_id = $this->db->lastInsertId();

    	if (isset($data['unit_store'])) {
			foreach ($data['unit_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `unit_to_store` SET `uunit_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$unit_id, (int)$store_id));
			}
		}


		$this->updateStatus($unit_id, $data['status']);
		$this->updateSortOrder($unit_id, $data['sort_order']);

    	return $unit_id; 
	}

	public function updateStatus($unit_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `unit_to_store` SET `status` = ? WHERE `store_id` = ? AND `uunit_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$unit_id));
	}

	public function updateSortOrder($unit_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `unit_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `uunit_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$unit_id));
	}

	public function editUnit($unit_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `units` SET `unit_name` = ?, `unit_details` = ? WHERE unit_id = ? ");
    	$statement->execute(array($data['unit_name'], $data['unit_details'], $unit_id));
		
		// Insert unit into store
    	if (isset($data['unit_store'])) {

			foreach ($data['unit_store'] as $store_id) {

				$statement = $this->db->prepare("SELECT * FROM `unit_to_store` WHERE `store_id` = ? AND `uunit_id` = ?");
			    $statement->execute(array($store_id, $unit_id));
			    $unit = $statement->fetch(PDO::FETCH_ASSOC);
			    if (!$unit) {
			    	$statement = $this->db->prepare("INSERT INTO `unit_to_store` SET `uunit_id` = ?, `store_id` = ?");
					$statement->execute(array((int)$unit_id, (int)$store_id));
			    }
			}
		}

		$this->updateStatus($unit_id, $data['status']);
		$this->updateSortOrder($unit_id, $data['sort_order']);

    	return $unit_id;
    
	}

	public function deleteUnit($unit_id) 
	{
    	$statement = $this->db->prepare("DELETE FROM `units` WHERE `unit_id` = ? LIMIT 1");
    	$statement->execute(array($unit_id));

    	$statement = $this->db->prepare("DELETE FROM `unit_to_store` WHERE `uunit_id` = ?");
    	$statement->execute(array($unit_id));

        return $unit_id;
	}

	public function getUnit($unit_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `units`
			LEFT JOIN `unit_to_store` as unit2s ON (`units`.`unit_id` = `unit2s`.`uunit_id`)  
	    	WHERE `unit2s`.`store_id` = ? AND `units`.`unit_id` = ?");
	  	$statement->execute(array($store_id, $unit_id));
	  	$unit = $statement->fetch(PDO::FETCH_ASSOC);

	    // Fetch stores related to units
	    $statement = $this->db->prepare("SELECT `store_id` FROM `unit_to_store` WHERE `uunit_id` = ?");
	    $statement->execute(array($unit_id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $unit['stores'] = $stores;

	    return $unit;
	}

	public function getUnits($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `units` LEFT JOIN `unit_to_store` unit2s ON (`units`.`unit_id` = `unit2s`.`uunit_id`) WHERE `unit2s`.`store_id` = ? AND `unit2s`.`status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `unit_name` LIKE '" . $data['filter_name'] . "%'";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `unit_id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `units`.`unit_id`";

		$sort_data = array(
			'unit_name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `unit_name`";
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

	public function getBelongsStore($unit_id)
	{
		$statement = $this->db->prepare("SELECT * FROM `unit_to_store` WHERE `uunit_id` = ?");
		$statement->execute(array($unit_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function total($store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `units`LEFT JOIN `unit_to_store` unit2s ON (`units`.`unit_id` = `unit2s`.`uunit_id`) where `unit2s`.`store_id` = ? AND `unit2s`.`status` = ?");
		$statement->execute(array($store_id, 1));
		
		return $statement->rowCount();
	}

	public function totalProduct($unit_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `products` p LEFT JOIN `product_to_store` p2s ON (`p`.`p_id` = `p2s`.`product_id`) WHERE `store_id` = ? AND `unit_id` = ?");
		$statement->execute(array($store_id, $unit_id));
	
		return $statement->rowCount();
	}
}