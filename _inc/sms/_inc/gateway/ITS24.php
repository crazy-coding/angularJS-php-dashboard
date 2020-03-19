<?php

namespace SMSGateway\Gateway;

class Its24 implements GatewayInterface
{
    protected $defaultConfig = array(
        'sender_id'    => '',
        'api_id'   => '',
        'url'       => 'https://sms.one9.one/sms/api',
    );
	
	protected $sender_id;
    protected $api_id;
    protected $url;

	public function __construct ($config = array()) 
	{
        extract(array_merge($this->defaultConfig, $config));

        $this->sender_id     = $sender_id;
		$this->api_id    = $api_id;
        $this->url        = $url;
	}

    public function send($to, $message) 
    {
    	
        //Your message to send, Add URL encoding here.
        $message = $message;
		
        //Prepare you post parameters
        $postData = array(
        	'action' => 'send-sms',
            'api_key' => $this->api_id,
            'to' => $to,
        	'from' => $this->sender_id,
            'sms' => $message,
        );

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
            //,CURLOPT_FOLLOWLOCATION => true
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);

        if(curl_errno($ch))
        {
            echo 'error:' . curl_error($ch);
        }

        curl_close($ch);

        if ($output) {
            return true;
        } else {
            return false;
        }
	}
}