<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	Modern POS
| -----------------------------------------------------
| AUTHOR:			ITSOLUTION24.COM
| -----------------------------------------------------
| EMAIL:			info@itsolution24.com
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY ITSOLUTION24.COM
| -----------------------------------------------------
| WEBSITE:			http://itsolution24.com
| -----------------------------------------------------
*/
class ModelPayment extends Model 
{
	public function getPayments($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

	    $statement = $this->db->prepare("SELECT * FROM `payments` 
	    	WHERE `store_id` = ? AND `invoice_id` = ? ORDER BY `type` DESC");
	    $statement->execute(array($store_id, $invoice_id));
	    $payments = $statement->fetchAll(PDO::FETCH_ASSOC);

	    $payment_array = array();
	    $i = 0;
	    foreach ($payments as $payment) {
	    	$payment_array[$i] = $payment;
	    	$payment_array[$i]['name'] = get_the_pmethod($payment['pmethod_id'], 'name');
	    	$payment_array[$i]['by'] = get_the_user($payment['created_by'], 'username');
	    	$i++;
	    }

	    return $payment_array;
	}

	public function getPurchasePayments($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
	    $statement = $this->db->prepare("SELECT * FROM `buying_payments` 
	    	WHERE `store_id` = ? AND `invoice_id` = ? ORDER BY `type` DESC");
	    $statement->execute(array($store_id, $invoice_id));
	    $buying_payments = $statement->fetchAll(PDO::FETCH_ASSOC);
	    $payment_array = array();
	    $i = 0;
	    foreach ($buying_payments as $payment) {
	    	$payment_array[$i] = $payment;
	    	$payment_array[$i]['name'] = get_the_pmethod($payment['pmethod_id'], 'name');
	    	$payment_array[$i]['by'] = get_the_user($payment['created_by'], 'username');
	    	$i++;
	    }
	    return $payment_array;
	}
}