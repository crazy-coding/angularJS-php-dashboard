<?php
function get_total_expense($from, $to, $store_id = null) 
{
	global $registry;

	$expense_model = $registry->get('loader')->model('expense');
	return $expense_model->getTotalExpense($from, $to, $store_id);
}