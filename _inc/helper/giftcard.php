<?php
function get_giftcards() 
{
	global $registry;
	$model = $registry->get('loader')->model('giftcard');
	return $model->getGiftcards();
}

function get_the_giftcard($id, $field = null) 
{
	global $registry;
	$model = $registry->get('loader')->model('giftcard');
	$giftcard = $model->getGiftcard($id);
	if ($field && isset($giftcard[$field])) {
		return $giftcard[$field];
	} elseif ($field) {
		return;
	}
	return $giftcard;
}

function get_giftcard_total_price($from, $to) 
{
	global $registry;
	$model = $registry->get('loader')->model('giftcard');
	return $model->totalPrice($from, $to);
}

function get_giftcard_total_topup($from, $to) 
{
	global $registry;
	$model = $registry->get('loader')->model('giftcard');
	return $model->totalTopup($from, $to);
}