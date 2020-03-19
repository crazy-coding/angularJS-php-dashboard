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
class ModelSetting extends Model 
{
	public function get($id = 1)
	{
		$statement = $this->db->prepare("SELECT *  FROM `settings` WHERE `id` = ?");
		$statement->execute(array($id));
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function getSMSSetting($type)
	{
		$statement = $this->db->prepare("SELECT *  FROM `sms_setting` WHERE `type` = ?");
		$statement->execute(array($type));
		return $statement->fetch(PDO::FETCH_ASSOC);
		
	}

	public function isUpdateAvailable()
	{
		$setting = $this->get();
		return $setting['is_update_available'];

	}
}