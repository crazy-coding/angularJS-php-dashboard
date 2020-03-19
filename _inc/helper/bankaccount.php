<?php
function get_bank_accounts() 
{
	global $registry;
	$model = $registry->get('loader')->model('bankaccount');
	return $model->getBankAccounts();
}

function get_the_bank_account($id, $field = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('bankaccount');
	$bank_account = $model->getBankAccount($id);
	if ($field && isset($bank_account[$field])) {
		return $bank_account[$field];
	} elseif ($field) {
		return;
	}
	return $bank_account;
}

function get_the_account_balance($account_id, $store_id = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('bankaccount');
	return $model->getTheBankBalance($account_id);
}

function get_the_account_deposit($account_id, $store_id = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('bankaccount');
	return $model->getTheDepositAmount($account_id);
}

function get_the_account_withdraw($account_id, $store_id = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('bankaccount');
	return $model->getTheWithdrawAmount($account_id);
}

function get_the_account_transfer_amount_to_other($account_id, $store_id = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('bankaccount');
	return $model->getTheTransferAmountToOther($account_id);
}

function get_the_account_transfer_amount_from_other($account_id, $store_id = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('bankaccount');
	return $model->getTheTransferAmountFromOther($account_id);
}