<?php
function get_products() 
{
	global $registry;
	$model = $registry->get('loader')->model('product');
	return $model->getProducts();
}

function get_the_product($id, $field = null, $store_id = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('product');
	$product = $model->getProduct($id, $store_id);
	if ($field && isset($product[$field])) {
		return $product[$field];
	} elseif ($field) {
		return;
	}
	return $product;
}

function product_selling_price($p_id, $from, $to)
{
	global $registry;
	$product_model = $registry->get('loader')->model('product');
	return $product_model->getSellingPrice($p_id, $from, $to);
}

function product_buying_price($p_id, $from, $to)
{
	global $registry;
	$product_model = $registry->get('loader')->model('product');
	return $product_model->getBuyingPrice($p_id, $from, $to);
}

function total_product_today($store_id = null)
{
	global $registry;
	$product_model = $registry->get('loader')->model('product');
	return $product_model->totalToday($store_id);

}

function total_product($from = null, $to = null, $store_id = null)
{
	global $registry;
	$product_model = $registry->get('loader')->model('product');
	return $product_model->total($from, $to, $store_id);

}

function total_trash_product()
{
	global $registry;
	$product_model = $registry->get('loader')->model('product');
	return $product_model->totalTrash();
}