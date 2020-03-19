<?php
class ModelExpense extends Model 
{

	public function getTotalExpense($from, $to, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`expenses`.`store_id` = $store_id AND `expenses`.`status` = ?";
		if ($from) {
			$where_query .= date_range_expense_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`expenses`.`amount`) as `total` FROM `expenses` 
			WHERE  $where_query");
		$statement->execute(array(1));
		$expense = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($expense['total']) ? $expense['total'] : 0;
	}
}