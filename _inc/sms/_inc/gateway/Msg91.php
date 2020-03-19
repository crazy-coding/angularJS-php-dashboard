<?php

namespace SMSGateway\Gateway;

class Msg91 implements GatewayInterface
{

    protected $authKey;
    protected $senderID;

	public function __construct () {
		$this->ci =& get_instance();
        $this->ci->load->model('smssettings_m');
        
        $msg91_bind = array();
        $get_msg91s = $this->ci->smssettings_m->get_order_by_msg91();
        foreach ($get_msg91s as $key => $get_msg91) {
            $msg91_bind[$get_msg91->field_names] = $get_msg91->field_values;
        }
        $this->authKey = $msg91_bind['msg91_authKey'];
        $this->senderID = $msg91_bind['msg91_senderID'];
        $this->url =  'https://msg91sms.vsms.net/eapi/submission/send_sms/2/2.0';
	}

    public function send($to, $message) 
    {
        //Your message to send, Add URL encoding here.
        $message = urlencode($message);

        //Define route
        $route = 4;
        //Prepare you post parameters
        $postData = array(
            'authkey' => $this->authKey,
            'mobiles' => $to,
            'message' => $message,
            'sender' => $this->senderID,
            'route' => $route
        );

        //API URL
        $url="http://api.msg91.com/api/sendhttp.php";

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
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

        //Print error if any
        if(curl_errno($ch))
        {
            echo 'error:' . curl_error($ch);
        }

        curl_close($ch);

        if ($output) {
            return TRUE;
        } else {
            return FALSE;
        }
	}

}