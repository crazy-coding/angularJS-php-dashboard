<?php
function get_taxrates() 
{
	global $registry;
	$model = $registry->get('loader')->model('taxrate');
	return $model->getTaxrates();
}

function get_the_taxrate($id, $field = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('taxrate');
	$taxrate = $model->getTaxrate($id);
	if ($field && isset($taxrate[$field])) {
		return $taxrate[$field];
	} elseif ($field) {
		return;
	}
	return $taxrate;
}