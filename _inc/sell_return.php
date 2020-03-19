<?php 
ob_start();
session_start();
include '../_init.php';

// Check, if user logged in or not
// if user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

//  Load Language File
$language->load('invoice');

$store_id = store_id();
$user_id = user_id();

// LOAD INVOICE MODEL
$invoice_model = $registry->get('loader')->model('invoice');

// return product
if ($request->server['REQUEST_METHOD'] == 'POST' && $request->get['action_type'] == 'RETURN')
{
  try {

    // Check product return permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'sell_return')) {
      throw new Exception($language->get('error_return_permission'));
    }

    // Validate invoice id
    if(empty($request->post['invoice-id'])) {
      throw new Exception($language->get('error_invoice_id'));
    }
    $invoice_id = $request->post['invoice-id']; 
    $customer_id = $request->post['customer-id']; 

    // Check, if invoice exist or not
    $statement = $db->prepare("SELECT `selling_info`.*, `selling_price`.`subtotal`, `selling_price`.`order_tax`, `selling_price`.`payable_amount`, `selling_price`.`paid_amount`, `selling_price`.`due`, `selling_price`.`cgst`, `selling_price`.`sgst`, `selling_price`.`igst` FROM `selling_info` LEFT JOIN `selling_price` ON (`selling_info`.`invoice_id` = `selling_price`.`invoice_id`) WHERE `selling_info`.`invoice_id` = ?");
    $statement->execute(array($invoice_id));
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$invoice) {
      throw new Exception($language->get('error_invoice_not_found'));
    }

    $selling_date_time = strtotime($invoice['created_at']);
    if (invoice_edit_lifespan() > $selling_date_time) {
      throw new Exception($language->get('error_edit_duration_expired'));
    }

    $reference_no = generate_return_reference_no();
    $note = $request->post['note'];
    $items = isset($request->post['items']) && !empty($request->post['items']) ? $request->post['items'] : array();
    
    $checked_item = 0;
    
    // Validate quantity
    foreach ($items as $item) {

      if (!isset($item['check']) OR !$item['check']) {
        continue;
      } else {
        $checked_item++;
      }

      $item_info = $invoice_model->getInvoiceItemInfo($invoice_id, $item['item_id']);
      $quantity = $item['item_quantity'];
      if ($quantity > $item_info['item_quantity']) {
        throw new Exception($language->get('error_quantity_exceed'));
      }
      if(!validateInteger($item['item_quantity'])) {
        throw new Exception($language->get('error_quantity_exceed'));
      }
      if ($item['item_quantity'] <= 0) {
        throw new Exception($language->get('error_quantity_exceed'));
      }
    }

    if ($checked_item <= 0) {
      throw new Exception($language->get('error_select_at_least_one_item'));
    }

    $return_amount = 0;
    $total_item = count($items);
    $total_quantity = 0;
    $total_amount = 0;
    $tpayable = 0;
    $tsubtotal = 0;
    $titem_tax = 0;
    foreach ($items as $item) {

      if (!isset($item['check']) OR !$item['check']) {
        continue;
      }

      $item_id = $item['item_id'];
      $quantity = $item['item_quantity'];
      $total_quantity += $quantity;

      // SELLING INVOICE ADJUSTMENT

        $statement = $db->prepare("SELECT * FROM `selling_item` WHERE `invoice_id` = ? AND `item_id` = ?");
        $statement->execute(array($invoice_id, $item_id));
        $invoice_item = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$invoice_item) {
          throw new Exception($language->get('error_invoice_item_not_found'));
        }

        $statement = $db->prepare("SELECT * FROM `products`
          LEFT JOIN `product_to_store` p2s ON (`products`.`p_id` = `p2s`.`product_id`)
          WHERE `p2s`.`store_id` = ? AND `p_id` = ?");
        $statement->execute(array($store_id, $item_id));
        $product = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
          throw new Exception($language->get('error_invoice_item_not_found'));
        }

      // ADJUST BUYING INVOICE ITEM
        $buying_invoice_id = $invoice_item['buying_invoice_id'];
        $quantity_exist = $quantity;
        $return_quantity = $quantity;
        $substract_buying_price = 0;
        $buying_item = null;
        $inc = 0;
        while ($quantity_exist > 0) 
        {
          if ($inc > 0) {
            $statement = $db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `item_id` = ? AND `status` = ?");
            $statement->execute(array($store_id, $item_id, 'sold'));
            $buying_item = $statement->fetch(PDO::FETCH_ASSOC);
            $buying_invoice_id = $buying_item['invoice_id'];
          } else {
            $statement = $db->prepare("SELECT * FROM `buying_item` WHERE `store_id` = ? AND `invoice_id` = ? AND `item_id` = ?");
            $statement->execute(array($store_id, $buying_invoice_id, $item_id));
            $buying_item = $statement->fetch(PDO::FETCH_ASSOC);
          }
          if ($buying_item) {
            $statement = $db->prepare("UPDATE `buying_item` SET `status` = ? WHERE `store_id` = ? AND `item_id` = ? AND `status` = ?");
            $statement->execute(array('stock', $store_id, $item_id, 'active'));

            $sold = $buying_item['total_sell'];
            if ($sold < $quantity_exist) {
              $return_quantity = $sold;
              $quantity_exist = $quantity_exist - $sold;
            } else {
              $return_quantity = $quantity_exist;
              $quantity_exist = 0;
            }
            $statement = $db->prepare("UPDATE `buying_item` SET `total_sell` = `total_sell`-$return_quantity, `status` = ? WHERE `store_id` = ? AND `invoice_id` = ? AND `item_id` = ?");
            $statement->execute(array('active', $store_id, $buying_invoice_id, $item_id));
            $substract_buying_price = $substract_buying_price + ($buying_item['item_buying_price'] * $return_quantity);
          }
          if ($inc > 100) {
            throw new Exception($language->get('an_unexpected_error_occured'));
            $quantity_exist = 0;
            break;
          }
          $inc++;
        }

        // Return Product
        $item_quantity = $invoice_item['item_quantity'] - $quantity;
        $per_item_tax = $invoice_item['item_tax'] / $invoice_item['item_quantity'];
        $item_tax = $invoice_item['item_tax'] - ($per_item_tax * $quantity);
        $tax_method = $product['tax_method'];
        $item_total_substract = ($invoice_item['item_price'] * $quantity) + $item_tax;
        $total_amount += $item_total_substract;
        if ($tax_method == 'exclusive') {
          $item_total = ($invoice_item['item_price'] * $item_quantity) + $item_tax;
        } else {
          $item_total = ($invoice_item['item_price'] * $item_quantity);
        }
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        if ($invoice_item['cgst'] > 0) {
          $cgst = $item_tax / 2;
        }
        if ($invoice_item['sgst'] > 0) {
          $sgst = $item_tax / 2;
        }
        if ($invoice_item['igst'] > 0) {
          $igst = $item_tax;
        }

        $statement = $db->prepare("UPDATE `selling_item` SET `item_quantity` = ?, `item_tax` = ?, `total_buying_price` = `total_buying_price`-$substract_buying_price, `item_total` = ?, `cgst` = ?, `sgst` = ?, `igst` = ? WHERE `id` = ?");
        $statement->execute(array($item_quantity, $item_tax, $item_total, $cgst, $sgst, $igst, $invoice_item['id']));

        if ($tax_method == 'exclusive') {
          $subtotal = ($invoice_item['item_price'] * $quantity) + $item_tax;
          $payable_amount = ($invoice_item['item_price'] * $quantity) + $item_tax;
          $tpayable += $payable_amount;
          $tsubtotal += $subtotal;
        } else {
          $subtotal = ($invoice_item['item_price'] * $quantity);
          $payable_amount = $invoice_item['item_price'] * $quantity;
          $tpayable += $payable_amount;
          $tsubtotal += $subtotal;
        }

        $titem_tax = $per_item_tax * $quantity;

        $statement = $db->prepare("UPDATE `product_to_store` SET `quantity_in_stock` = `quantity_in_stock` + $quantity WHERE `store_id` = ? AND `product_id` = ?");
        $statement->execute(array($store_id, $item_id));

        $statement = $db->prepare("INSERT INTO `return_items` SET `store_id` = ?, `invoice_id` = ?, `product_id` = ?, `product_name` = ?, `quantity` = ?, `amount` = ?");
        $statement->execute(array($store_id, $invoice_id, $item_id, $product['p_name'], $quantity, $item_total_substract));
    };

    if ($total_quantity <= 0) {
      throw new Exception($language->get('error_empty_list'));
    }

    $paid_amount = $invoice['paid_amount'];
    $due = $invoice['due'];
    $balance = 0;
    $payment_status = $invoice['payment_status'];
    if ($paid_amount > $tpayable) {
      $paid_amount = $tpayable;
      $due = 0;
      $balance = $tpayable;
      $return_amount = $balance;
    }
    if ($paid_amount == $tpayable) {
      $payment_status = 'paid';
    }
    if ($due > $tpayable) {
      $due = $tpayable;
    }

    $statement = $db->prepare("UPDATE `selling_info` SET `payment_status` = ? WHERE `store_id` = ? AND `invoice_id` = ?");
    $statement->execute(array($payment_status, $store_id, $invoice_id));

    $tcgst = 0;
    $tsgst = 0;
    $tigst = 0;
    if ($invoice['cgst'] > 0) {
      $tcgst = $titem_tax / 2;
    }
    if ($invoice['sgst'] > 0) {
      $tsgst = $titem_tax / 2;
    }
    if ($invoice['igst'] > 0) {
      $tigst = $titem_tax;
    }

    $statement = $db->prepare("UPDATE `selling_price` SET `subtotal` = `subtotal`-$tsubtotal, `item_tax` = `item_tax`-$titem_tax, `payable_amount` = `payable_amount`-$tpayable, `paid_amount` = `paid_amount`-$paid_amount, `due` = `due`-$due, `cgst` = `cgst`-$tcgst, `sgst` = `sgst`-$tsgst, `igst` = `igst`-$tigst  WHERE `store_id` = ? AND `invoice_id` = ?");
    $statement->execute(array($store_id, $invoice_id));

    if ($balance > 0) {
      $statement = $db->prepare("INSERT INTO `payments` SET `type` = ?, `store_id` = ?, `invoice_id` = ?, `note` = ?, `pos_balance` = ?, `created_by` = ?");
      $statement->execute(array('change', $store_id, $invoice_id, 'return_change', $balance, $user_id));
    } else {
      $statement = $db->prepare("DELETE FROM `payments` WHERE `store_id` = ? AND `invoice_id` = ? AND `note` = ?");
      $statement->execute(array($store_id, $invoice_id, 'return_change'));
    }

    $statement = $db->prepare("INSERT INTO `returns` SET `store_id` = ?, `reference_no` = ?, `invoice_id` = ?, `customer_id` = ?, `note` = ?, `total_item` = ?, `total_quantity` = ?, `total_amount` = ?, `created_by` = ?");
    $statement->execute(array($store_id, $reference_no, $invoice_id, $customer_id, $note, $total_item, $total_quantity, $total_amount, $user_id));

    if ($return_amount > 0) {
      $statement = $db->prepare("INSERT INTO `payments` SET `type` = ?, `store_id` = ?, `invoice_id` = ?, `reference_no` = ?, `amount` = ?, `created_by` = ?");
      $statement->execute(array('return', $store_id, $invoice_id, $reference_no, -$balance, $user_id));
    }

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_return_success'), 'id' => $item_id));
    exit();
    
  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}