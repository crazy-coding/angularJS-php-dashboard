<?php
function get_loans() 
{
	global $registry;
	$model = $registry->get('loader')->model('loan');
	return $model->getLoans();
}

function get_the_loan($id, $field = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('loan');
	$loan = $model->getLoan($id);
	if ($field && isset($loan[$field])) {
		return $loan[$field];
	} elseif ($field) {
		return;
	}
	return $loan;
}

function get_total_loan($from, $to, $store_id = null)
{
	global $registry;
	$model = $registry->get('loader')->model('loan');
	return $model->totalLoan($from, $to, $store_id);
}

function get_total_loan_paid($from, $to, $store_id = null)
{
	global $registry;
	$model = $registry->get('loader')->model('loan');
	return $model->totalPaid($from, $to, $store_id);
}

function get_total_laon_due($from, $to, $store_id = null)
{
	global $registry;
	$model = $registry->get('loader')->model('loan');
	return $model->totalDue($from, $to, $store_id);
}