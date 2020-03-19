<?php
function settings($field = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('setting');
	$setting = $model->get();

	if ($field && isset($setting[$field])) {
		return $setting[$field];
	} elseif ($field) {
		return null;
	}

	return $setting;
}

function sms_setting($type, $field = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('setting');
	$setting = $model->getSMSSetting($type);

	if ($field && isset($setting[$field])) {
		return $setting[$field];
	} elseif ($field) {
		return null;
	}
	return $setting;
}