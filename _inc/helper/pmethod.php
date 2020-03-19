<?php
function get_pmethods() 
{
	global $registry;

	$model = $registry->get('loader')->model('pmethod');

	return $model->getPmethods();
}

function get_the_pmethod($id, $field = null)
{
	global $registry;

	$model = $registry->get('loader')->model('pmethod');

	$pmethods = $model->getPmethod($id);

	if ($field && isset($pmethods[$field])) {
		return $pmethods[$field];
	} elseif ($field) {
		return;
	}

	return '';
}