<?php
function get_currencies() 
{
	global $registry;

	$model = $registry->get('loader')->model('currency');

	return $model->getCurrencies();
}

function get_the_curreny($id, $field = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('currencies');

	$currencies = $model->getCurrency($id);

	if ($field && isset($currencies[$field])) {
		return $currencies[$field];
	} elseif ($field) {
		return;
	}

	return $currencies;
}

function get_currency_symbol()
{
	global $currency;

	return $currency->getSymbolLeft() ? $currency->getSymbolLeft() : $currency->getSymbolRight();
}

function get_currency_code()
{
	global $currency;
	return $currency->getCode();
}

function currency_id($currency_code = "")
{
	global $currency;

	return $currency->getId($currency_code);
}

function currency_format($value) 
{
	return number_format($value, 2);
}