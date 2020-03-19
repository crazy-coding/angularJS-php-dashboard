<?php
function dd($data)
{
	echo "<pre>".print_r($data,true)."</pre>"; exit;
}

function redirect($url, $status = 302) {
	global $registry;
	if ($registry->get('user') && $registry->get('user')->isLogged() && isset($registry->get('request')->get['redirect_to']) && $registry->get('request')->get['redirect_to']) {
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $registry->get('request')->get['redirect_to']), true, $status);
		exit();
	}
	header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, $status);
	exit();
}

function is_https()
{
	return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? true : false;
}

function get_protocol()
{
	return is_https() ? "https" : "http";
}

function root_url() 
{
    $host  = rtrim($_SERVER['HTTP_HOST'], '/\\');
	$sub_directory = SUBDIRECTORY ? '/' . rtrim(SUBDIRECTORY, '/\\') : null;
	return get_protocol() . '://' . $host . $sub_directory;
}

function url() 
{
    $request_uri = SUBDIRECTORY ? str_replace(SUBDIRECTORY, '', $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'];
    return root_url() . str_replace('//','/',$request_uri);
}

function relative_url() 
{
	return strtok($_SERVER["REQUEST_URI"], '?');
}

function query_string($name)
{
	global $request;
	if (isset($request->get[$name])) {
		return htmlspecialchars($request->get[$name]);
	}	
}

function get_real_ip() {
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function current_nav() 
{
	return basename(relative_url(), ".php");
}

function create_box_state()
{
	global $request;
	$box_state = array(
		'open'
	);
	if (isset($request->get['box_state'] ) 
		&& in_array($request->get['box_state'], $box_state)) {
		return null;
	}
	return ' collapsed-box';
}

function year()
{
	return date('Y');
}

function month() 
{
	return date('m');
}

function day() 
{
	return date('d');
}

function current_time() 
{
	return date('h:i:s');
}

function to_am_pm($time) {
	return date("g:i A", strtotime($time));
}

function date_time()
{
	return date('Y-m-d H:i:s');
}

function format_date($date) 
{
	return date("j M Y g:i A", strtotime($date));
}

function format_only_date($date) 
{
	return date("j M Y", strtotime($date));
}

function randomNumber($length) {
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}

function unique_id($limit = 8) 
{
    return substr(md5(uniqid(mt_rand(), true)), 0, $limit);
}

function random_color_part() 
{
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() 
{
    return random_color_part() . random_color_part() . random_color_part();
}

function get_months($index) 
{
	$array = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	return isset($array[$index]) ? $array[$index] : $index;
}

function get_total_day_in_month()
{
	return cal_days_in_month(CAL_GREGORIAN, month(), year());
}

function limit_char($string, $max = 255)
{
   if(mb_strlen($string, 'utf-8') >= $max){
       $string = mb_substr($string, 0, $max - 5, 'utf-8').'...';
   } 

   return $string;
}

function replace_lines($file, $new_lines, $source_file = null) 
{
    $response = 0;
    $tab = chr(9);
    $lbreak = chr(13) . chr(10);
    if ($source_file) {
        $lines = file($source_file);
    }
    else {
        $lines = file($file);
    }
    foreach ($new_lines as $key => $value) {
        $lines[--$key] = $tab . $value . $lbreak;
    }
    $new_content = implode('', $lines);
    if ($h = fopen($file, 'w')) {
        if (fwrite($h, $new_content)) {
            $response = 1;
        }
        fclose($h);
    }
    return $response;
}

function get_pcode() 
{
	$info = array();

	$file = DIR_INCLUDE.'config/purchase.php';
	@chmod($file, FILE_WRITE_MODE);
	$purchase = file_get_contents($file);
	$purchase = json_decode($purchase);

	if(is_array($purchase)) {
		return trim($purchase[1]);
	}
	return false;
}

function get_pusername() 
{
	$info = array();
	$file = DIR_INCLUDE.'config/purchase.php';
	@chmod($file, FILE_WRITE_MODE);
	$purchase = file_get_contents($file);
	$purchase = json_decode($purchase);

	if(is_array($purchase)) {
		return trim($purchase[0]);
	}
	return false;
}

function revalidate_pcode() 
{
	if (!get_pcode() || !get_pusername() || get_pcode() == 'error' || get_pusername() == 'error') {
		return false;
	}
	$info = array(
		'purchase_code' => get_pcode(),
		'username' => get_pusername(),
		'action' => 'revalidate',
	);
    $apiCall = apiCall($info);
	return $apiCall->status;
}

function check_pcode() 
{
	if (!get_pcode() || !get_pusername() || get_pcode() == 'error' || get_pusername() == 'error') {
		return false;
	}
	$info = array(
		'purchase_code' => get_pcode(),
		'username' => get_pusername(),
	);
    $apiCall = apiCall($info);
	return $apiCall->status;
}

function from()
{
	global $request;
	$from = null;
	if (isset($request->get['from']) && $request->get['from'] && ($request->get['from'] != 'null')) {
	  $from = $request->get['from'];
	}
	return $from;
}

function to()
{
	global $request;
	$to = null;
	if (isset($request->get['to']) && isset($request->get['from']) && ($request->get['to'] != 'null') && ($request->get['from'] != 'null')) {
	  $to = $request->get['to'];
	} elseif(isset($request->get['from']) && ($request->get['from'] != 'null')) {
		$to = date('Y-m-d 23:59:59', strtotime($request->get['from']));
	}
	return $to;
}

function date_range_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`selling_info`.`created_at`) = $day";
		$where_query .= " AND MONTH(`selling_info`.`created_at`) = $month";
		$where_query .= " AND YEAR(`selling_info`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND selling_info.created_at >= '{$from}' AND selling_info.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_item_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`selling_item`.`created_at`) = $day";
		$where_query .= " AND MONTH(`selling_item`.`created_at`) = $month";
		$where_query .= " AND YEAR(`selling_item`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND selling_item.created_at >= '{$from}' AND selling_item.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_filter2($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`buying_info`.`created_at`) = $day";
		$where_query .= " AND MONTH(`buying_info`.`created_at`) = $month";
		$where_query .= " AND YEAR(`buying_info`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND buying_info.created_at >= '{$from}' AND buying_info.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_sell_payments_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`payments`.`created_at`) = $day";
		$where_query .= " AND MONTH(`payments`.`created_at`) = $month";
		$where_query .= " AND YEAR(`payments`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND payments.created_at >= '{$from}' AND payments.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_sell_payments_reverse_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
	$where_query = " AND payments.created_at < '{$from}'";
	return $where_query;
}

function date_range_buy_payments_reverse_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
	$where_query = " AND buying_payments.created_at < '{$from}'";
	return $where_query;
}

function date_range_buying_payments_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`buying_payments`.`created_at`) = $day";
		$where_query .= " AND MONTH(`buying_payments`.`created_at`) = $month";
		$where_query .= " AND YEAR(`buying_payments`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND buying_payments.created_at >= '{$from}' AND buying_payments.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_accounting_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`bank_transaction_info`.`created_at`) = $day";
		$where_query .= " AND MONTH(`bank_transaction_info`.`created_at`) = $month";
		$where_query .= " AND YEAR(`bank_transaction_info`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND bank_transaction_info.created_at >= '{$from}' AND bank_transaction_info.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_loan_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`loans`.`created_at`) = $day";
		$where_query .= " AND MONTH(`loans`.`created_at`) = $month";
		$where_query .= " AND YEAR(`loans`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND loans.created_at >= '{$from}' AND loans.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_loan_payment_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`loan_payments`.`created_at`) = $day";
		$where_query .= " AND MONTH(`loan_payments`.`created_at`) = $month";
		$where_query .= " AND YEAR(`loan_payments`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND loan_payments.created_at >= '{$from}' AND loan_payments.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_filter_customer($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`customers`.`created_at`) = $day";
		$where_query .= " AND MONTH(`customers`.`created_at`) = $month";
		$where_query .= " AND YEAR(`customers`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND customers.created_at >= '{$from}' AND customers.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_buy_transaction_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`supplier_transactions`.`created_at`) = $day";
		$where_query .= " AND MONTH(`supplier_transactions`.`created_at`) = $month";
		$where_query .= " AND YEAR(`supplier_transactions`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND supplier_transactions.created_at >= '{$from}' AND supplier_transactions.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_sell_transaction_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`customer_transactions`.`created_at`) = $day";
		$where_query .= " AND MONTH(`customer_transactions`.`created_at`) = $month";
		$where_query .= " AND YEAR(`customer_transactions`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND customer_transactions.created_at >= '{$from}' AND customer_transactions.created_at <= '{$to}'";
	}
	return $where_query;
}

function date_range_giftcard_topup_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`gift_card_topups`.`date`) = $day";
		$where_query .= " AND MONTH(`gift_card_topups`.`date`) = $month";
		$where_query .= " AND YEAR(`gift_card_topups`.`date`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND gift_card_topups.date >= '{$from}' AND gift_card_topups.date <= '{$to}'";
	}
	return $where_query;
}

function date_range_giftcard_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`gift_cards`.`date`) = $day";
		$where_query .= " AND MONTH(`gift_cards`.`date`) = $month";
		$where_query .= " AND YEAR(`gift_cards`.`date`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND gift_cards.date >= '{$from}' AND gift_cards.date <= '{$to}'";
	}
	return $where_query;
}

function date_range_expense_filter($from, $to)
{
	$from = $from ? $from : date('Y-m-d');
	$to = $to ? $to : date('Y-m-d');
	$where_query = '';
	if (($from && ($to == false)) || ($from == $to)) {
		$day = date('d', strtotime($from));
		$month = date('m', strtotime($from));
		$year = date('Y', strtotime($from));
		$where_query .= " AND DAY(`expenses`.`created_at`) = $day";
		$where_query .= " AND MONTH(`expenses`.`created_at`) = $month";
		$where_query .= " AND YEAR(`expenses`.`created_at`) = $year";
	} else {
		$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
		$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
		$where_query .= " AND expenses.created_at >= '{$from}' AND expenses.created_at <= '{$to}'";
	}
	return $where_query;
}

function barcode_generator()
{
	require_once(DIR_INCLUDE.'vendor/barcode-reader/src/BarcodeGenerator.php');
	require_once(DIR_INCLUDE.'vendor/barcode-reader/src/BarcodeGeneratorPNG.php');
	require_once(DIR_INCLUDE.'vendor/barcode-reader/src/BarcodeGeneratorSVG.php');
	require_once(DIR_INCLUDE.'vendor/barcode-reader/src/BarcodeGeneratorJPG.php');
	require_once(DIR_INCLUDE.'vendor/barcode-reader/src/BarcodeGeneratorHTML.php');

	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	return $generator;
}

function barcode_symbology($generator, $type = 'code_39')
{
	switch ($type) {
		case 'code_39':
			$symbology = $generator::TYPE_CODE_39;
			break;
		case 'code_93':
			$symbology = $generator::TYPE_CODE_93;
			break;
		case 'code_128':
			$symbology = $generator::TYPE_CODE_128;
			break;
		case 'ean_2':
			$symbology = $generator::TYPE_EAN_2;
			break;
		case 'ean_5':
			$symbology = $generator::TYPE_EAN_5;
			break;
		default:
			$symbology = $generator::TYPE_CODE_39;
			break;
	}
	return $symbology;
}

function pdo_start()
{
	global $sql_details;
	$host = $sql_details['host'];
	$db = $sql_details['db'];
	$user = $sql_details['user'];
	$pass = $sql_details['pass'];
	try {
		$db = new PDO("mysql:host={$host};dbname={$db};charset=utf8",$user,$pass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		die('Database Connection Error: '.$e->getMessage());
	}
	return $db;
}

function tableExists($pdo, $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
    } catch (Exception $e) {
        return false;
    }
    return $result !== false;
}

function play_sound($name, $path = null) {
	$path = $path ? $path : root_url() . '/assets/itsolution24/mp3/' . $name;
	?>
	<audio style="display:none;" controls autoplay>
	  <source src="<?php echo $path;?>" type="audio/ogg">
	  <source src="<?php echo $path;?>" type="audio/mpeg">
	  <source src="<?php echo $path;?>" type="audio/mp3">
	</audio>
	<?php
}

function upper($state) {
    return str_replace('_', ' ', ucwords($state));
}

if (!function_exists('health_checkup'))
{
	function health_checkup($store_id = null)
	{		
		return true;
	}
}

function updateImageValue(&$image, $key) {
  if($key == 'p_image') {
    if (FILEMANAGERPATH && is_file(FILEMANAGERPATH.$image) && file_exists(FILEMANAGERPATH.$image))  {
    	$image = FILEMANAGERURL.$image;
    } elseif (is_file(DIR_STORAGE . 'products/' . $image) && file_exists(DIR_STORAGE . 'products/' . $image)) {
    	$image = root_url().'/storage/products'.$image;
    } else {
    	$image = root_url().'/assets/itsolution24/img/noproduct.png';
    }
  }
}

function updateNameValue(&$data, $key) {
  if($key == 'p_name') {
    $data = htmlspecialchars_decode($data);
  }
}

function get_progress_percentage($total, $substract)
{
	return 100 - (($substract / $total)*100);
}