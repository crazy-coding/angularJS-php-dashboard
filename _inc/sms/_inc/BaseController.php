<?php

namespace SMSGateway;

class BaseController
{
	protected $gateways = array();

	public $activeGateway = null;

	public function __construct($activeGateway = null)
	{
		global $config;
		foreach ($config['gateways'] as $key => $gateway) {
			$this->gateways[$key] = $gateway;
		}
		$this->activeGateway = $activeGateway;
	}

	public function getways() 
	{
		return $this->gateways;
	}

	protected function _isGatewayExist() 
	{
		return isset($this->gateways[$this->activeGateway]);
	}

	protected function _verifyGateway()
	{
		if (!$this->_isGatewayExist()) {
			throw new \Exception('gateway not found!');
		}
	}

	public function initGateway($activeGateway = null, $config = array()) 
	{
		if (is_array($activeGateway)) {
			$config = $activeGateway;
		} else if ($activeGateway) {
			$this->activeGateway = $activeGateway;
		}
		$this->_verifyGateway();
		return new $this->gateways[$this->activeGateway]($config);
	}
}

class SMSGateway extends BaseController {
	public function __construct ($gateway = null) {
		parent::__construct($gateway);
	}

	public function send($to, $message, $getway = null) {

		if ($gateway) { $this->activeGateway = $gateway; }
		
		$result = array();

		if($this->initGateway()->send($to, $message) == true)  {
			$result['check'] = true;
			return $result;
		} else {
			$result['check'] = false;
			$result['message'] = "Check your " . $this->activeGateway . " account settings";
			return $result;
		}

		return $result;
	}
}