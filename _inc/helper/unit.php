<?php
function get_units() 
{
	global $registry;

	$model = $registry->get('loader')->model('unit');

	return $model->getUnits();
}

function get_the_unit($id, $field = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('unit');

	$unit = $model->getUnit($id);

	if ($field && isset($unit[$field])) {
		return $unit[$field];
	} elseif ($field) {
		return;
	}

	return $unit;
}