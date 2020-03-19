<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	MODERN POS
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
class Store 
{
	private $registry;
	private $request;
	private $db;
	private $session;
	private $data;

	public function __construct($registry) 
	{
		$this->registry = $registry;

		$this->request = $this->registry->get('request');

		$this->db = $registry->get('db');

		$this->session = $registry->get('session');

		if (!isset($this->session->data['store_id'])) {
			$this->session->data['store_id'] = 1;
		}

		if (isset($this->session->data['store_id'])) {

			$store_id = $this->session->data['store_id'];

			$statement = $this->db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
			$statement->execute(array($store_id));
			$this->data = $statement->fetch(PDO::FETCH_ASSOC);

			if (isset($this->data['store_id'])) {
				$this->session->data['store_id'] = $this->data['store_id'];
			}
		}
	}

	public function openTheStore($store_id = 1) 
	{
		$store_id = $store_id ? (int)$store_id : 1;

		$statement = $this->db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
		$statement->execute(array($store_id));
		$store = $statement->fetch(PDO::FETCH_ASSOC);

		if (isset($store['store_id'])) {
			unset($this->session->data['store_id']);
			$this->session->data['store_id'] = $store['store_id'];
		}
	}

	public function setStore($store_id)
	{
		$store_id = $store_id ? (int)$store_id : 1;

		$statement = $this->db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
		$statement->execute(array($store_id));
		$this->data = $statement->fetch(PDO::FETCH_ASSOC);
	}

	public function getAll()
	{
		return $this->data;
	}

	public function get($key) 
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function isMultiStore()
	{
		$statement = $this->db->prepare("SELECT * FROM `stores`");
		$statement->execute();

		return $statement->rowCount();
	}

	public function getSql() 
	{
		$statement = $this->db->prepare("SHOW TABLES");
		$statement->execute();
		$tables = $statement->fetchAll(PDO::FETCH_NUM);

		$output = '';

		foreach ($tables as $table) {

		  $table = $table[0];

		  $output .= 'TRUNCATE TABLE `' . $table . '`;' . "\n\n";

		  $statement = $this->db->prepare("SELECT * FROM `" . $table . "`");
		  $statement->execute();
		  $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

		  foreach ($rows as $result) {
		    $fields = '';

		    foreach (array_keys($result) as $value) {
		      $fields .= '`' . $value . '`, ';
		    }

		    $values = '';

		    foreach (array_values($result) as $value) {
		      $value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
		      $value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
		      $value = str_replace('\\', '\\\\',  $value);
		      $value = str_replace('\'', '\\\'',  $value);
		      $value = str_replace('\\\n', '\n',  $value);
		      $value = str_replace('\\\r', '\r',  $value);
		      $value = str_replace('\\\t', '\t',  $value);

		      $values .= '\'' . $value . '\', ';
		    }

		    $output .= 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
		  }

		  $output .= "\n\n";
		} 

		return $output;
	}
}