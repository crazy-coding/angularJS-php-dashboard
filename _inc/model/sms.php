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
class ModelSms extends Model 
{
	public function addSchedule($data) 
	{		
    	$statement = $this->db->prepare("INSERT INTO `sms_schedule` (schedule_datetime, store_id, people_type, mobile_number, people_name, sms_text, campaign_name, process_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    	$statement->execute(array($data['schedule_datetime'], $data['store_id'], $data['people_type'], $data['mobile_number'], $data['people_name'], $data['sms_text'], $data['campaign_name'], $data['process_status'], $data['created_at']));
    	$schedule_id = $this->db->lastInsertId();
    	return $schedule_id; 
	}

	public function updateSchedule($id, $data) 
	{		
    	$statement = $this->db->prepare("UPDATE `sms_schedule` SET `mobile_number` = ?, `sms_text` = ?, `process_status` = ?, `response_text` = ?, `delivery_status` = ? WHERE `id` = ?");
		$statement->execute(array($data['mobile_number'], $data['sms_text'], 0, NULL, 'pending', $id));
	}

	public function updateStatus($id, $response_text)
	{
		$statement = $this->db->prepare("UPDATE `sms_schedule` SET `process_status` = ?, `response_text` = ? WHERE `id` = ?");
		$statement->execute(array(1, $response_text, $id));
	}

	public function updateDeliveryStatus($id, $delivery_status)
	{
		$statement = $this->db->prepare("UPDATE `sms_schedule` SET `delivery_status` = ? WHERE `id` = ?");
		$statement->execute(array($delivery_status, $id));
	}

	public function getScheduleSmsRow($id, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$statement = $this->db->prepare("SELECT * FROM `sms_schedule`
	    	WHERE `store_id` = ? AND `id` = ?");
	  	$statement->execute(array($store_id, $id));
	  	return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getScheduleSms($data = array(), $store_id = null)
	{
		$store_id = $store_id ? $store_id : store_id();

		$sql = "SELECT * FROM `sms_schedule` WHERE `store_id` = ?";

		if (isset($data['filter_people_type'])) {
			$sql .= " AND `people_type` = '" . $data['filter_people_type'] . "'";
		}

		if (isset($data['filter_mobile_number'])) {
			$sql .= " AND `mobile_number` = '" . $data['filter_mobile_number'] . "'";
		}

		if (isset($data['filter_people_name'])) {
			$sql .= " AND `people_name` = '" . $data['filter_people_name'] . "'";
		}

		if (isset($data['filter_sms_type'])) {
			$sql .= " AND `sms_type` = '" . $data['filter_sms_type'] . "'";
		}

		if (isset($data['filter_campaign_name'])) {
			$sql .= " AND `campaign_name` = '" . $data['filter_campaign_name'] . "'";
		}

		if (isset($data['filter_process_status'])) {
			$sql .= " AND `process_status` = '" . $data['filter_process_status'] . "'";
		} else {
			$sql .= " AND `process_status` = 0";
		}

		if (isset($data['filter_delivery_status'])) {
			$sql .= " AND `delivery_status` = '" . $data['filter_delivery_status'] . "'";
		} else {
			$sql .= " AND `delivery_status` = 'pending'";
		}

		if (isset($data['exclude'])) {
			$sql .= " AND `id` != " . $data['exclude'];
		}

		$sql .= " GROUP BY `id`";

		$sort_data = array(
			'id',
			'schedule_datetime',
			'created_at',
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
		$statement->execute(array($store_id));

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}