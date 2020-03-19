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
class ModelExpenseCategory extends Model 
{
	public function addExpenseCategory($data) 
	{
    	$statement = $this->db->prepare("INSERT INTO `expense_categorys` (category_name, category_slug, parent_id, category_details, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['category_name'], $data['category_slug'], $data['parent_id'], $data['category_details'], $data['status'], $data['sort_order'], date_time()));
    	$category_id = $this->db->lastInsertId();
    	return $category_id; 
	}

	public function editExpenseCategory($category_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `expense_categorys` SET `category_name` = ?, `category_slug` = ?, `parent_id` = ?, `category_details` = ?, `status` = ?, `sort_order` = ? WHERE category_id = ? ");
    	$statement->execute(array($data['category_name'], $data['category_slug'], (int)$data['parent_id'], $data['category_details'], $data['status'], $data['sort_order'], $category_id));

    	return $category_id;
	}

	public function deleteExpenseCategory($category_id) 
	{
    	$statement = $this->db->prepare("DELETE FROM `expense_categorys` WHERE `category_id` = ? LIMIT 1");
    	$statement->execute(array($category_id));

        return $category_id;
	}

	public function getExpenseCategory($category_id) 
	{
		$statement = $this->db->prepare("SELECT * FROM `expense_categorys`
	    	WHERE `category_id` = ?");
	  	$statement->execute(array($category_id));
	  	$expense_category = $statement->fetch(PDO::FETCH_ASSOC);
	    return $expense_category;
	}

	public function isTopLevel($category_id)
	{
		$statement = $this->db->prepare("SELECT `category_id` FROM `expense_categorys` WHERE `category_id` = ? AND `parent_id` = ?");
	    $statement->execute(array($category_id, 0));
	    return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getParentID($category_id)
	{
		$statement = $this->db->prepare("SELECT `parent_id` FROM `expense_categorys` WHERE `category_id` = ?");
	    $statement->execute(array($category_id));
	    $expense_category = $statement->fetch(PDO::FETCH_ASSOC);
	    return isset($expense_category['parent_id']) ? $expense_category['parent_id'] : 0;
	}

	public function getExpenseCategorys($data = array()) 
	{
		$sql = "SELECT * FROM `expense_categorys` WHERE `status` = ?";

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

		$sql .= " GROUP BY `expense_categorys`.`category_id`";

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
		$statement->execute(array(1));

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getExpenseCategoryTree($data = array())
	{
		$tree = array();
		$expense_categorys = $this->getExpenseCategorys($data);
		foreach ($expense_categorys as $expense_category) {
			$name = '';
			$parent = $this->getExpenseCategory($expense_category['parent_id']);
			if (isset($parent['category_id'])) {
				$name = $parent['category_name'] .  ' > ';
			}

			$tree[$expense_category['category_id']] = $name . $expense_category['category_name'];
		}		
		return $tree;
	}

	public function total() 
	{
		$statement = $this->db->prepare("SELECT * FROM `expense_categorys` WHERE `status` = ?");
		$statement->execute(array(1));
		return $statement->rowCount();
	}

	public function totalItem($category_id) 
	{
		$statement = $this->db->prepare("SELECT * FROM `expenses` WHERE `category_id` = ? AND `status` = ?");
		$statement->execute(array($category_id, 1));
		return $statement->rowCount();
	}

	public function replaceWith($new_category_id, $category_id)
	{
      	$statement = $this->db->prepare("UPDATE `expenses` SET `category_id` = ? WHERE `category_id` = ?");
      	$statement->execute(array($new_category_id, $category_id));
	}
}