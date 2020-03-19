<?php
function get_expense_categorys($data = array(), $store = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('expensecategory');

	return $model->getExpenseCategorys($data, $store);
}

function get_expense_category_tree($data = array(), $store = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('expensecategory');

	return $model->getExpenseCategoryTree($data, $store);
}

function get_the_expense_category($id, $field = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('expensecategory');

	$expense_category = $model->getExpenseCategory($id);

	if ($field) {
		return isset($expense_category[$field]) ? $expense_category[$field] : null;
	} elseif ($field) {
		return;
	}
	
	return $expense_category;
}

function get_total_valid_expense_category_item($expense_category_id)
{
	global $registry;

	$model = $registry->get('loader')->model('expensecategory');

	return $model->totalValidItem($expense_category_id);
}

function get_total_expense_category_item($expense_category_id)
{
	global $registry;

	$model = $registry->get('loader')->model('expensecategory');

	return $model->totalItem($expense_category_id);
}