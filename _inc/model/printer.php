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
class ModelPrinter extends Model 
{
	public function addPrinter($data) 
	{		
    	$statement = $this->db->prepare("INSERT INTO `printers` (title, type, char_per_line, created_at) VALUES (?, ?, ?, ?)");
    	$statement->execute(array($data['title'], $data['type'], $data['char_per_line'], date_time()));

    	$printer_id = $this->db->lastInsertId();

    	if (isset($data['printer_store'])) {
			foreach ($data['printer_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `printer_to_store` SET `pprinter_id` = ?, `store_id` = ?");
				$statement->execute(array($printer_id, $store_id));
			}
		}
		$this->updatePath($printer_id, $data['path']);
		$this->updateIpAddress($printer_id, $data['ip_address']);
		$this->updatePort($printer_id, $data['port']);
		$this->updateStatus($printer_id, $data['status']);
		$this->updateSortOrder($printer_id, $data['sort_order']);

    	return $printer_id;
	}

	public function updatePath($printer_id, $path, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `printer_to_store` SET `path` = ? WHERE `store_id` = ? AND `pprinter_id` = ?");
		$statement->execute(array($path, $store_id, $printer_id));
	}

	public function updateIpAddress($printer_id, $ip_address, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `printer_to_store` SET `ip_address` = ? WHERE `store_id` = ? AND `pprinter_id` = ?");
		$statement->execute(array($ip_address, $store_id, $printer_id));
	}

	public function updatePort($printer_id, $port, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `printer_to_store` SET `port` = ? WHERE `store_id` = ? AND `pprinter_id` = ?");
		$statement->execute(array($port, $store_id, $printer_id));
	}

	public function updateStatus($printer_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `printer_to_store` SET `status` = ? WHERE `store_id` = ? AND `pprinter_id` = ?");
		$statement->execute(array($status, $store_id, $printer_id));
	}

	public function updateSortOrder($printer_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `printer_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `pprinter_id` = ?");
		$statement->execute(array($sort_order, $store_id, $printer_id));
	}

	public function editPrinter($printer_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `printers` SET `title` = ?, `type` = ?, `char_per_line` = ? WHERE printer_id = ? ");
    	$statement->execute(array($data['title'], $data['type'], $data['char_per_line'], $printer_id));
		
		// insert printer into store
    	if (isset($data['printer_store'])) {

    		$store_ids = array();

			foreach ($data['printer_store'] as $store_id) {

				$statement = $this->db->prepare("SELECT * FROM `printer_to_store` WHERE `store_id` = ? AND `pprinter_id` = ?");
			    $statement->execute(array($store_id, $printer_id));
			    $printer = $statement->fetch(PDO::FETCH_ASSOC);
			    if (!$printer) {
			    	$statement = $this->db->prepare("INSERT INTO `printer_to_store` SET `pprinter_id` = ?, `store_id` = ?");
					$statement->execute(array($printer_id, $store_id));
			    }

			    $store_ids[] = $store_id;
			}

			// delete unwanted store
			if (!empty($store_ids)) {

				$unremoved_store_ids = array();

				// get unwanted stores
				$statement = $this->db->prepare("SELECT * FROM `printer_to_store` WHERE `store_id` NOT IN (" . implode(',', $store_ids) . ")");
				$statement->execute();
				$unwanted_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($unwanted_stores as $store) {

					$store_id = $store['store_id'];
					
				    $item_available = $statement->fetch(PDO::FETCH_ASSOC);

				     // if item available then store in variable
				    if ($item_available) {
				      $unremoved_store_ids[$item_available['store_id']] = store_field('name', $item_available['store_id']);
				      continue;
				    }

				    // delete unwanted store link
					$statement = $this->db->prepare("DELETE FROM `printer_to_store` WHERE `store_id` = ? AND `pprinter_id` = ?");
					$statement->execute(array($store_id, $printer_id));

				}

				if (!empty($unremoved_store_ids)) {

					throw new Exception('The Printer belongs to the stores(s) "' . implode(', ', $unremoved_store_ids) . '" contains products, so its can not be removed');
				}				
			}
		}
		$this->updatePath($printer_id, $data['path']);
		$this->updateIpAddress($printer_id, $data['ip_address']);
		$this->updatePort($printer_id, $data['port']);
		$this->updateStatus($printer_id, $data['status']);
		$this->updateSortOrder($printer_id, $data['sort_order']);

    	return $printer_id;
    
	}

	public function deletePrinter($printer_id) 
	{
    	$statement = $this->db->prepare("DELETE FROM `printers` WHERE `printer_id` = ? LIMIT 1");
    	$statement->execute(array($printer_id));

    	$statement = $this->db->prepare("DELETE FROM `printer_to_store` WHERE `pprinter_id` = ?");
    	$statement->execute(array($printer_id));

        return $printer_id;
	}

	public function getPrinter($printer_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `printers`
			LEFT JOIN `printer_to_store` as p2s ON (`printers`.`printer_id` = `p2s`.`pprinter_id`)  
	    	WHERE `p2s`.`store_id` = ? AND `printers`.`printer_id` = ?");
	  	$statement->execute(array($store_id, $printer_id));
	  	$printer = $statement->fetch(PDO::FETCH_ASSOC);

	    // fetch stores related to printers
	    $statement = $this->db->prepare("SELECT `store_id` FROM `printer_to_store` WHERE `pprinter_id` = ?");
	    $statement->execute(array($printer_id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $printer['stores'] = $stores;

	    return $printer;
	}

	public function getPrinters($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `printers` LEFT JOIN `printer_to_store` p2s ON (`printers`.`printer_id` = `p2s`.`pprinter_id`) WHERE `p2s`.`store_id` = ? AND `p2s`.`status` = ?";

		if (isset($data['filter_title'])) {
			$sql .= " AND `title` LIKE '" . $data['filter_title'] . "%'";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `printer_id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `printers`.`printer_id`";

		$sort_data = array(
			'title'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `title`";
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

			$sql .= " LIMIT " . $data['start'] . "," . $data['limit'];
		}

		$statement = $this->db->prepare($sql);
		$statement->execute(array($store_id, 1));

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getBelongsStore($printer_id)
	{
		$statement = $this->db->prepare("SELECT * FROM `printer_to_store` WHERE `pprinter_id` = ?");
		$statement->execute(array($printer_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);

	}

	public function total($store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `printers`LEFT JOIN `printer_to_store` p2s ON (`printers`.`printer_id` = `p2s`.`pprinter_id`) where `p2s`.`store_id` = ? AND `p2s`.`status` = ?");
		$statement->execute(array($store_id, 1));
		
		return $statement->rowCount();
	}
}