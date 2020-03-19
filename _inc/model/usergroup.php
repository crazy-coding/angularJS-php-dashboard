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
class ModelUsergroup extends Model 
{
	public function addUsergroup($data) 
	{
    	$statement = $this->db->prepare("INSERT INTO `user_group` (name, slug) VALUES (?, ?)");
    	$statement->execute(array($data['name'], $data['slug']));
    
    	return $this->db->lastInsertId();    
	}

	public function editUsergroup($group_id, $data, $permission) 
	{    	
    	$statement = $this->db->prepare("UPDATE `user_group` SET name = ?, slug = ?, `permission` =? WHERE `group_id` = ?");
    	$statement->execute(array($data['name'], $data['slug'], serialize($permission), $group_id));
    
    	return $group_id;
	}

	public function deleteUsergroup($group_id) 
	{    	
    	$statement = $this->db->prepare("DELETE FROM `user_group` WHERE `group_id` = ? LIMIT 1");
    	$statement->execute(array($group_id));

        return $group_id;
	}

	public function getUsergroup($group_id) 
	{
	    $statement = $this->db->prepare("SELECT * FROM `user_group` WHERE `group_id` = ?");
  		$statement->execute(array($group_id));
  		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getUsergroups($data = array()) 
	{
		$sql = "SELECT * FROM `user_group` WHERE 1=1";

		if (isset($data['filter_name'])) {
			$sql .= " AND `name` LIKE '" . $data['filter_name'] . "%'";
		}

		$sql .= " GROUP BY group_id";

		$sort_data = array(
			'name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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

	public function totalUser($group_id, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `users`
			LEFT JOIN `user_to_store` u2s ON (`users`.`id` = `u2s`.`user_id`) WHERE `store_id` = ? AND `group_id` = ?");
		$statement->execute(array($store_id, $group_id));

		return $statement->rowCount();

	}
}