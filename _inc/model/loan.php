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
class ModelLoan extends Model 
{
	public function addLoanPay($data) 
	{
		$created_by = user_id();
		$created_at = date_time();
		$paid = $data['paid'];
		$loan_id = $data['loan_id'];
    	$statement = $this->db->prepare("INSERT INTO `loan_payments` (lloan_id, ref_no, paid, note, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($loan_id, $data['ref_no'], $paid, $data['note'], $created_by, $created_at));
    	$paid_id = $this->db->lastInsertId();

    	// Upade paid and due amount
    	$statement = $this->db->prepare("UPDATE `loans` SET `paid` = `paid` + $paid, `due` = `due` - $paid WHERE `loan_id` = ?");
		$statement->execute(array($loan_id));

		return $paid_id;
	}

	public function addLoan($data) 
	{
		$payable = $data['interest'] > 0 ? $data['amount']+(($data['interest']/100)*$data['amount']) : $data['amount'];
		$due = $payable;
		$created_by = user_id();
		$created_at = date('Y-m-d H:i:s', strtotime($data['date']));
    	$statement = $this->db->prepare("INSERT INTO `loans` (ref_no, loan_from, title, amount, interest, payable, due, details, attachment, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['ref_no'], $data['loan_from'], $data['title'], $data['amount'], $data['interest'], $payable, $due, $data['details'], $data['image'], $created_by, $created_at));

    	$loan_id = $this->db->lastInsertId();

    	if (isset($data['loan_store'])) {
			foreach ($data['loan_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `loan_to_store` SET `lloan_id` = ?, `store_id` = ?");
				$statement->execute(array((int)$loan_id, (int)$store_id));
			}
		}

		$this->updateStatus($loan_id, $data['status']);
		$this->updateSortOrder($loan_id, $data['sort_order']);

    	return $loan_id; 
	}

	public function updateStatus($loan_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `loan_to_store` SET `status` = ? WHERE `store_id` = ? AND `lloan_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$loan_id));
	}

	public function updateSortOrder($loan_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `loan_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `lloan_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$loan_id));
	}

	public function editLoan($loan_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `loans` SET `loan_from` = ?, `ref_no` = ?, `title` = ?, `details` = ?, attachment = ? WHERE loan_id = ? ");
    	$statement->execute(array($data['loan_from'], $data['ref_no'], $data['title'], $data['details'], $data['image'], $loan_id));
		
		// insert loan into store
    	if (isset($data['loan_store'])) {

    		$store_ids = array();

			foreach ($data['loan_store'] as $store_id) {

				$statement = $this->db->prepare("SELECT * FROM `loan_to_store` WHERE `store_id` = ? AND `lloan_id` = ?");
			    $statement->execute(array($store_id, $loan_id));
			    $loan = $statement->fetch(PDO::FETCH_ASSOC);
			    if (!$loan) {
			    	$statement = $this->db->prepare("INSERT INTO `loan_to_store` SET `lloan_id` = ?, `store_id` = ?");
					$statement->execute(array((int)$loan_id, (int)$store_id));
			    }

			    $store_ids[] = $store_id;
			}

			// delete unwanted store
			if (!empty($store_ids)) {

				// get unwanted stores
				$statement = $this->db->prepare("SELECT * FROM `loan_to_store` WHERE `store_id` NOT IN (" . implode(',', $store_ids) . ")");
				$statement->execute();
				$unwanted_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($unwanted_stores as $store) {

					$store_id = $store['store_id'];

				    // delete unwanted store link
					$statement = $this->db->prepare("DELETE FROM `loan_to_store` WHERE `store_id` = ? AND `lloan_id` = ?");
					$statement->execute(array($store_id, $loan_id));

				}				
			}
		}

		$this->updateStatus($loan_id, $data['status']);
		$this->updateSortOrder($loan_id, $data['sort_order']);

    	return $loan_id;
    
	}

	public function deleteLoan($loan_id) 
	{
    	$statement = $this->db->prepare("DELETE FROM `loans` WHERE `loan_id` = ? LIMIT 1");
    	$statement->execute(array($loan_id));

    	$statement = $this->db->prepare("DELETE FROM `loan_to_store` WHERE `lloan_id` = ?");
    	$statement->execute(array($loan_id));

    	$statement = $this->db->prepare("DELETE FROM `loan_payments` WHERE `lloan_id` = ?");
    	$statement->execute(array($loan_id));

        return $loan_id;
	}

	public function getLoan($loan_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `loans`
			LEFT JOIN `loan_to_store` as l2s ON (`loans`.`loan_id` = `l2s`.`lloan_id`)  
	    	WHERE `l2s`.`store_id` = ? AND `loans`.`loan_id` = ?");
	  	$statement->execute(array($store_id, $loan_id));
	  	$loan = $statement->fetch(PDO::FETCH_ASSOC);

	    // fetch stores related to loans
	    $statement = $this->db->prepare("SELECT `store_id` FROM `loan_to_store` WHERE `lloan_id` = ?");
	    $statement->execute(array($loan_id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $loan['stores'] = $stores;

	    return $loan;
	}

	public function getLoanPayments($loan_id) 
	{
		$statement = $this->db->prepare("SELECT * FROM `loan_payments` WHERE `lloan_id` = ?");
	  	$statement->execute(array($loan_id));
	  	$payments = $statement->fetchAll(PDO::FETCH_ASSOC);
	    return $payments;
	}

	public function getLoans($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `loans` LEFT JOIN `loan_to_store` l2s ON (`loans`.`loan_id` = `l2s`.`lloan_id`) WHERE `l2s`.`store_id` = ? AND `l2s`.`status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `loan_name` LIKE '" . $data['filter_name'] . "%'";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `loan_id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `loans`.`loan_id`";

		$sort_data = array(
			'loan_name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `loan_name`";
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

	public function getBelongsStore($loan_id)
	{
		$statement = $this->db->prepare("SELECT * FROM `loan_to_store` WHERE `lloan_id` = ?");
		$statement->execute(array($loan_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);

	}

	public function totalLoan($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`l2s`.`store_id` = ? AND `l2s`.`status` = ?";
		if ($from) {
			$where_query .= date_range_loan_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`payable`) as total FROM `loans`LEFT JOIN `loan_to_store` l2s ON (`loans`.`loan_id` = `l2s`.`lloan_id`) WHERE $where_query");
		$statement->execute(array($store_id, 1));
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return $row['total'];
	}

	public function totalPaid($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`l2s`.`store_id` = ? AND `l2s`.`status` = ?";
		if ($from) {
			$where_query .= date_range_loan_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`paid`) as total FROM `loans`LEFT JOIN `loan_to_store` l2s ON (`loans`.`loan_id` = `l2s`.`lloan_id`) WHERE $where_query");
		$statement->execute(array($store_id, 1));
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return $row['total'];
	}

	public function totalDue($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`l2s`.`store_id` = ? AND `l2s`.`status` = ?";
		if ($from) {
			$where_query .= date_range_loan_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`due`) as total FROM `loans`LEFT JOIN `loan_to_store` l2s ON (`loans`.`loan_id` = `l2s`.`lloan_id`) WHERE $where_query");
		$statement->execute(array($store_id, 1));
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return $row['total'];
	}
}