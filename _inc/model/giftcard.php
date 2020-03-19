
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
class ModelGiftcard extends Model 
{
	public function addGiftcard($data) 
	{		
    	$statement = $this->db->prepare("INSERT INTO `gift_cards` (card_no, value, balance, customer_id, expiry, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['card_no'], $data['giftcard_value'], $data['balance'], $data['customer_id'], $data['expiry'], user_id()));
    	$id = $this->db->lastInsertId();

    	$statement = $this->db->prepare("UPDATE `customers` SET `is_giftcard` = ? WHERE customer_id = ? ");
    	$statement->execute(array(1, $data['customer_id']));

    	if ($data['balance'] > 0) {
    		$statement = $this->db->prepare("INSERT INTO `gift_card_topups` (card_id, amount, created_by) VALUES (?, ?, ?)");
    		$statement->execute(array($data['card_no'], $data['balance'], user_id()));
    	}

    	return $id; 
	}

	public function editGiftcard($id, $data) 
	{
    	$statement = $this->db->prepare("UPDATE `gift_cards` SET `card_no` = ?, `value` = ?, `expiry` = ? WHERE id = ? ");
    	$statement->execute(array($data['card_no'], $data['giftcard_value'], $data['expiry'], $id));
    	return $id;
	}

	public function topupGiftcard($card_no, $amount, $expiry) 
	{
		$statement = $this->db->prepare("UPDATE `gift_cards` SET `balance` = `balance` + $amount, `expiry` = ? WHERE `card_no` = ? ");
    	$statement->execute(array($expiry, $card_no));

    	$statement = $this->db->prepare("INSERT INTO `gift_card_topups` (card_id, amount, created_by) VALUES (?, ?, ?)");
    	$statement->execute(array($card_no, $amount, user_id()));
    	return $card_no;
	}

	public function deleteGiftcard($id) 
	{
		$giftcard = $this->getGiftcard($id);
		if ($giftcard) {
			$statement = $this->db->prepare("DELETE FROM `gift_cards` WHERE `id` = ? LIMIT 1");
	    	$statement->execute(array($id));

	    	$statement = $this->db->prepare("DELETE FROM `gift_card_topups` WHERE `card_id` = ?");
	    	$statement->execute(array($giftcard['card_no']));

	    	$statement = $this->db->prepare("UPDATE `customers` SET `is_giftcard` = ? WHERE customer_id = ? ");
    		$statement->execute(array(0, $giftcard['customer_id']));
		}
        return $id;
	}

	public function getGiftcard($id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `gift_cards` WHERE (`id` = ? OR `card_no` = ?)");
	  	$statement->execute(array($id, $id));
	  	$giftcard = $statement->fetch(PDO::FETCH_ASSOC);
	    return $giftcard;
	}

	public function getGiftcards($data = array(), $store_id = null) {

		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `gift_cards` WHERE `expiry` > NOW()";

		if (isset($data['filter_card_no'])) {
			$sql .= " AND `card_no` = " . $data['filter_card_no'];
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `gift_cards`.`id`";

		$sort_data = array(
			'id',
			'card_no',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `id`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$statement = $this->db->prepare($sql);
		$statement->execute(array(1));

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function total($store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();

		$statement = $this->db->prepare("SELECT * FROM `gift_cards` WHERE `expiry` > NOW()");
		$statement->execute(array($store_id, 1));
		return $statement->rowCount();
	}

	public function totalPrice($from, $to, $store_id = null) 
	{
		$where_query = "1=1";
		if ($from) {
			$where_query .= date_range_giftcard_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`value`) as total FROM `gift_cards` WHERE $where_query");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($row['total']) ? $row['total'] : 0;
	}

	public function totalTopup($from, $to) 
	{
		$where_query = "1=1";
		if ($from) {
			$where_query .= date_range_giftcard_topup_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`amount`) as total FROM `gift_card_topups` WHERE $where_query");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($row['total']) ? $row['total'] : 0;
	}
}