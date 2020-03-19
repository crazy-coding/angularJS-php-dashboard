<?php
include ("../_init.php");

// // return customer due amount
// if($request->server['REQUEST_METHOD'] == 'POST' AND isset($request->post['customer_id'])) {
// 	echo json_encode(array('due_price' => getBalance($request->post['customer_id'])));
// 	exit();
// }

// StockItems
if($request->server['REQUEST_METHOD'] == 'GET' AND $request->get['type'] == 'STOCKITEMS') 
{
	try {
		$store_id = $request->get['store_id'] ? $request->get['store_id'] : store_id();
		$statement = $db->prepare("SELECT `buying_item`.*, `buying_info`.`inv_type` FROM `buying_item` LEFT JOIN `buying_info` ON (`buying_item`.`invoice_id` = `buying_info`.`invoice_id`) WHERE `buying_item`.`store_id` = ? AND `buying_item`.`item_quantity` > `buying_item`.`total_sell` AND `buying_item`.`status` IN ('stock','active') AND `buying_info`.`inv_type` = ?");
	    $statement->execute(array($store_id, 'buy'));
	    $products = $statement->fetchAll(PDO::FETCH_ASSOC);

	    header('Content-Type: application/json');
	    echo json_encode(array('msg' => $language->get('text_success'), 'products' => $products));
	    exit();

	  } catch (Exception $e) { 
	    
	    header('HTTP/1.1 422 Unprocessable Entity');
	    header('Content-Type: application/json; charset=UTF-8');
	    echo json_encode(array('errorMsg' => $e->getMessage()));
	    exit();
	  }
}

// StockItem
if($request->server['REQUEST_METHOD'] == 'GET' AND $request->get['type'] == 'STOCKITEM') 
{
	try {
		$id = $request->get['id'];
		$quantity = $request->get['quantity'];
		$statement = $db->prepare("SELECT * FROM `buying_item` WHERE `id` = ? AND `item_quantity` > `total_sell` AND `status` IN ('stock','active')");
	    $statement->execute(array($id));
	    $products = $statement->fetch(PDO::FETCH_ASSOC);

	    header('Content-Type: application/json');
	    echo json_encode(array('msg' => $language->get('text_success'), 'products' => $products));
	    exit();

	  } catch (Exception $e) { 
	    
	    header('HTTP/1.1 422 Unprocessable Entity');
	    header('Content-Type: application/json; charset=UTF-8');
	    echo json_encode(array('errorMsg' => $e->getMessage()));
	    exit();
	  }
}

// return product list
if($request->server['REQUEST_METHOD'] == 'POST' AND $request->get['type'] == 'BUYINGITEM') 
{
	$sup_id = isset($request->post['sup_id']) ? $request->post['sup_id'] : null;
	$type = $request->post['type'];
	$name = $request->post['name_starts_with'];
	$query = "SELECT `p_id`, `p_name`, `p_code`, `category_id`, `p2s`.`tax_method`, `p2s`.`buy_price`, `p2s`.`sell_price`, `p2s`.`quantity_in_stock` 
		FROM `products` 
		LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`)
		WHERE `p2s`.`store_id` = ? AND `p2s`.`status` = ?";
	if ($sup_id) {
		$query .= " AND `p2s`.`sup_id` = ?";
	}
	$query .= " AND UPPER($type) LIKE '" . strtoupper($name) . "%' ORDER BY `p_id` DESC LIMIT 10";
	$statement = $db->prepare($query);
	if ($sup_id) {
		$statement->execute(array(store_id(), 1, $sup_id));
	} else {
		$statement->execute(array(store_id(), 1));
	}
	$products = $statement->fetchAll(PDO::FETCH_ASSOC);
	$data = array();
    foreach ($products as $product) {
    	$buy_price = $product['buy_price'];
    	$sell_price = $product['sell_price'];
    	$tax_amount = 0;
    	$tax_method = $product['tax_method'] ? $product['tax_method'] : 'exclusive';
    	$taxrate = 0;
    	$product_info = get_the_product($product['p_id']);
    	if ($product_info && $product_info['taxrate']) {
    		$taxrate = $product_info['taxrate']['taxrate'];
    		$tax_amount = ($product_info['taxrate']['taxrate'] / 100 ) * $buy_price;
    	}
		$name = $product['p_id'].'|'.$product['p_name'].'|'.$product['p_code'].'|'.$product['category_id'].'|'.$product['quantity_in_stock'].'|'.$buy_price .'|'.$sell_price.'|'.$tax_amount.'|'.$tax_method.'|'.$taxrate;
		array_push($data, $name);
    }
	echo json_encode($data);
	exit();
}

if($request->server['REQUEST_METHOD'] == 'GET' AND $request->get['type'] == 'STOCKCHECK') {
	if (checkInternetConnection()) {
		if (!DEMO && revalidate_pcode() == 'error') {
			unset($session->data['stock_check']);
			unset($session->data['quantity_check']);
			$file = DIR_INCLUDE.'config/purchase.php';
			$uac = json_encode(array('error', 'error'));
			@chmod($file, FILE_WRITE_MODE);
			$purchase_file = file_get_contents($file);
			write_file($file, $uac);
			echo json_encode(array('error'=>true));
			exit();
		} elseif (revalidate_pcode() == 'ok') {
			global $session;
			$session->data['stock_check'] = true;
			echo json_encode(array('error'=>false));
			exit();
		}
	} else {
		global $session;
		$session->data['stock_check'] = true;
	}
}