<?php

namespace SMSGateway\Gateway;

interface GatewayInterface 
{
  public function send($to, $message);
}