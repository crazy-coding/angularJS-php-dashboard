<?php

namespace SMSGateway\Gateway;

function mergeArray($array1,$array2)
{
    $mergedArray = [];

    foreach ($array1 as $key => $value) 
    {
        if(isset($array2[$key]))
        {
           $mergedArray[$key] = $array2[$key];
        } else {
            $mergedArray[$key] = $array1[$key];
        }
    }
    return $mergedArray;
}

class Onnorokomsms implements GatewayInterface
{
    protected $defaultConfig = array(
        'username'    => '',
        'password'   => '',
        'type'  => 'TEXT',
        'maskname' => '',
        'campaignname' => '',
        'url' => 'https://api2.onnorokomsms.com/HttpSendSms.ashx?',
    );
    
    protected $username;
    protected $password;
    protected $type;
    protected $maskName;
    protected $campaignName;
    protected $url;

    public function __construct ($config = array()) 
    {
        extract(mergeArray($this->defaultConfig, $config));
        $this->username = $username;
        $this->password = $password;
        $this->type = $type;
        $this->maskName = $maskname;
        $this->campaignName = $campaignname;
        $this->url = $url;
    }

    public function send($to, $message) 
    {
        //Your message to send, Add URL encoding here.
        $smsText = $message;
        
        //Prepare you post parameters
        $postData = array(
            'op' => 'OneToOne',
            'type' => $this->type,
            'mobile' => $to,
            'smsText' => $smsText,
            'username' => $this->username,
            'password' => $this->password,
            'maskName' => $this->maskName,
            'campaignName' => $this->campaignName,
        );

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);

        if(curl_errno($ch))
        {
            return 'error:' . curl_error($ch);
        }

        curl_close($ch);
        return $output;
    }

    public function deliveryStatus($response_id)
    {
        //Prepare you post parameters
        $postData = array(
            'op' => 'DeliveryStatus',
            'responseId' => $response_id,
            'username' => $this->username,
            'password' => $this->password,
            'maskName' => $this->maskName,
            'campaignName' => $this->campaignName,
        );

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
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
        return $output;
    }
}