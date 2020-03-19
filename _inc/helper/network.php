<?php
// function checkInternetConnection($domain = 'www.google.com')  
function checkInternetConnection($domain = 'www.itsolution24.com')  
{
	if($socket =@ fsockopen($domain, 80, $errno, $errstr, 30)) {
		fclose($socket);
		return true;
	}
	return false;
}

function checkOnline($domain) 
{
	return checkInternetConnection($domain);
}

function checkDBConnection() 
{
	global $sql_details;
	$host = $sql_details['host'];
	$db = $sql_details['db'];
	$user = $sql_details['user'];
	$pass = $sql_details['pass'];
	try {
		$conn = new PDO("mysql:host={$host};dbname={$db};charset=utf8",$user,$pass);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conn;
	}
	catch(PDOException $e) {
		return false;
		// die('Database Connection Error: '.$e->getMessage());
	}
}

function isLocalhost() {
    $whitelist = array('localhost','127.0.0.1','::1');
    return in_array( $_SERVER['REMOTE_ADDR'], $whitelist);
}

function apiCall($data, $url = NULL) 
{
	if(is_null($url)) {
        $url = activeServer();
    }

	if(!$url) {
		return (object) array(
			'status' => FALSE,
			'message' => 'Server Down',
			'for' => 'Invalid Server',
		);
	}

	$data['site'] = root_url();
	$data_string = json_encode($data);
    $ch = curl_init($url);
    // dd($data_string);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)]
    );
    $result = curl_exec($ch);
    return json_decode($result);
}

function activeServer() 
{
	$allDomain = [ 
		get_protocol().'://tracker.itsolution24.com/pos20',
		get_protocol().'://najmul.net/pos-tracker',
	];

	if(count($allDomain)) {
		foreach ($allDomain as $domain) {
			$url = parse_url($domain);
			if(checkOnline($url['host'])) {
				return $domain.'/check.php';
			}
		}
	}

	return false;
}

function getMAC()
{
	ob_start(); // Turn on output buffering
	system('ipconfig /all'); //Execute external program to display output
	$mycom=ob_get_contents(); // Capture the output into a variable
	ob_clean(); // Clean (erase) the output buffer
	$mac = array();
	foreach(preg_split("/(\r?\n)/", $mycom) as $line) {
		if(strstr($line, 'Physical Address')) {
			$mac[]= substr($line,39,18);
		}
	}
	return $mac;
}