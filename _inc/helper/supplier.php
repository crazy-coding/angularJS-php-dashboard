<?php
function get_suppliers() 
{
	global $registry;
	$model = $registry->get('loader')->model('supplier');
	return $model->getSuppliers();
}

function get_the_supplier($id, $field = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('supplier');
	$supplier = $model->getSupplier($id);
	if ($field && isset($supplier[$field])) {
		return $supplier[$field];
	} elseif ($field) {
		return;
	}
	return $supplier;
}

function supplier_selling_price($sup_id, $from, $to)
{
	global $registry;
	$supplier_model = $registry->get('loader')->model('supplier');
	return $supplier_model->getSellingPrice($sup_id, $from, $to);
}

function supplier_buying_price($sup_id, $from, $to)
{
	global $registry;
	$supplier_model = $registry->get('loader')->model('supplier');
	return $supplier_model->getBuyingPrice($sup_id, $from, $to);
}

function total_supplier_today($store_id = null)
{
	global $registry;
	$supplier_model = $registry->get('loader')->model('supplier');
	return $supplier_model->totalToday($store_id);
}

function total_supplier($from = null, $to = null, $store_id = null)
{
	global $registry;
	$supplier_model = $registry->get('loader')->model('supplier');
	return $supplier_model->total($from, $to, $store_id);
}

function total_product_of_supplier($sup_id)
{
	global $registry;
	
	$supplier_model = $registry->get('loader')->model('supplier');
	return $supplier_model->totalProduct($sup_id);

}