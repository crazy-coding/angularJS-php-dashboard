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
class ModelBankAccount extends Model 
{
	public function addBankAccount($data) 
	{		
    	$statement = $this->db->prepare("INSERT INTO `bank_accounts` (account_name, account_details, initial_balance, account_no, contact_person, phone_number, url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['account_name'], $data['account_details'], $data['initial_balance'], $data['contact_person'], $data['account_no'], $data['phone_number'], $data['url'], date('Y-m-d H:i:s')));
    	$account_id = $this->db->lastInsertId();

    	if (isset($data['account_store'])) {
			foreach ($data['account_store'] as $store_id) {
				$statement = $this->db->prepare("INSERT INTO `bank_account_to_store` SET `account_id` = ?, `store_id` = ?");
				$statement->execute(array($account_id, $store_id));
			}
		}

		$this->updateStatus($account_id, $data['status']);
		$this->updateSortOrder($account_id, $data['sort_order']);

    	return $account_id; 
	}

	public function updateStatus($account_id, $status, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `bank_account_to_store` SET `status` = ? WHERE `store_id` = ? AND `account_id` = ?");
		$statement->execute(array((int)$status, $store_id, (int)$account_id));
	}

	public function updateSortOrder($account_id, $sort_order, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("UPDATE `bank_account_to_store` SET `sort_order` = ? WHERE `store_id` = ? AND `account_id` = ?");
		$statement->execute(array((int)$sort_order, $store_id, (int)$account_id));
	}

	public function editBankAccount($account_id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `bank_accounts` SET `account_name` = ?, `account_details` = ?, `account_no` = ?, `contact_person` = ?, `phone_number` = ?, `url` = ? WHERE id = ? ");
    	$statement->execute(array($data['account_name'], $data['account_details'], $data['account_no'], $data['contact_person'], $data['phone_number'], $data['url'], $account_id));

    	// insert box into store
    	if (isset($data['account_store'])) {

    		$store_ids = array();

			foreach ($data['account_store'] as $store_id) {

				$statement = $this->db->prepare("SELECT * FROM `bank_account_to_store` WHERE `store_id` = ? AND `account_id` = ?");
			    $statement->execute(array($store_id, $account_id));
			    $box = $statement->fetch(PDO::FETCH_ASSOC);
			    if (!$box) {
			    	$statement = $this->db->prepare("INSERT INTO `bank_account_to_store` SET `account_id` = ?, `store_id` = ?");
					$statement->execute(array((int)$account_id, (int)$store_id));
			    }

			    $store_ids[] = $store_id;
			}

			// delete unwanted store
			if (!empty($store_ids)) {

				// get unwanted stores
				$statement = $this->db->prepare("SELECT * FROM `bank_account_to_store` WHERE `store_id` NOT IN (" . implode(',', $store_ids) . ")");
				$statement->execute();
				$unwanted_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($unwanted_stores as $store) {

					$store_id = $store['store_id'];

				    // delete unwanted store link
					$statement = $this->db->prepare("DELETE FROM `bank_account_to_store` WHERE `store_id` = ? AND `account_id` = ?");
					$statement->execute(array($store_id, $account_id));
				}				
			}
		}

		$this->updateStatus($account_id, $data['status']);
		$this->updateSortOrder($account_id, $data['sort_order']);

    	return $account_id;
	}

	public function deleteBankAccount($account_id) 
	{
    	$statement = $this->db->prepare("DELETE FROM `bank_accounts` WHERE `id` = ? LIMIT 1");
    	$statement->execute(array($account_id));

    	$statement = $this->db->prepare("DELETE FROM `bank_account_to_store` WHERE `account_id` = ?");
    	$statement->execute(array($account_id));

        return $account_id;
	}

	public function getBankAccount($account_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `bank_accounts` 
			LEFT JOIN `bank_account_to_store` as ba2s ON (`bank_accounts`.`id` = `ba2s`.`account_id`)  
	    	WHERE `ba2s`.`store_id` = ? AND `bank_accounts`.`id` = ?");
	  	$statement->execute(array($store_id, $account_id));
	  	$account = $statement->fetch(PDO::FETCH_ASSOC);

	  	// fetch stores related to boxs
	    $statement = $this->db->prepare("SELECT `store_id` FROM `bank_account_to_store` WHERE `account_id` = ?");
	    $statement->execute(array($account_id));
	    $all_stores = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $stores = array();
	    foreach ($all_stores as $store) {
	    	$stores[] = $store['store_id'];
	    }

	    $account['stores'] = $stores;

	    return $account;
	}

	public function getTheBankBalance($account_id, $store_id = null) 
	{
		$balance = 0;
		$bank_account = $this->getBankAccount($account_id, $store_id);
		if (isset($bank_account['account_id'])) {
			$balance = ($bank_account['deposit'] + $bank_account['transfer_from_other']) - ($bank_account['withdraw'] + $bank_account['transfer_to_other']);
		}
	    return $balance;
	}

	public function getTheDepositAmount($account_id, $store_id = null) 
	{
		$amount = 0;
		$bank_account = $this->getBankAccount($account_id, $store_id);
		if (isset($bank_account['account_id'])) {
			$amount = $bank_account['deposit'];
		}
	    return $amount;
	}

	public function getTheWithdrawAmount($account_id, $store_id = null) 
	{
		$amount = 0;
		$bank_account = $this->getBankAccount($account_id, $store_id);
		if (isset($bank_account['account_id'])) {
			$amount = $bank_account['withdraw'];
		}
	    return $amount;
	}

	public function getTheTransferAmountToOther($account_id, $store_id = null) 
	{
		$amount = 0;
		$bank_account = $this->getBankAccount($account_id, $store_id);
		if (isset($bank_account['account_id'])) {
			$amount = $bank_account['transfer_to_other'];
		}
	    return $amount;
	}

	public function getTheTransferAmountFromOther($account_id, $store_id = null) 
	{
		$amount = 0;
		$bank_account = $this->getBankAccount($account_id, $store_id);
		if (isset($bank_account['account_id'])) {
			$amount = $bank_account['transfer_from_other'];
		}
	    return $amount;
	}

	public function getBankAccounts($data = array(), $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `bank_accounts` LEFT JOIN `bank_account_to_store` ba2s ON (`bank_accounts`.`id` = `ba2s`.`account_id`) WHERE `ba2s`.`store_id` = ? AND `ba2s`.`status` = ?";

		if (isset($data['filter_name'])) {
			$sql .= " AND `account_name` LIKE '" . $data['filter_name'] . "%'";
		}

		if (isset($data['filter_no'])) {
			$sql .= " AND `account_no` LIKE '" . $data['filter_no'] . "%'";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `bank_accounts`.`id`";

		$sort_data = array(
			'account_name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `account_name`";
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

	public function total($store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `bank_accounts`LEFT JOIN `bank_accounts_to_store` ba2s ON (`bank_accounts`.`id` = `ba2s`.`account_id`) where `ba2s`.`store_id` = ? AND `b2s`.`status` = ?");
		$statement->execute(array($store_id, 1));
		
		return $statement->rowCount();
	}
}