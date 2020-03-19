<?php
function get_boxes() 
{
	global $registry;
	$model = $registry->get('loader')->model('box');
	return $model->getBoxes();
}

function get_the_box($id, $field = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('box');
	$box = $model->getBox($id);
	if ($field && isset($box[$field])) {
		return $box[$field];
	} elseif ($field) {
		return;
	}
	return $box;
}