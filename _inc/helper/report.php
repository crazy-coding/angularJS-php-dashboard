<?php
function selling_price($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getSellingPrice($from, $to);
}

function sell_buying_price($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getBuyingPriceOfSell($from, $to);
}

function discount_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getDiscountAmount($from, $to);
}

function buying_price($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getBuyingPrice($from, $to);
}

function due_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getDueAmount($from, $to);
}

function buying_due_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getBuyingDueAmount($from, $to);
}

function due_collection_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getDueCollectionAmount($from, $to);
}

function anotherday_due_collection_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getAnothrDayDueCollectionAmount($from, $to);
}

function anotherday_due_paid_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getAnothrDayDuePaidAmount($from, $to);
}

function buying_due_paid_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getBuyingDuePaidAmount($from, $to);
}

function received_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getReceivedAmount($from, $to);
}

function buying_total_paid($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getBuyingTotalPaidAmount($from, $to);
}

function profit_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getProfitAmount($from, $to);
}

function get_tax($type, $from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getTax($type, $from, $to);
}

function get_in_or_exclusive_tax($type, $from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getInOrExclusiveTax($type, $from, $to);
}

function get_buy_tax($type, $from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getBuyTax($type, $from, $to);
}

function get_in_or_exclusive_buy_tax($type, $from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getInOrExclusiveBuyTax($type, $from, $to);
}

function selling_price_daywise($year, $month = null, $day = null) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getSellingPriceDaywise($year, $month, $day);
}

function received_amount_daywise($year, $month = null, $day = null) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getReceivedAmountDaywise($year, $month, $day);
}

function profit_amount_daywise($year, $month = null, $day = null) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getProfitAmountDaywise($year, $month, $day);
}

function tax_amount_daywise($year, $month = null, $day = null) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getTaxAmountDaywise($year, $month, $day);
}

function expense_amount($from, $to) 
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getExpenseAmount($from, $to);
}

function purchase_in_year($year) 
{
	$totalPurchase = [];
	for ($i=1; $i < 12; $i++) { 
		$totalPurchase[$i] = purchase_price($year, $i);
	}
	return $totalPurchase;
}

function sell_in_year($year) 
{
	$totalSell = [];
	for ($i=1; $i < 12; $i++) { 
		$totalSell[$i] = sell_price($year, $i);
	}
	return $totalSell;
}

function top_product($from, $to, $limit = 3)
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->getTopProduct($from, $to, $limit);
}

function total_out_of_stock()
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->totalOutOfStock();
}

function total_expired()
{
	global $registry;

	$report_model = $registry->get('loader')->model('report');
	return $report_model->totalExpired();
}

function get_balance($customer_id, $index = null) 
{	
	global $registry;

	$customer_model = $registry->get('loader')->model('customer');
	return $customer_model->getBalance($customer_id, $index);
}

function customer_avatar($sex)
{
	global $registry;

	$customer_model = $registry->get('loader')->model('customer');

	return $customer_model->getCustomerAvatar($sex);
}

function get_quantity_in_stock($p_id, $store_id = null)
{
	$store_id = $store_id ? $store_id : store_id();

	global $registry;

	$product_model = $registry->get('loader')->model('product');

	return $product_model->getQtyInStock($p_id, $store_id);
}