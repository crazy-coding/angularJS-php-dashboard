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
class ModelTaxrate extends Model 
{
	public function addTaxrate($data) 
	{		
    	$statement = $this->db->prepare("INSERT INTO `taxrates` (taxrate_name, taxrate_code, taxrate, status, sort_order) VALUES (?, ?, ?, ?, ?)");
    	$statement->execute(array($data['taxrate_name'], $data['taxrate_code'], $data['taxrate'], $data['status'], $data['sort_order']));
    	$taxrate_id = $this->db->lastInsertId();
    	return $taxrate_id; 
	}

	public function editTaxrate($taxrate_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `taxrates` SET `taxrate_name` = ?, `taxrate_code` = ?, `taxrate` = ?, `status` = ?, `sort_order` = ? WHERE taxrate_id = ? ");
    	$statement->execute(array($data['taxrate_name'], $data['taxrate_code'], $data['taxrate'], $data['status'], $data['sort_order'], $taxrate_id));
    	return $taxrate_id;
	}

	public function deleteTaxrate($taxrate_id) 
	{
    	$statement = $this->db->prepare("DELETE FROM `taxrates` WHERE `taxrate_id` = ? LIMIT 1");
    	$statement->execute(array($taxrate_id));
        return $taxrate_id;
	}

	public function getTaxrate($taxrate_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `taxrates` WHERE `taxrate_id` = ?");
	  	$statement->execute(array($taxrate_id));
	  	$taxrate = $statement->fetch(PDO::FETCH_ASSOC);
	    return $taxrate;
	}

	public function getTaxrates($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `taxrates` WHERE `status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `taxrate_name` LIKE '" . $data['filter_name'] . "%'";
		}

		if (isset($data['filter_code'])) {
			$sql .= " AND `taxrate_code` LIKE '" . $data['filter_code'] . "%'";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `taxrate_id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `taxrates`.`taxrate_id`";

		$sort_data = array(
			'taxrate_name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `taxrate_name`";
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
		$statement->execute(array(1));

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function total($store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `taxrates` WHERE `status` = ?");
		$statement->execute(array($store_id, 1));
		return $statement->rowCount();
	}

	public function totalProduct($taxrate_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `products` p LEFT JOIN `product_to_store` p2s ON (`p`.`p_id` = `p2s`.`product_id`) WHERE `store_id` = ? AND `taxrate_id` = ?");
		$statement->execute(array($store_id, $taxrate_id));
		return $statement->rowCount();
	}
}