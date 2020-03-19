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
class ModelCustomer extends Model 
{
	public function addCustomer($data) 
	{
    	$statement = $this->db->prepare("INSERT INTO `customers` (customer_name, customer_email, customer_mobile, customer_sex, customer_age, customer_address, customer_city, customer_state, customer_country, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['customer_name'], $data['customer_email'], $data['customer_mobile'], $data['customer_sex'], $data['customer_age'], $data['customer_address'], $data['customer_city'], $data['customer_state'], $data['customer_country'], date_time()));

    	$customer_id = $this->db->lastInsertId();

		foreach (get_stores() as $the_store) {
			$statement = $this->db->prepare("INSERT INTO `customer_to_store` SET `customer_id` = ?, `store_id` = ?");
			$statement->execute(array((int)$customer_id, (int)$the_store['store_id']));
		}

		$this->updateStatus($customer_id, $data['status']);
		$this->updateSortOrder($customer_id, $data['sort_order']);

    	return $customer_id;    
	}

	public function updateStatus($customer_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `customer_to_store` SET `status` = ? WHERE `store_id` = ? AND `customer_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$customer_id));
	}

	public function updateSortOrder($customer_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `customer_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `customer_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$customer_id));
	}

	public function editCustomer($customer_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `customers` SET `customer_name` = ?, `customer_email` = ?, `customer_mobile` = ?, `customer_sex` = ?, `customer_age` = ?, `customer_address` = ?, `customer_city` = ?, `customer_state` = ?, `customer_country` = ? WHERE `customer_id` = ? ");
    	$statement->execute(array($data['customer_name'], $data['customer_email'], $data['customer_mobile'], $data['customer_sex'], $data['customer_age'], $data['customer_address'], $data['customer_city'], $data['customer_state'], $data['customer_country'], $customer_id));

    	$this->updateStatus($customer_id, $data['status']);
    	$this->updateSortOrder($customer_id, $data['sort_order']);

    	return $customer_id;
    
	}

	public function replaceWith($new_id, $id)
	{
		$statement = $this->db->prepare("UPDATE `customer_to_store` SET `customer_id` = ? WHERE `customer_id` = ?");
      	$statement->execute(array($new_id, $id));

      	$statement = $this->db->prepare("UPDATE `selling_info` SET `customer_id` = ? WHERE `customer_id` = ?");
      	$statement->execute(array($new_id, $id));
	}

	public function deleteCustomer($customer_id) 
	{
        $statement = $this->db->prepare("DELETE FROM `customers` WHERE `customer_id` = ? LIMIT 1");
    	$statement->execute(array($customer_id));

    	$statement = $this->db->prepare("DELETE FROM `customer_to_store` WHERE `customer_id` = ?");
    	$statement->execute(array($customer_id));

        return $customer_id;
	}

	public function getCustomer($customer_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

	    $statement = $this->db->prepare("SELECT * FROM `customers` 
	    	LEFT JOIN `customer_to_store` c2s ON (`customers`.`customer_id` = `c2s`.`customer_id`)
	    	WHERE `customers`.`customer_id` = ? AND `store_id` = ?");

		$statement->execute(array($customer_id, $store_id));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getCustomers($data = array(), $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `customers` c LEFT JOIN `customer_to_store` c2s ON (`c`.`customer_id` = `c2s`.`customer_id`) WHERE `c2s`.`store_id` = ? AND `status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `c`.`customer_name` LIKE '" . $data['filter_name'] . "%'";
		}

		if (isset($data['filter_email'])) {
			$sql .= " AND `c`.`customer_email` LIKE '" . $data['filter_email'] . "%'";
		}

		if (isset($data['filter_mobile'])) {
			$sql .= " AND `c`.`customer_mobile` LIKE '" . $data['filter_mobile'] . "%'";
		}

		if (isset($data['filter_has_giftcard'])) {
			$sql .= " AND `c`.`is_giftcard` = " . $data['filter_has_giftcard'];
		}

		if (isset($data['only'])) {
			$sql .= " AND `c`.`customer_id` IN (" . implode(',', $data['only']) . ")";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `c`.`customer_id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `c`.`customer_id`";

		$sort_data = array(
			'customer_name',
			'customer_email',
			'customer_mobile'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `c`.`customer_id`";
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

	public function getBestCustomer($field, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_price`.*, `customers`.*, SUM(`selling_price`.`payable_amount`) as total 
			FROM `selling_info` 
			LEFT JOIN `customers` ON (`selling_info`.`customer_id` = `customers`.`customer_id`)
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE `selling_info`.`store_id` = ?
			GROUP BY `selling_info`.`customer_id` ORDER BY `total` DESC");
		$statement->execute(array($store_id));
		$customer = $statement->fetch(PDO::FETCH_ASSOC);

		return isset($customer[$field]) ? $customer[$field] : null;
	}

	public function getRecentCustomers($limit, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT customers.* FROM `selling_info` 
			LEFT JOIN `customers` ON (`selling_info`.`customer_id` = `customers`.`customer_id`) 
			LEFT JOIN `customer_to_store` as c2s ON (`selling_info`.`customer_id` = `c2s`.`customer_id`)
			where `selling_info`.`store_id` = ? AND `c2s`.`status` = ?
			GROUP BY `selling_info`.`customer_id`
			ORDER BY `selling_info`.`info_id` DESC 
			LIMIT $limit"
			);
	    $statement->execute(array($store_id, 1));
	    return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getTotalBuyingAmount($customer_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_price`.*, `customers`.*, SUM(`selling_price`.`payable_amount`) as total FROM `selling_info` 
			LEFT JOIN `customers` ON (`selling_info`.`customer_id` = `customers`.`customer_id`)
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`)
			where `customers`.`customer_id` = ? AND `selling_info`.`store_id` = ? 
			ORDER BY `total` DESC");
		$statement->execute(array($customer_id, $store_id));
		$customer = $statement->fetch(PDO::FETCH_ASSOC);

		return isset($customer['total']) ? $customer['total'] : '0';
	}

	public function getTotalInvoiceNumber($customer_id = null, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		if ($customer_id) {
			$statement = $this->db->prepare("SELECT * FROM `selling_info` 
				WHERE `customer_id` = ? AND `store_id` = ?");
			$statement->execute(array($customer_id, store_id()));
		}
		else {
			$statement = $this->db->prepare("SELECT * FROM `selling_info` WHERE `store_id` = ?");
			$statement->execute(array($store_id));
		}

		return $statement->rowCount();
	}

	public function getBalance($customer_id, $index = null, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_price`.`present_due` FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE `selling_info`.`customer_id` = ? AND `selling_info`.`store_id` = ?
			ORDER by `selling_info`.`invoice_id` DESC LIMIT 1");
		$statement->execute(array($customer_id, $store_id));

		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		
		if ($index) {
			return isset($invoice[$index]) ? $invoice[$index] : null;
		}

		return isset($invoice['present_due']) ? $invoice['present_due'] : 0;
	}

	public function getDueAmount($customer_id, $store_id = null, $index = 'due_amount') 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `customer_to_store` WHERE `customer_id` = ? AND `store_id` = ?");
		$statement->execute(array($customer_id, $store_id));

		$row = $statement->fetch(PDO::FETCH_ASSOC);

		return isset($row[$index]) ? $row[$index] : null;
	}

	public function getBelongsStore($customer_id)
	{
		$statement = $this->db->prepare("SELECT * FROM `customer_to_store` WHERE `customer_id` = ?");
		$statement->execute(array($customer_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function totalToday($store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`c2s`.`store_id` = '$store_id'";
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		if (($from && ($to == false)) || ($from == $to)) {
			$day = date('d', strtotime($from));
			$month = date('m', strtotime($from));
			$year = date('Y', strtotime($from));
			$where_query .= " AND DAY(`customers`.`created_at`) = $day";
			$where_query .= " AND MONTH(`customers`.`created_at`) = $month";
			$where_query .= " AND YEAR(`customers`.`created_at`) = $year";
		} else {
			$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
			$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
			$where_query .= " AND customers.created_at >= '{$from}' AND customers.created_at <= '{$to}'";
		}

		$statement = $this->db->prepare("SELECT * FROM `customers` LEFT JOIN `customer_to_store` c2s ON (`customers`.`customer_id` = `c2s`.`customer_id`) WHERE $where_query");
		$statement->execute(array());

		return $statement->rowCount();
	}

	public function total($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`c2s`.`store_id` = '$store_id'";
		if ($from) {
			$from = $from ? $from : date('Y-m-d');
			$to = $to ? $to : date('Y-m-d');
			if (($from && ($to == false)) || ($from == $to)) {
				$day = date('d', strtotime($from));
				$month = date('m', strtotime($from));
				$year = date('Y', strtotime($from));
				$where_query .= " AND DAY(`customers`.`created_at`) = $day";
				$where_query .= " AND MONTH(`customers`.`created_at`) = $month";
				$where_query .= " AND YEAR(`customers`.`created_at`) = $year";
			} else {
				$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
				$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
				$where_query .= " AND customers.created_at >= '{$from}' AND customers.created_at <= '{$to}'";
			}
		}
		$statement = $this->db->prepare("SELECT * FROM `customers` LEFT JOIN `customer_to_store` c2s ON (`customers`.`customer_id` = `c2s`.`customer_id`) WHERE $where_query");
		$statement->execute(array());
		return $statement->rowCount();
	}

	public function getCustomerAvatar($sex)
	{
		switch ($sex) {
			case 1:
				$avatar = 'avatar';
				break;
			case 2:
				$avatar = 'avatar-female';
				break;
			default:
				$avatar = 'avatar-others';
				break;
		}
		
		return $avatar;
	}
}