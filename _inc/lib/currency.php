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
class Currency 
{
	private $code;
	private $currencies = array();

	public function __construct($registry) 
	{
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		
		$statement = $this->db->prepare("SELECT * FROM `currency`");
		$statement->execute();
		$currencies = $statement->fetchAll(PDO::FETCH_ASSOC);

		foreach ($currencies as $result) {
			$this->currencies[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'symbol_left'   => $result['symbol_left'],
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value']
			);
		}

		if (isset($this->request->get['currency']) && (array_key_exists($this->request->get['currency'], $this->currencies))) {
			$this->set($this->request->get['currency']);
		} else {
			$statement = $this->db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
			$statement->execute(array(store_id()));
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			$this->code = $row['currency'];
		}
	}

	public function getDefault() 
	{
		return 'USD';
	}

	public function set($currency, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$this->code = $currency;
		$statement = $this->db->prepare("UPDATE `stores` SET `currency` = ? WHERE `store_id` = ?");
		$statement->execute(array($currency, $store_id));
	}

	public function format($number, $currency = '', $value = '', $format = true) 
	{
		if ($currency && $this->has($currency)) {

			$symbol_left   = $this->currencies[$currency]['symbol_left'];
			$symbol_right  = $this->currencies[$currency]['symbol_right'];
			$decimal_place = $this->currencies[$currency]['decimal_place'];

		} else {

			$symbol_left   = $this->currencies[$this->code]['symbol_left'];
			$symbol_right  = $this->currencies[$this->code]['symbol_right'];
			$decimal_place = $this->currencies[$this->code]['decimal_place'];
			$currency = $this->code;
		}

		if ($value) {
			$value = $value;
		} else {
			$value = $this->currencies[$currency]['value'];
		}

		if ($value) {
			$value = (float)$number * $value;
		} else {
			$value = $number;
		}

		$string = '';

		if (($symbol_left) && ($format)) {
			$string .= $symbol_left;
		}

		if ($format) {
			$decimal_point = '.';
		} else {
			$decimal_point = '.';
		}

		if ($format) {
			$thousand_point = ',';
		} else {
			$thousand_point = '';
		}
		$string .= number_format(round($value, (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);

		if (($symbol_right) && ($format)) {
			$string .= $symbol_right;
		}
		return $string;
	}

	public function convert($value, $from, $to) 
	{
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}		

		return $value * ($to / $from);
	}

	public function getId($currency = '') 
	{
		if (!$currency && isset($this->currencies[$this->code]['currency_id'])) {
			return $this->currencies[$this->code]['currency_id'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['currency_id'];
		} else {
			return 0;
		}
	}

	public function getSymbolLeft($currency = '') 
	{
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_left'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency = '') 
	{
		if (!$currency) {
			return $this->currencies[$this->code]['symbol_right'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency = '') 
	{
		if (!$currency) {
			return $this->currencies[$this->code]['decimal_place'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getCode() 
	{
		return $this->code;
	}

	public function getValue($currency = '') 
	{
		if (!$currency) {
			return $this->currencies[$this->code]['value'];
		} elseif ($currency && isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			
			return 0;
		}
	}

	public function has($currency) 
	{
		return isset($this->currencies[$currency]);
	}
}
