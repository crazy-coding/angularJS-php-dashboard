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
class ModelCategory extends Model 
{
	public function addCategory($data) 
	{
    	$statement = $this->db->prepare("INSERT INTO `categorys` (category_name, category_slug, parent_id, category_details, created_at) VALUES (?, ?, ?, ?, ?)");
    	$statement->execute(array($data['category_name'], $data['category_slug'], $data['parent_id'], $data['category_details'], date_time()));

    	$category_id = $this->db->lastInsertId();

    	if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `category_to_store` SET `ccategory_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$category_id, (int)$store_id));
			}
		}

		$this->updateStatus($category_id, $data['status']);
		$this->updateSortOrder($category_id, $data['sort_order']);

    	return $category_id; 
	}

	public function updateStatus($category_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `category_to_store` SET `status` = ? WHERE `store_id` = ? AND `ccategory_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$category_id));
	}

	public function updateSortOrder($category_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `category_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `ccategory_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$category_id));
	}

	public function editCategory($category_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `categorys` SET `category_name` = ?, `category_slug` = ?, `parent_id` = ?, `category_details` = ? WHERE category_id = ? ");
    	$statement->execute(array($data['category_name'], $data['category_slug'], (int)$data['parent_id'], $data['category_details'], $category_id));

    	// insert category into store
    	if (isset($data['category_store'])) {

    		$store_ids = array();

			foreach ($data['category_store'] as $store_id) {

				$statement = $this->db->prepare("SELECT * FROM `category_to_store` WHERE `store_id` = ? AND `ccategory_id` = ?");
			    $statement->execute(array($store_id, $category_id));
			    $category = $statement->fetch(PDO::FETCH_ASSOC);
			    if (!$category) {
			    	$statement = $this->db->prepare("INSERT INTO `category_to_store` SET `ccategory_id` = ?, `store_id` = ?");
					$statement->execute(array((int)$category_id, (int)$store_id));
			    }

			    $store_ids[] = $store_id;
			}

			// delete unwanted store
			if (!empty($store_ids)) {

				$unremoved_store_ids = array();

				// get unwanted stores
				$statement = $this->db->prepare("SELECT * FROM `category_to_store` WHERE `store_id` NOT IN (" . implode(',', $store_ids) . ")");
				$statement->execute();
				$unwanted_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($unwanted_stores as $store) {

					$store_id = $store['store_id'];
					
					// fetch buying invoice id
				    $statement = $this->db->prepare("SELECT * FROM `products` as p LEFT JOIN `product_to_store` as p2s ON (`p`.`p_id` = `p2s`.`product_id`) WHERE `store_id` = ? AND `category_id` = ?");
				    $statement->execute(array($store_id, $category_id));
				    $item_available = $statement->fetch(PDO::FETCH_ASSOC);

				     // if item available then store in variable
				    if ($item_available) {
				      $unremoved_store_ids[$item_available['store_id']] = store_field('name', $item_available['store_id']);
				      continue;
				    }

				    // delete unwanted store link
					$statement = $this->db->prepare("DELETE FROM `category_to_store` WHERE `store_id` = ? AND `ccategory_id` = ?");
					$statement->execute(array($store_id, $category_id));

				}

				if (!empty($unremoved_store_ids)) {

					throw new Exception('The Category belongs to the stores(s) "' . implode(', ', $unremoved_store_ids) . '" has product(s), so its can not be removed');
				}				
			}
		}

		$this->updateStatus($category_id, $data['status']);
		$this->updateSortOrder($category_id, $data['sort_order']);

    	return $category_id;
	}

	public function deleteCategory($category_id) 
	{
		$statement = $this->db->prepare("DELETE FROM `category_to_store` WHERE `ccategory_id` = ?");
    	$statement->execute(array($category_id));

    	$statement = $this->db->prepare("DELETE FROM `categorys` WHERE `category_id` = ? LIMIT 1");
    	$statement->execute(array($category_id));

        return $category_id;
	}

	public function getCategory($category_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `categorys`
			LEFT JOIN `category_to_store` as c2s ON (`categorys`.`category_id` = `c2s`.`ccategory_id`)  
	    	WHERE `c2s`.`store_id` = ? AND `category_id` = ?");
	  	$statement->execute(array($store_id, $category_id));
	  	$category = $statement->fetch(PDO::FETCH_ASSOC);

	  	// fetch stores related to categorys
	    $statement = $this->db->prepare("SELECT `store_id` FROM `category_to_store` WHERE `ccategory_id` = ?");
	    $statement->execute(array($category_id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $category['stores'] = $stores;

	    return $category;
	}

	public function isTopLevel($category_id)
	{
		$statement = $this->db->prepare("SELECT `category_id` FROM `categorys` WHERE `category_id` = ? AND `parent_id` = ?");
	    $statement->execute(array($category_id, 0));
	    return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getParentID($category_id)
	{
		$statement = $this->db->prepare("SELECT `parent_id` FROM `categorys` WHERE `category_id` = ?");
	    $statement->execute(array($category_id));
	    $category = $statement->fetch(PDO::FETCH_ASSOC);
	    return isset($category['parent_id']) ? $category['parent_id'] : 0;
	}

	public function getCategorys($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `categorys` LEFT JOIN `category_to_store` c2s ON (`categorys`.`category_id` = `c2s`.`ccategory_id`) WHERE `c2s`.`store_id` = ? AND `c2s`.`status` = ?";

		if (isset($data['filter_parent_id'])) {
			$sql .= " AND `parent_id` = " . $data['filter_parent_id'];
		} elseif (!isset($data['filter_fetch_all'])) {
			$sql .= " AND `parent_id` = 0";
		}

		if (isset($data['filter_category_name'])) {
			$sql .= " AND `category_name` LIKE '" . $data['filter_category_name'] . "%'";
		}

		if (isset($data['only'])) {
			$sql .= " AND `category_id` IN (" . implode(',', $data['only']) . ")";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `category_id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `categorys`.`category_id`";

		$sort_data = array(
			'category_name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `category_name`";
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

	public function getCategoryTree($data = array(), $store_id = null)
	{
		$tree = array();
		$categorys = $this->getCategorys($data, $store_id);
		foreach ($categorys as $category) {
			$name = '';
			$parent = $this->getCategory($category['parent_id']);
			if (isset($parent['category_id'])) {
				$name = $parent['category_name'] .  ' > ';
			}

			$tree[$category['category_id']] = $name . $category['category_name'];
		}		
		return $tree;
	}

	public function getBelongsStore($category_id)
	{
		$statement = $this->db->prepare("SELECT * FROM `category_to_store` WHERE `ccategory_id` = ?");
		$statement->execute(array($category_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);

	}

	public function total($store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `categorys`LEFT JOIN `category_to_store` c2s ON (`categorys`.`category_id` = `c2s`.`ccategory_id`) where `c2s`.`store_id` = ? AND `c2s`.`status` = ?");
		$statement->execute(array($store_id, 1));
		
		return $statement->rowCount();
	}

	public function totalValidItem($category_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `products` LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`) WHERE `store_id` = ? AND `category_id` = ? AND `p2s`.`quantity_in_stock` > ? AND `p2s`.`status` = ?");
		$statement->execute(array($store_id, $category_id, 0, 1));
	
		return $statement->rowCount();
	}

	public function totalItem($category_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `products` LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`) WHERE `store_id` = ? AND `category_id` = ? AND `p2s`.`status` = ?");
		$statement->execute(array($store_id, $category_id, 1));
	
		return $statement->rowCount();
	}

	public function replaceWith($new_id, $id)
	{
		$statement = $this->db->prepare("UPDATE `category_to_store` SET `ccategory_id` = ? WHERE `ccategory_id` = ?");
      	$statement->execute(array($new_id, $id));

      	$statement = $this->db->prepare("UPDATE `selling_item` SET `category_id` = ? WHERE `category_id` = ?");
      	$statement->execute(array($new_id, $id));
	}
}