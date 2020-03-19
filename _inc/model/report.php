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
class ModelReport extends Model 
{
	public function getTax($type, $from, $to, $store_id = null) 
	{
		$tax = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter($from, $to);
		if ($type != 'order_tax') {
			$statement = $this->db->prepare("SELECT SUM(`selling_item`.`{$type}`) as total FROM `selling_item` 
			LEFT JOIN `selling_info` ON (`selling_info`.`invoice_id` = `selling_item`.`invoice_id`) 
			WHERE $where_query GROUP BY `selling_item`.`invoice_id`");
		} else {
			$statement = $this->db->prepare("SELECT SUM(`selling_price`.`{$type}`) as total FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE $where_query");
		}
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		if ($invoice) {
			$tax = $invoice['total'];
		}
		return $tax;
	}

	public function getInOrExclusiveTax($type, $from, $to, $store_id = null) 
	{
		$tax = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = $store_id AND `selling_item`.`tax_method` = '{$type}'";
		$where_query .= date_range_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`selling_item`.`item_tax`) as total FROM `selling_info` 
			LEFT JOIN `selling_item` ON (`selling_info`.`invoice_id` = `selling_item`.`invoice_id`) 
			WHERE $where_query GROUP BY `selling_info`.`invoice_id`");
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		if ($invoice) {
			$tax = $invoice['total'];
		}
		return $tax;
	}

	public function getBuyTax($type, $from, $to, $store_id = null) 
	{
		$tax = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`buying_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter2($from, $to);
		if ($type != 'order_tax') {
			$statement = $this->db->prepare("SELECT SUM(`buying_item`.`{$type}`) as total FROM `buying_item` 
			LEFT JOIN `buying_info` ON (`buying_info`.`invoice_id` = `buying_item`.`invoice_id`) 
			WHERE $where_query GROUP BY `buying_item`.`invoice_id`");
		} else {
			$statement = $this->db->prepare("SELECT SUM(`buying_price`.`{$type}`) as total FROM `buying_info` 
			LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`) 
			WHERE $where_query");
		}		
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		if ($invoice) {
			$tax = $invoice['total'];
		}
		return $tax;
	}

	public function getInOrExclusiveBuyTax($type, $from, $to, $store_id = null) 
	{
		$tax = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`buying_info`.`store_id` = $store_id AND `buying_item`.`tax_method` = '{$type}'";
		$where_query .= date_range_filter2($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`buying_item`.`item_tax`) as total FROM `buying_info` 
			LEFT JOIN `buying_item` ON (`buying_info`.`invoice_id` = `buying_item`.`invoice_id`) 
			WHERE $where_query GROUP BY `buying_info`.`invoice_id`");
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		if ($invoice) {
			$tax = $invoice['total'];
		}
		return $tax;
	}

	public function getSellingPrice($from, $to, $store_id = null) 
	{
		$invoice_price = 0;
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`selling_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter($from, $to);

		$statement = $this->db->prepare("SELECT SUM(`selling_price`.`payable_amount`) as total FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE $where_query");

		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);

		if ($invoice) {
			$invoice_price = $invoice['total'];
		}
		return $invoice_price;
	}

	public function getSellingPriceDaywise($year, $month = null, $day = null, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = '$store_id'";
		if ($day) {
		  $where_query .= " AND DAY(`selling_info`.`created_at`) = $day";
		}
		if ($month) {
		  $where_query .= " AND MONTH(`selling_info`.`created_at`) = $month";
		}
		if ($year) {
		  $where_query .= " AND YEAR(`selling_info`.`created_at`) = $year";
		}
		$statement = $this->db->prepare("SELECT SUM(`selling_price`.`payable_amount`) as total FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);

		return $invoice['total'];
	}

	public function getNetSellingPriceDaywise($year, $month = null, $day = null, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = '$store_id'";
		if ($day) {
		  $where_query .= " AND DAY(`selling_info`.`created_at`) = $day";
		}
		if ($month) {
		  $where_query .= " AND MONTH(`selling_info`.`created_at`) = $month";
		}
		if ($year) {
		  $where_query .= " AND YEAR(`selling_info`.`created_at`) = $year";
		}
		$statement = $this->db->prepare("SELECT SUM(`selling_price`.`order_tax`) as order_tax, SUM(`selling_price`.`item_tax`) as item_tax, SUM(`selling_price`.`payable_amount`) as total FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);

		return $invoice['total'] - ($invoice['order_tax'] + $invoice['item_tax']);
	}

	public function getBuyingPriceOfSellDaywise($year, $month, $day, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = '$store_id'";
		if ($day) {
		  $where_query .= " AND DAY(selling_info.created_at) = $day";
		}
		if ($month) {
		  $where_query .= " AND MONTH(selling_info.created_at) = $month";
		}
		if ($year) {
		  $where_query .= " AND YEAR(selling_info.created_at) = $year";
		}
		$statement = $this->db->prepare("SELECT SUM(`selling_item`.`total_buying_price`) as total FROM `selling_info` 
			LEFT JOIN `selling_item` ON `selling_info`.`invoice_id` = `selling_item`.`invoice_id`
			WHERE $where_query");

		$statement->execute(array());
		return $statement->fetch(PDO::FETCH_ASSOC)['total'];
	}

	public function getProfitAmountDaywise($year, $month, $day) 
	{
		return $this->getSellingPriceDaywise($year, $month, $day)  - $this->getBuyingPriceOfSellDaywise($year, $month, $day);
	}

	public function getReceivedAmountDaywise($year, $month = null, $day = null, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = '$store_id'";
		if ($day) {
		  $where_query .= " AND DAY(selling_info.created_at) = $day";
		}
		if ($month) {
		  $where_query .= " AND MONTH(selling_info.created_at) = $month";
		}
		if ($year) {
		  $where_query .= " AND YEAR(selling_info.created_at) = $year";
		}
		$statement = $this->db->prepare("SELECT SUM(`selling_price`.`paid_amount`) as total FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array());
		$paid_amount = $statement->fetch(PDO::FETCH_ASSOC);
		return $paid_amount['total'];
	}

	public function getReceivedAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		return $this->getPaidAmount($from, $to, $store_id) + $this->getAnothrDayDueCollectionAmount($from, $to, $store_id);
	}

	public function getProfitAmount($from, $to, $store_id = null) 
	{
		return ($this->getSellingPrice($from, $to, $store_id) - $this->getBuyingPriceOfSell($from, $to, $store_id));
	}

	public function getBuyingPriceOfSell($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`selling_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter($from, $to);

		$statement = $this->db->prepare("SELECT SUM(`selling_item`.`total_buying_price`) as total FROM `selling_info` 
			LEFT JOIN `selling_item` ON `selling_info`.`invoice_id` = `selling_item`.`invoice_id`
			WHERE $where_query");

		$statement->execute(array());

		return $statement->fetch(PDO::FETCH_ASSOC)['total'];
	}

	public function getOrderTaxAmountDaywise($year, $month = null, $day = null, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`selling_info`.`store_id` = '$store_id'";
		if ($day) {
		  $where_query .= " AND DAY(`selling_info`.`created_at`) = $day";
		}
		if ($month) {
		  $where_query .= " AND MONTH(`selling_info`.`created_at`) = $month";
		}
		if ($year) {
		  $where_query .= " AND YEAR(`selling_info`.`created_at`) = $year";
		}

		$statement = $this->db->prepare("SELECT SUM(`selling_price`.`order_tax`) as total FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array());
		$order_tax = $statement->fetch(PDO::FETCH_ASSOC);

		return $order_tax['total'];
	}

	public function getPaidAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`selling_price`.`paid_amount`) as total FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return $row['total'];
	}

	public function getDiscountAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`store_id` = $store_id AND `type` = 'discount'";
		$where_query .= date_range_sell_payments_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`amount`) as total FROM `payments` 
			WHERE $where_query");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return $row['total'];
	}

	public function getDueAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`selling_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`selling_price`.`due`) as due FROM `selling_info` 
			LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`)
			WHERE $where_query");
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);

		return $invoice['due'];
	}

	public function getBuyingDueAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`buying_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter2($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`buying_price`.`due`) as due FROM `buying_info` 
			LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`)
			WHERE $where_query");
		$statement->execute(array());
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);

		return $invoice['due'];
	}

	public function getBuyingPrice($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`buying_info`.`inv_type` != 'expense' AND `buying_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter2($from, $to);

		$statement = $this->db->prepare("SELECT SUM(`buying_price`.`payable_amount`) as total FROM `buying_info` 
			LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array());
		$buying_price = $statement->fetch(PDO::FETCH_ASSOC);

		return $buying_price['total'];
	}

	public function getBuyingTotalPaidAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`buying_info`.`inv_type` != 'expense' AND `buying_info`.`store_id` = ?";
		$where_query .= date_range_filter2($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`buying_price`.`paid_amount`) as total FROM `buying_info` 
			LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array($store_id));
		$buying_price = $statement->fetch(PDO::FETCH_ASSOC);

		return $buying_price['total'];
	}

	public function getExpenseAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`buying_info`.`inv_type` = 'expense' AND `buying_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter2($from, $to);

		$statement = $this->db->prepare("SELECT SUM(`buying_price`.`paid_amount`) as total FROM `buying_info` 
			LEFT JOIN `buying_price` ON (`buying_info`.`invoice_id` = `buying_price`.`invoice_id`) 
			WHERE $where_query");
		$statement->execute(array());
		$buying_price = $statement->fetch(PDO::FETCH_ASSOC);

		return $buying_price['total'];
	}

	public function getDueCollectionAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`type` = ? AND `store_id` = ?";
		$where_query .= date_range_sell_payments_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`amount`) as due_paid FROM `payments` 
			WHERE $where_query");
		$statement->execute(array('due_paid', $store_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return $invoice['due_paid'];
	}

	public function getAnothrDayDueCollectionAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`type` = ? AND `store_id` = ?";
		$where_query .= date_range_sell_payments_reverse_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`amount`) as due_paid FROM `payments` 
			WHERE $where_query");
		$statement->execute(array('due_paid', $store_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return $invoice['due_paid'];
	}

	public function getAnothrDayDuePaidAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`type` = ? AND `store_id` = ?";
		$where_query .= date_range_buy_payments_reverse_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`amount`) as due_paid FROM `buying_payments` 
			WHERE $where_query");
		$statement->execute(array('due_paid', $store_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return $invoice['due_paid'];
	}

	public function getBuyingDuePaidAmount($from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`type` IN ('due_paid','transfer') AND `store_id` = ?";
		$where_query .= date_range_buying_payments_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`amount`) as due_paid FROM `buying_payments` 
			WHERE $where_query");
		$statement->execute(array($store_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return $invoice['due_paid'] > 0 ? $invoice['due_paid'] : 0;
	}

	public function getTopProduct($from, $to, $limit = 3, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();

		$where_query = "`selling_info`.`store_id` = '$store_id'";
		$where_query .= date_range_filter($from, $to);

		$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_item`.`item_name`, SUM(`selling_item`.`item_quantity`) as quantity FROM `selling_info` 
			LEFT JOIN `selling_item` ON (`selling_info`.`invoice_id` = `selling_item`.`invoice_id`)
			WHERE $where_query
			GROUP BY `selling_item`.`item_id` ORDER BY `quantity` 
			DESC LIMIT $limit");
		$statement->execute(array());

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function totalOutOfStock($store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement =  $this->db->prepare("SELECT * FROM `products` 
			LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`) 
			WHERE `p2s`.`store_id` = ? AND (`p2s`.`quantity_in_stock` <= `alert_quantity`) AND `p2s`.`status` = 1");
		$statement->execute(array($store_id));
		
		return $statement->rowCount();
	}

	public function totalExpired($store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement =  $this->db->prepare("SELECT * FROM `products` LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`) WHERE `p2s`.`store_id` = ? AND `e_date` <= CURDATE() AND `p2s`.`status` = 1");
		$statement->execute(array($store_id));
		
		return $statement->rowCount();
	}

	public function getTotalCashReceivedBy($type, $type_id, $from = null, $to = null, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = ? AND `selling_info`.`status` = 1";
		$where_query .= date_range_filter($from, $to);
		switch ($type) {
			case 'userwise':
				$where_query .= " AND `selling_info`.`inv_type` = 'sell' AND `selling_info`.`created_by` = $type_id";
				$statement = $this->db->prepare("SELECT SUM(`selling_price`.`paid_amount`) as total FROM `selling_info` 
					LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
					WHERE $where_query");
				$statement->execute(array($store_id));
				$invoice = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($invoice['total']) ? (float)$invoice['total'] : 0;
				$prev_due_collection = $this->getTotalPrevDueCollectionBy($type_id, $from, $to);
				$total = $total+$prev_due_collection;
				break;
			case 'invoicewise':
				$where_query .= " AND `selling_info`.`inv_type` = 'sell' AND `selling_info`.`invoice_id` = '$type_id'";
				$statement = $this->db->prepare("SELECT SUM(`selling_price`.`paid_amount`) as total FROM `selling_info` 
					LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
					WHERE $where_query");
				$statement->execute(array($store_id));
				$invoice = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($invoice['total']) ? (float)$invoice['total'] : 0;
				break;
		}	
		return $total;
	}

	public function getTotalDueAmountBy($type, $type_id, $from = null, $to = null, $store_id = null) 
	{
		$edited_invoice_amount = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = ? AND `selling_info`.`status` = 1";
		$where_query .= date_range_filter($from, $to);
		switch ($type) {
			case 'invoicewise':
				$where_query .= " AND `selling_info`.`invoice_id` = '$type_id'";
				$statement = $this->db->prepare("SELECT SUM(`selling_price`.`due`) as total FROM `selling_info` 
					LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
					WHERE $where_query");
				$statement->execute(array($store_id));
				$invoice = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($invoice['total']) ? (float)$invoice['total'] : 0;
				break;
			case 'userwise':
				$where_query .= " AND `inv_type` = 'sell' AND `selling_info`.`created_by` = $type_id";
				$statement = $this->db->prepare("SELECT SUM(`selling_price`.`due`) as total FROM `selling_info` 
					LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
					WHERE $where_query");
				$statement->execute(array($store_id));
				$invoice = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($invoice['total']) ? (float)$invoice['total'] : 0;
				break;
		}	
		return $total + $edited_invoice_amount;
	}

	public function getTotalDueCollectionBy($user_id, $from = null, $to = null) 
	{
		$total = 0;
		$from = $from ? $from : date('Y-m-d');
		$to = $to ? $to : date('Y-m-d');
		$where_query = "`payments`.`type`='due_paid' AND `payments`.`created_by` = ?";
		$where_query .= date_range_sell_payments_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`payments`.`amount`) as total, `created_at` FROM `payments` 
			WHERE $where_query");
		$statement->execute(array($user_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return $invoice['total'];
	}

	public function getTotalPrevDueCollectionBy($user_id, $from = null, $to = null) 
	{
		$total = 0;
		$from = $from ? $from : date('Y-m-d');
		$to = $to ? $to : date('Y-m-d');
		$where_query = "`payments`.`type`='due_paid' AND `payments`.`created_by` = ?";
		$where_query .= date_range_sell_payments_reverse_filter($from, $to);
		$statement = $this->db->prepare("SELECT SUM(`payments`.`amount`) as total, `created_at` FROM `payments` 
			WHERE $where_query");
		$statement->execute(array($user_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return $invoice['total'];
	}

	public function getTotalTaxAmountBy($type, $type_id, $from = null, $to = null, $store_id = null) 
	{
		$total = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`inv_type` = 'sell' AND `selling_info`.`store_id` = ? AND `selling_info`.`status` = 1";
		$where_query .= date_range_filter($from, $to);
		switch ($type) {
			case 'invoicewise':
				$where_query .= " AND `selling_info`.`invoice_id` = '$type_id'";
				$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_price`.`order_tax` as order_tax, `selling_price`.`item_tax` as item_tax 
				FROM `selling_info` 
				LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
				WHERE $where_query");
				break;
			case 'userwise':
				$where_query .= " AND `selling_info`.`created_by` = $type_id";
				$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_price`.`order_tax` as order_tax, `selling_price`.`item_tax` as item_tax 
				FROM `selling_info` 
				LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
				WHERE $where_query");
				break;		
		}
		$statement->execute(array($store_id));
		$invoices = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($invoices as $inv) {
			$total += $inv['order_tax'] + $inv['item_tax'];
		}
		return $total;
	}

	public function getTotalDiscountAmountBy($type, $type_id, $from = null, $to = null, $store_id = null) 
	{
		$total = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`type` = 'discount' AND `store_id` = ?";
		$where_query .= date_range_sell_payments_filter($from, $to);
		switch ($type) {
			case 'invoicewise':
				$where_query .= " AND `invoice_id` = '$type_id'";
				$statement = $this->db->prepare("SELECT SUM(`amount`) as total 
				FROM `payments` 
				WHERE $where_query");
				$statement->execute(array($store_id));
				$row = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($row['total']) ? $row['total'] : 0;
				break;
			case 'userwise':
				$where_query .= " AND `created_by` = $type_id";
				$statement = $this->db->prepare("SELECT SUM(`amount`) as total 
				FROM `payments` 
				WHERE $where_query");
				$statement->execute(array($store_id));
				$row = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($row['total']) ? $row['total'] : 0;
				break;
			case 'itemwise':
				$item_count = count(get_invoice_items($type_id, $store_id));
				$where_query .= " AND `invoice_id` = '$type_id'";
				$statement = $this->db->prepare("SELECT SUM(`amount`) as total 
				FROM `payments` 
				WHERE $where_query");
				$statement->execute(array($store_id));
				$row = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($row['total']) ? $row['total'] / $item_count : 0;
				break;			
		}
		return $total;
	}

	public function getTotalInvoiceAmountBy($type, $type_id, $from = null, $to = null, $store_id = null) 
	{
		$total = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = ? AND `selling_info`.`status` = 1";
		$where_query .= date_range_filter($from, $to);
		switch ($type) {
			case 'invoicewise':
				$where_query .= " AND `inv_type` = 'sell' AND `selling_info`.`invoice_id` = '$type_id'";
				$statement = $this->db->prepare("SELECT SUM(`selling_price`.`subtotal`) as total FROM `selling_info` 
					LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
					WHERE $where_query");
				$statement->execute(array($store_id));
				$invoice = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($invoice['total']) ? (float)$invoice['total'] : 0;
				break;
			case 'userwise':
				$where_query .= " AND `inv_type` = 'sell' AND `selling_info`.`created_by` = $type_id";
				$statement = $this->db->prepare("SELECT SUM(`selling_price`.`subtotal`) as total FROM `selling_info` 
					LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) 
					WHERE $where_query");
				$statement->execute(array($store_id));
				$invoice = $statement->fetch(PDO::FETCH_ASSOC);
				$total = isset($invoice['total']) ? (float)$invoice['total'] : 0;
				break;
		}	
		return $total;
	}

	public function userTotalInvoiceCount($user_id = null, $from = null, $to = null, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`selling_info`.`store_id` = ? AND `selling_info`.`status` = 1 AND `created_by` = $user_id AND `inv_type` = 'sell'";
		$where_query .= date_range_filter($from, $to);
		$statement = $this->db->prepare("SELECT * FROM `selling_info` WHERE $where_query");
		$statement->execute(array($store_id));
		return $statement->rowCount();
	}
}