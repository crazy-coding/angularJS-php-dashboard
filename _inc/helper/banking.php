<?php
function get_bank_balance($from = null, $to = null, $store = null)
{
	global $registry;
	
	$banking_model = $registry->get('loader')->model('banking');
	return $banking_model->getBalance($from, $to, $store);
}

function get_bank_deposit_amount($from = null, $to = null, $store = null)
{
	global $registry;
	
	$banking_model = $registry->get('loader')->model('banking');
	return $banking_model->getDepositAmount($from, $to, $store);
}

function get_bank_withdraw_amount($from = null, $to = null, $store = null)
{
	global $registry;
	
	$banking_model = $registry->get('loader')->model('banking');
	return $banking_model->getWithdrawAmount($from, $to, $store);
}