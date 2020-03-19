<?php
function get_customers($data = array()) 
{
	global $registry;
	$model = $registry->get('loader')->model('customer');
	return $model->getCustomers($data);
}

function get_the_customer($id, $field = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('customer');
	$customer = $model->getCustomer($id);
	if ($field && isset($customer[$field])) {
		return $customer[$field];
	} elseif ($field) {
		return;
	}
	return $customer;
}

function total_customer_today($store_id = null)
{
	global $registry;
	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->totalToday($store_id);
}

function total_customer($from = null, $to = null, $store_id = null)
{
	global $registry;
	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->total($from, $to, $store_id);
}

function get_customer_due($customer_id, $store_id = null, $index = 'due_amount')
{
	global $registry;
	
	$customer_model = $registry->get('loader')->model('customer');
	
	return $customer_model->getDueAmount($customer_id, $store_id, $index);
}

function recent_customers($limit)
{
	global $registry;

	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->getRecentCustomers($limit);
}

function customer_total_buying_amount($customer_id) 
{
	global $registry;

	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->getTotalBuyingAmount($customer_id);
}

function customer_total_invoice($customer_id = null) 
{
	global $registry;

	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->getTotalInvoiceNumber($customer_id);
}

function best_customer($field) 
{
	global $registry;

	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->getBestCustomer($field);
}

function get_best_customer_buy_amount() 
{
	global $registry;

	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->getBestCustomerTotalBuyAmount();
}