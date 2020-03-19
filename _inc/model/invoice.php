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
class ModelInvoice extends Model 
{
	public function createInvoice($request, $store_id = null)
	{
		global $language;
		$store_id = $store_id ? $store_id : store_id();
		$user_id = user_id();
		$created_at   = date('Y-m-d H:i:s');

		$product_items  = $request->post['product-item'];
	    $total_items  = count($request->post['product-item']);
	    $invoice_note   = $request->post['invoice-note'];
	    $customer_id    = $request->post['customer-id'];
	    $customer_mobile    = $request->post['customer-mobile-number'];
	    $pmethod_id    = $request->post['pmethod-id'];
	    $subtotal      = $request->post['sub-total'];
	    $discount_type  = $request->post['discount-type'];
	    $discount_amount= $request->post['discount-amount'];
	    $shipping_type  = $request->post['shipping-type'] ? $request->post['shipping-type'] : 'plain';
	    $shipping_amount= $request->post['shipping-amount'] ? $request->post['shipping-amount'] : 0;
	    $order_tax     = $request->post['tax-amount'];
	    $payable_amount = $request->post['payable-amount'] + $discount_amount;
	    $paid_amount = $request->post['paid-amount'] ? $request->post['paid-amount'] + $discount_amount : 0;
	    $total_paid = $paid_amount;

	    $details_raw = isset($request->post['payment_details']) ? $request->post['payment_details'] : array();
	    $details = serialize($details_raw);
	    $is_card_payments = false;
	    $card_no = '';

	    if (isset($details_raw['card_no'])) 
	    {
	      $card_no = $details_raw['card_no'];
	      $statement = $this->db->prepare("SELECT * FROM `gift_cards` WHERE `customer_id` = ? AND `card_no` = ? AND `balance` >= ? AND `expiry` > NOW()");
      	  $statement->execute(array($customer_id, $card_no, $payable_amount));
	      $row = $statement->fetch(PDO::FETCH_ASSOC);
	      if ($row) {
	        $is_card_payments = true;
	        $paid_amount = $row['balance'];
	      } else {
	      	global $language;
	        throw new Exception($language->get('error_not_found_or_insufficient_balance'));
	      }
	    }

	    $balance = 0;
	    $due  = ($payable_amount - $paid_amount) > 0 ? ($payable_amount - $paid_amount) : 0;
	    if ($paid_amount > $payable_amount) {
	    	$due = 0;
	    	$balance = $paid_amount - $payable_amount;
	    	$paid_amount = $payable_amount;
	    }

	    $product_discount = $discount_amount / $total_items;
	    $product_tax = $order_tax / $total_items;
	    $payment_status = $due > 0 ? 'due' : 'paid';
	    
	    if ($customer_id == 1 &&  $due > 0) {
	      throw new Exception($language->get('error_walking_customer_can_not_craete_due'));
	    }

      	$invoice_id = generate_invoice_id('sell');

		$item_tax = 0;
		$igst = 0;
		$cgst = 0;
		$sgst = 0;

		$tgst = 0;
		$tigst = 0;
		$tcgst = 0;
		$tsgst = 0;

		foreach ($product_items as $product) 
		{
			$product_id     	= $product['item_id'];
			$product_info 		= get_the_product($product_id);
			$taxrate_id 		= $product_info['taxrate_id'];
			$category_id     	= $product['category_id'];
			$sup_id     		= $product['sup_id'];
			$product_name   	= $product['item_name'];
			$product_quantity  	= $product['item_quantity'];
			$product_price     	= $product['item_price'];
			$product_total     	= $product['item_total'];
			$buying_invoice_id  = NULL;
			$total_buying_price = 0;

			$quantity_exist = $product_quantity;
			$sell_quantity = $product_quantity;
			while ($quantity_exist > 0) 
			{
				$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `item_id` = ? AND `status` = ? AND `item_quantity` > `total_sell`");
				$statement->execute(array($store_id, $product_id, 'active'));
				$buying_item = $statement->fetch(PDO::FETCH_ASSOC);
				if (!$buying_item) {
					$statement = $this->db->prepare("UPDATE `buying_item` SET `status` = ? WHERE `store_id` = ? AND `item_id` = ? AND `status` = ?");
					$statement->execute(array('active', $store_id, $product_id, 'stock'));
				}
				$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `item_id` = ? AND `status` = ? AND `item_quantity` > `total_sell`");
				$statement->execute(array($store_id, $product_id, 'active'));
				$buying_item = $statement->fetch(PDO::FETCH_ASSOC);

				$buying_invoice_id = $buying_item['invoice_id'];
				$stock = $buying_item['item_quantity'] - $buying_item['total_sell'];
				if ($stock < $quantity_exist) {
					$sell_quantity = $stock;
					$quantity_exist = $quantity_exist - $stock;
				} else {
					$sell_quantity = $quantity_exist;
					$quantity_exist = 0;
				}

				$statement = $this->db->prepare("UPDATE `buying_item` SET `total_sell` = `total_sell` + $sell_quantity WHERE `store_id` = ? AND `item_id` = ? AND `status` = ?");
				$statement->execute(array($store_id, $product_id, 'active'));

				$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `item_id` = ? AND `status` = ?");
				$statement->execute(array($store_id, $product_id, 'active'));
				$buying_item = $statement->fetch(PDO::FETCH_ASSOC);

				$total_buying_price += $buying_item['item_buying_price'] * $sell_quantity;

				if ($buying_item['item_quantity'] <= $buying_item['total_sell']) {

					$statement = $this->db->prepare("UPDATE `buying_item` SET `status` = ? WHERE `store_id` = ? AND `item_id` = ? AND `status` = ?");
					$statement->execute(array('sold', $store_id, $product_id, 'active'));

					$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `item_id` = ? AND `status` = ? ORDER BY `id` ASC LIMIT 1");
					$statement->execute(array($store_id, $product_id, 'stock'));
					$buying_item = $statement->fetch(PDO::FETCH_ASSOC);

					$statement = $this->db->prepare("UPDATE `buying_item` SET `status` = ? WHERE `id` = ?");
					$statement->execute(array('active', $buying_item['id']));
				}
			}

	        if (get_the_customer($customer_id, 'customer_state') == get_preference('business_state')) 
	        {
	          $cgst = $item_tax / 2;
	          $sgst = $item_tax / 2;

	          $tcgst += $item_tax / 2;
	          $tsgst += $item_tax / 2;
	        } else {
	          $igst = $item_tax;
	          $tigst += $item_tax;
	        }

			$statement = $this->db->prepare("INSERT INTO `selling_item` (invoice_id, store_id, item_id, category_id, sup_id, item_name, total_buying_price, item_price, item_discount, item_tax, taxrate_id, cgst, sgst, igst, item_quantity, item_total, buying_invoice_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$statement->execute(array($invoice_id, $store_id, $product_id, $category_id, $sup_id, $product_name, $total_buying_price, $product_price, $product_discount, $item_tax, $taxrate_id, $cgst, $sgst, $igst, $product_quantity, $product_total, $buying_invoice_id));

		  	$statement = $this->db->prepare("UPDATE `product_to_store` SET `quantity_in_stock` = `quantity_in_stock` - $product_quantity WHERE `store_id` = ? AND `product_id` = ?");
		  	$statement->execute(array($store_id, $product_id));
		}

		$statement = $this->db->prepare("INSERT INTO `selling_info` (invoice_id, store_id, customer_id, customer_mobile, invoice_note, total_items, payment_status, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$statement->execute(array($invoice_id, $store_id, $customer_id, $customer_mobile, $invoice_note, $total_items, $payment_status, $user_id, $created_at));

		$statement = $this->db->prepare("INSERT INTO `selling_price` (invoice_id, store_id, subtotal, discount_type, item_tax, order_tax, cgst, sgst, igst, shipping_type, shipping_amount, payable_amount, paid_amount, due) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$statement->execute(array($invoice_id, $store_id, $subtotal, $discount_type, $item_tax, $order_tax, $tcgst, $tsgst, $tigst, $shipping_type, $shipping_amount, $payable_amount, $paid_amount, $due
		));

		if ($discount_amount > 0) {
            $statement = $this->db->prepare("INSERT INTO `payments` (type, store_id, invoice_id, amount, note, total_paid, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $statement->execute(array('discount', $store_id, $invoice_id, $discount_amount, 'discount_while_invoice_create', $discount_amount, $user_id, $created_at));
        }

		if ($paid_amount > 0) {
			$statement = $this->db->prepare("INSERT INTO `payments` (store_id, invoice_id, pmethod_id, amount, details, note, total_paid, pos_balance, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    		$statement->execute(array($store_id, $invoice_id, $pmethod_id, $paid_amount-$discount_amount, $details, $invoice_note, $total_paid, $balance, $user_id, $created_at));

		    $statement = $this->db->prepare("UPDATE `selling_info` SET `pmethod_id` = ?, `checkout_status` = ? WHERE `invoice_id` = ? AND `store_id` = ?");
		    $statement->execute(array($pmethod_id, 1, $invoice_id, $store_id));
		}

		$reference_no = generate_customer_transacton_ref_no('purchase');
		$statement = $this->db->prepare("INSERT INTO `customer_transactions` (customer_id, reference_no, type, pmethod_id, description, amount, store_id, ref_invoice_id, created_by, created_at) VALUES (?, ?,?,?,?,?,?,?,?,?)");
		$statement->execute(array($customer_id, $reference_no, 'purchase', $pmethod_id, 'Paid while purchasing', $paid_amount, $store_id, $invoice_id, user_id(), $created_at));

		if ($due > 0) {
			$reference_no = generate_customer_transacton_ref_no('due');
			$statement = $this->db->prepare("INSERT INTO `customer_transactions` (customer_id, reference_no, type, pmethod_id, description, amount, store_id, ref_invoice_id, created_by, created_at) VALUES (?,?,?,?,?,?,?,?,?,?)");
			$statement->execute(array($customer_id, $reference_no, 'due', $pmethod_id, 'Due while purchasing', $due, $store_id, $invoice_id, user_id(), $created_at));

			$update_due = $this->db->prepare("UPDATE `customer_to_store` SET `balance` = `balance` - $due WHERE `customer_id` = ? AND `store_id` = ?");
			$update_due->execute(array($customer_id, $store_id));
		}

		if ($is_card_payments && $card_no) {
	        $statement = $this->db->prepare("UPDATE `gift_cards` SET `balance` = `balance` - $paid_amount  WHERE `card_no` = ?");
	        $statement->execute(array($card_no));
	    }

		return $invoice_id;
	}

	public function getInvoices($type, $store_id = null, $limit = 100000) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_price`.*, `customers`.`customer_id`, `customers`.`customer_name`, `customers`.`customer_mobile`, `customers`.`customer_email` FROM `selling_info` 
			LEFT JOIN `selling_price` ON `selling_info`.`invoice_id` = `selling_price`.`invoice_id` 
			LEFT JOIN `customers` ON `selling_info`.`customer_id` = `customers`.`customer_id` 
			WHERE `selling_info`.`store_id` = ? AND `selling_info`.`inv_type` = ? ORDER BY `selling_info`.`created_at` DESC LIMIT $limit");
		$statement->execute(array($store_id, $type));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getInvoiceInfo($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT `selling_info`.*, `selling_price`.*, `customers`.`customer_id`, `customers`.`customer_name`, `customers`.`customer_mobile` AS `mobile_number`, `customers`.`customer_email` FROM `selling_info` 
			LEFT JOIN `selling_price` ON `selling_info`.`invoice_id` = `selling_price`.`invoice_id` 
			LEFT JOIN `customers` ON `selling_info`.`customer_id` = `customers`.`customer_id` 
			WHERE `selling_info`.`store_id` = ? AND (`selling_info`.`invoice_id` = ? OR (`selling_info`.`customer_id` = ?) AND `selling_info`.`inv_type` = 'sell') ORDER BY `selling_info`.`invoice_id` DESC");
		$statement->execute(array($store_id, $invoice_id, $invoice_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		if ($invoice) {
			$invoice['by'] = get_the_user($invoice['created_by'], 'username');
		}
		return $invoice;
	}

	public function getInvoiceItems($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `selling_item` WHERE `store_id` = ? AND invoice_id = ?");
		$statement->execute(array($store_id, $invoice_id));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getInvoiceItemInfo($invoice_id, $item_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `selling_item` WHERE `store_id` = ? AND  invoice_id = ? AND `item_id` = ?");
		$statement->execute(array($store_id, $invoice_id, $item_id));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getInvoiceItemTaxes($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT SUM(`item_quantity`) as qty, SUM(`tax`) as tax, SUM(`item_total`) as total, item_tax, `taxrates`.`taxrate_name`, `taxrates`.`taxrate_code` FROM `selling_item` LEFT JOIN `taxrates` ON (`selling_item`.`taxrate_id` = `taxrates`.`taxrate_id`) WHERE `store_id` = ? AND invoice_id = ? GROUP BY `selling_item`.`taxrate_id`");
		$statement->execute(array($store_id, $invoice_id));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getSellingPrice($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `selling_price` WHERE `store_id` = ? AND invoice_id = ?");
		$statement->execute(array($store_id, $invoice_id));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function hasInvoice($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `selling_info` WHERE `selling_info`.`store_id` = ? AND `selling_info`.`invoice_id` = ?");
		$statement->execute(array($store_id, $invoice_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($invoice['invoice_id']);

	}

	public function isLastInvoice($customer_id, $invoice_id, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statemtnt = $this->db->prepare("SELECT * FROM `selling_info` WHERE `store_id` = ? AND `customer_id` = ? AND `inv_type` = ? ORDER BY `info_id` DESC LIMIT 1");
        $statemtnt->execute(array($store_id, $customer_id, 'sell'));
        $row = $statemtnt->fetch(PDO::FETCH_ASSOC);
        return $row['invoice_id'] == $invoice_id;
	}

	public function getLastInvoice($type = 'sell', $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statemtnt = $this->db->prepare("SELECT * FROM `selling_info` WHERE `store_id` = ? AND `inv_type` = ? ORDER BY `info_id` DESC");
        $statemtnt->execute(array($store_id, $type));
        return $statemtnt->fetch(PDO::FETCH_ASSOC);
	}

	public function getNextInvoice($customer_id, $invoice_id, $type = 'sell', $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statemtnt = $this->db->prepare("SELECT * FROM `selling_info` WHERE `store_id` = ? AND `customer_id` = ? AND `inv_type` = ? ORDER BY `info_id` DESC");
        $statemtnt->execute(array($store_id, $customer_id, $type));
        $rows = $statemtnt->fetchAll(PDO::FETCH_ASSOC);
        $invoice = null;
        foreach ($rows as $r) {
        	if ($r['invoice_id'] == $invoice_id) {
        		break;
        	}
        	$invoice = $r;
        }
        return $invoice;
	}

	public function totalToday($store_id = null)
	{
		$from = date('Y-m-d');
		$to = date('Y-m-d');
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`store_id` = ? AND `inv_type` = 'sell'";
		$where_query .= date_range_filter($from, $to);
		$statement = $this->db->prepare("SELECT * FROM `selling_info` WHERE $where_query");
		$statement->execute(array($store_id));
		return $statement->rowCount();
	}

	public function total($from = null, $to = null, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`store_id` = ? AND `inv_type` = 'sell'";
		if ($from) {
			$where_query .= date_range_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT * FROM `selling_info` WHERE $where_query");
		$statement->execute(array($store_id));
		return $statement->rowCount();
	}
}