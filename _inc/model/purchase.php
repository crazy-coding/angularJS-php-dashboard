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
class ModelPurchase extends Model 
{
	public function getInvoices($type, $store_id = null, $limit = 100000) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT `buying_info`.*, `buying_price`.*, `suppliers`.`sup_id`, `suppliers`.`sup_name`, `suppliers`.`sup_mobile`, `suppliers`.`sup_email` FROM `buying_info` 
			LEFT JOIN `buying_price` ON `buying_info`.`invoice_id` = `buying_price`.`invoice_id` 
			LEFT JOIN `suppliers` ON `buying_info`.`sup_id` = `suppliers`.`sup_id` 
			WHERE `buying_info`.`store_id` = ? AND `buying_info`.`inv_type` = ? ORDER BY `buying_info`.`created_at` DESC LIMIT $limit");
		$statement->execute(array($store_id, $type));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getInvoiceInfo($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT `buying_info`.*, `buying_price`.*, `suppliers`.`sup_id`, `suppliers`.`sup_name`, `suppliers`.`sup_mobile` AS `mobile_number`, `suppliers`.`sup_email` FROM `buying_info` 
			LEFT JOIN `buying_price` ON `buying_info`.`invoice_id` = `buying_price`.`invoice_id` 
			LEFT JOIN `suppliers` ON `buying_info`.`sup_id` = `suppliers`.`sup_id` 
			WHERE `buying_info`.`store_id` = ? AND (`buying_info`.`invoice_id` = ? OR (`buying_info`.`sup_id` = ?) AND `buying_info`.`inv_type` IN ('buy','transfer')) ORDER BY `buying_info`.`invoice_id` DESC");
		$statement->execute(array($store_id, $invoice_id, $invoice_id));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getInvoiceItems($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `invoice_id` = ?");
		$statement->execute(array($store_id, $invoice_id));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getTheInvoiceItem($invoice_id, $item_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `invoice_id` = ? AND `item_id` = ?");
		$statement->execute(array($store_id, $invoice_id, $item_id));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getInvoiceItemCount($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `invoice_id` = ?");
		$statement->execute(array($store_id, $invoice_id));
		return $statement->rowCount();
	}

	public function getInvoiceItemTaxes($invoice_id) 
	{
		$statement = $this->db->prepare("SELECT SUM(`item_quantity`) as qty, SUM(`tax`) as tax, SUM(`item_total`) as total, item_tax, `taxrates`.`taxrate_name`, `taxrates`.`taxrate_code` FROM `buying_item` LEFT JOIN `taxrates` ON (`buying_item`.`taxrate_id` = `taxrates`.`taxrate_id`) WHERE invoice_id = ? GROUP BY `buying_item`.`taxrate_id`");
		$statement->execute(array($invoice_id));
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getSellingPrice($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `buying_price` WHERE `store_id` = ? AND invoice_id = ?");
		$statement->execute(array($store_id, $invoice_id));
		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function hasInvoice($invoice_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `buying_info` WHERE `buying_info`.`store_id` = ? AND `buying_info`.`invoice_id` = ?");
		$statement->execute(array($store_id, $invoice_id));
		$invoice = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($invoice['invoice_id']);
	}

	public function hasTheInvoiceItem($invoice_id, $item_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `invoice_id` = ? AND `item_id` = ?");
		$statement->execute(array($store_id, $invoice_id, $item_id));
		return $statement->rowCount();

	}

	public function DeleteTheInvoiceItem($invoice_id, $item_id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("DELETE FROM `buying_item` WHERE `store_id` = ? AND `invoice_id` = ? AND `item_id` = ?");
		$statement->execute(array($store_id, $invoice_id, $item_id));
		return true;
	}

	public function isLastInvoice($sup_id, $invoice_id, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statemtnt = $this->db->prepare("SELECT * FROM `buying_info` WHERE `store_id` = ? AND `sup_id` = ? AND `inv_type` IN ('buy','transfer') ORDER BY `info_id` DESC LIMIT 1");
        $statemtnt->execute(array($store_id, $sup_id));
        $row = $statemtnt->fetch(PDO::FETCH_ASSOC);
        return $row['invoice_id'] == $invoice_id;
	}

	public function getLastInvoice($type = 'sell', $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statemtnt = $this->db->prepare("SELECT * FROM `buying_info` WHERE `store_id` = ? AND `inv_type` = ? ORDER BY `info_id` DESC");
        $statemtnt->execute(array($store_id, $type));
        return $statemtnt->fetch(PDO::FETCH_ASSOC);
	}

	public function getNextInvoice($sup_id, $invoice_id, $type = 'sell', $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$statemtnt = $this->db->prepare("SELECT * FROM `buying_info` WHERE `store_id` = ? AND `sup_id` = ? AND `inv_type` = ? ORDER BY `info_id` DESC");
        $statemtnt->execute(array($store_id, $sup_id, $type));
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
		$where_query = "`store_id` = ? AND `inv_type` IN ('buy','transfer')";
		$where_query .= date_range_filter($from, $to);
		$statement = $this->db->prepare("SELECT * FROM `buying_info` WHERE $where_query");
		$statement->execute(array(store_id()));
		return $statement->rowCount();
	}

	public function total($from = null, $to = null, $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`store_id` = ? AND `inv_type` IN ('buy','transfer')";
		if ($from) {
			$where_query .= date_range_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT * FROM `buying_info` WHERE $where_query");
		$statement->execute(array(store_id()));
		return $statement->rowCount();
	}
}