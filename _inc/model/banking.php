<?php
class ModelBanking extends Model 
{
	public function getTransactions($type, $store_id = null, $limit = 100000)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT `bank_transaction_info`.*, `bank_transaction_price`.* FROM `bank_transaction_info` 
			LEFT JOIN `bank_transaction_price` ON `bank_transaction_info`.`ref_no` = `bank_transaction_price`.`ref_no` 
			WHERE `bank_transaction_info`.`store_id` = ? AND `bank_transaction_info`.`transaction_type` = ? ORDER BY `bank_transaction_info`.`created_at` DESC LIMIT $limit");
		$statement->execute(array($store_id, $type));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getTransactionInfo($ref_no, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT `bank_transaction_info`.*, `bank_transaction_price`.* FROM `bank_transaction_info` 
			LEFT JOIN `bank_transaction_price` ON `bank_transaction_info`.`ref_no` = `bank_transaction_price`.`ref_no` 
			WHERE `bank_transaction_info`.`store_id` = ? AND `bank_transaction_info`.`ref_no` = ? ORDER BY `bank_transaction_info`.`ref_no` DESC");
		$statement->execute(array($store_id, $ref_no));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function gePrevWithdraw($price_id, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`ref_no` = `bank_transaction_price`.`ref_no`) 
			WHERE `bank_transaction_info`.`store_id` = ? AND `bank_transaction_price`.`price_id` BETWEEN ? AND ?
			AND `transaction_type` = ?
			ORDER BY `price_id` ASC");
		$statement->execute(array($store_id, 1, ((int)$price_id)-1, 'withdraw'));
		$bank_transaction = $statement->fetch(PDO::FETCH_ASSOC);
		return $bank_transaction ? $bank_transaction['total'] : 0;
	}

	public function getPrevBalance($price_id, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`ref_no` = `bank_transaction_price`.`ref_no`) 
			WHERE `bank_transaction_info`.`store_id` = ? AND `bank_transaction_info`.`transaction_type` = 'deposit'
			AND `bank_transaction_price`.`price_id` BETWEEN ? AND ? 
			ORDER BY `bank_transaction_price`.`price_id` ASC");
		$statement->execute(array($store_id, 1, ((int)$price_id)-1));
		$bank_transaction = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($bank_transaction['total']) ? $bank_transaction['total'] : 0;
	}

	public function getDepositAmount($from = null, $to = null, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`bank_transaction_info`.`store_id` = $store_id AND `bank_transaction_info`.`transaction_type` = 'deposit'";
		if ($from && $to) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`ref_no` = `bank_transaction_price`.`ref_no`) 
			WHERE  $where_query");
		$statement->execute(array());
		$bank_transaction = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($bank_transaction['total']) ? $bank_transaction['total'] : 0;
	}

	public function getWithdrawAmount($from = null, $to = null, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`bank_transaction_info`.`store_id` = $store_id AND `bank_transaction_info`.`transaction_type` = 'withdraw'";
		if ($from && $to) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`ref_no` = `bank_transaction_price`.`ref_no`) 
			WHERE  $where_query");
		$statement->execute(array());
		$bank_transaction = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($bank_transaction['total']) ? $bank_transaction['total'] : 0;
	}

	public function getBalance($from = null, $to = null, $store_id = null) 
	{	
		$deposit = $this->getDepositAmount($from, $to);
		$withdraw = $this->getWithdrawAmount($from, $to);
		return $deposit - $withdraw;
	}
}