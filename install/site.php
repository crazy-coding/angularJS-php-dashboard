<?php 
ob_start();
session_start();
include ("_init.php");

if (defined('INSTALLED')) {
	header('Location: ../index.php');
}



include("header.php"); 

$errors = array();
$success = array();
$info = array();

$errors['config'] = null;
$errors['store_name'] = null;
$errors['phone'] = null;
$errors['email'] = null;
$errors['password'] = null;
$errors['address'] = null;

if(!checkDBConnection()) {
	redirect("database.php");
}

if ($request->server['REQUEST_METHOD'] == 'POST') {

	$dbhost = $sql_details['host'];
	$dbname = $sql_details['db'];
	$dbuser = $sql_details['user'];
	$dbpass = $sql_details['pass'];

	// Connect to Database
	try {
		$db = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8",$dbuser,$dbpass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		redirect('database.php');
	}

	// Validate Post Data
	if (!validateString($request->post['store_name'])) {
		$errors['store_name'] = 'Store name field is required.';
	} elseif (empty($request->post['phone'])) {
		$errors['phone'] = 'Phone number field is required.';
	} elseif (!validateEmail($request->post['email'])) {
		$errors['email'] = 'Email field is required.';
	} elseif (!validateAlphanumeric($request->post['password'])) {
		$errors['password'] = 'Password field is required.';
	} else {

		$info['username'] = get_pusername();
		$info['purchase_code'] = get_pcode();
		$info['domain'] = root_url();
		$info['ip'] = get_real_ip();
		$info['mac'] = getMAC();
		$info['email'] = $request->post['email'];
		$info['phone'] = $request->post['phone'];
		$info['country'] = '';
		$info['zip_code'] = '';
		$info['address'] = $request->post['address'];
		$info['action'] = 'installinfo';
	    $apiCall = apiCall($info);

		$info = array(
			'name' => $request->post['store_name'], 
			'email' => $request->post['email'], 
			'mobile' => $request->post['phone'],
			'country' => 'US',
			'zip_code' => '1200',
			'status' => 1,
			'cashier_id' => 2,
			'address' => $request->post['address'],
		);

		$preference = array(
			'timezone' => $session->data['timezone'],
			'invoice_edit_lifespan' => 1440,
			'invoice_edit_lifespan_unit' => 'minute',
			'invoice_delete_lifespan' => 1440,
			'invoice_delete_lifespan_unit' => 'minute',
			'tax' => 0,
			'stock_alert_quantity' => 10,
			'datatable_item_limit' => 25,
			'after_sell_page' => 'pos',
			'invoice_footer_text' => 'Thank you for choosing us!',
			'email_from' => $request->post['store_name'],
			'email_address' => 'US',
			'email_driver' => 'smtp_server',
			'smtp_host' => 'smtp.google.com',
			'smtp_username' => '',
			'smtp_password' => '',
			'smtp_port' => 465,
			'ssl_tls' => 'ssl',
		);

		$store_id = 1;
    	// insert store info
		$statement = $db->prepare("UPDATE `stores` SET `name` = ?, `mobile` = ?, `country` = ?, `zip_code` = ?, `cashier_id` = ?, `address` = ?, `preference` = ? WHERE `store_id` = ?");
    	$statement->execute(array($info['name'], $info['mobile'], $info['country'], $info['zip_code'], $info['cashier_id'], $info['address'], serialize($preference), $store_id));
    	

		// insert user
		$info = array(
			'username' => 'Your Name',
			'email' => $request->post['email'],
			'mobile' => $request->post['phone'],
			'password' => $request->post['password'],
			'ip' => get_real_ip(),
			'status' => 1,
			'created_at' => date_time(), 
		);

		$cashier_email = 'cashier@'.substr(strrchr($request->post['email'], "@"), 1);
		$salesman_email = 'salesman@'.substr(strrchr($request->post['email'], "@"), 1);

		// Update admin info
    	$statement = $db->prepare("UPDATE `users` SET username = ?, email = ?, mobile = ?, password = ?, raw_password = ?, ip = ?, created_at = ? WHERE `id` = ?");
    	$statement->execute(array($info['username'], $info['email'], $info['mobile'], md5($info['password']), $info['password'], $info['ip'], $info['created_at'], 1));

    	// Update cashier info
    	$statement = $db->prepare("UPDATE `users` SET email = ?, password = ?, raw_password = ?, ip = ?, created_at = ? WHERE `id` = ?");
    	$statement->execute(array($cashier_email, md5($info['password']), $info['password'], $info['ip'], $info['created_at'], 2));

    	// Update salesman info
    	$statement = $db->prepare("UPDATE `users` SET email = ?, password = ?, raw_password = ?, ip = ?, created_at = ? WHERE `id` = ?");
    	$statement->execute(array($salesman_email, md5($info['password']), $info['password'], $info['ip'], $info['created_at'], 3));

		if(!$errors['site'] || !$errors['index_validation']) {

			// define INSTALLED constant
			$config_path = ROOT . '/config.php';

			@chmod($config_path, 0777);
			if (is_writable($config_path) === false) {

				$errors['config'] = 'Config file is unwritable';
				return false;

			} else {

				$file = $config_path;

				$line_host 	= "define('INSTALLED', true);";

				$fileArray = array(3 => $line_host);

				replace_lines($file, $fileArray);
				@chmod($config_path, 0644);

				$session->data['admin_username'] = $request->post['email'];
				$session->data['cashier_username'] = $cashier_email;
				$session->data['salesman_username'] = $salesman_email;
				$session->data['password'] = $request->post['password'];

				redirect('done.php');
			}
			
		}
	}
}
?>

<?php include '../_inc/template/install/site.php'; ?>

<?php include("footer.php"); ?>
