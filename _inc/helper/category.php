<?php
function get_categorys($data = array(), $store = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('category');

	return $model->getCategorys($data, $store);
}

function get_category_tree($data = array(), $store = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('category');

	return $model->getCategoryTree($data, $store);
}

function get_the_category($id, $field = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('category');

	$category = $model->getCategory($id);

	if ($field) {
		return isset($category[$field]) ? $category[$field] : null;
	} elseif ($field) {
		return;
	}
	
	return $category;
}

function get_total_valid_category_item($category_id)
{
	global $registry;

	$model = $registry->get('loader')->model('category');

	return $model->totalValidItem($category_id);
}

function get_total_category_item($category_id)
{
	global $registry;

	$model = $registry->get('loader')->model('category');

	return $model->totalItem($category_id);
}