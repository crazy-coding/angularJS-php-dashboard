<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_summary_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('summary_report');

$store_id = store_id();
$user_id = user_id();

// view expense
if (isset($request->get['action_type']) && $request->get['action_type'] == 'VIEW') 
{
  $from = date('Y-m-d');
  $to = date('Y-m-d');

  $report = array();
  $duration = $request->get['duration'];
  switch ($duration) {
    case 'today':
      break;

    case 'this_week':
      $from = date("Y-m-d", strtotime("-1 week"));
      break;

    case 'this_month':
      $from = date("Y-m-d", strtotime("-1 month"));
      break;

    case 'this_year':
      $from = date("Y-m-d", strtotime("-1 year"));
      break;
    
    default:
      # code...
      break;
  }

  $tax_collection = get_tax('order_tax',$from, $to) + get_in_or_exclusive_tax('exclusive',$from, $to);
  $invoice_amount = selling_price($from, $to) - $tax_collection;
  // $due_collection = due_collection_amount($from, $to);
  $prev_due_collection = anotherday_due_collection_amount($from, $to);
  $loan_taken = get_total_loan($from, $to);
  $gift_card_price = get_giftcard_total_price($from, $to);
  $gift_card_topup = get_giftcard_total_topup($from, $to);
  $total_income = $invoice_amount+$tax_collection+$loan_taken+$gift_card_price+$gift_card_topup+$prev_due_collection;

  $purchase_tax = get_in_or_exclusive_buy_tax('exclusive',$from, $to) + get_buy_tax('order_tax',$from, $to);
  $product_purchase = buying_price($from, $to)-$purchase_tax;
  $sell_tax = get_tax('order_tax',$from, $to) + get_in_or_exclusive_tax('exclusive',$from, $to);
  // $due_paid = buying_due_paid_amount($from, $to);
  $prev_due_paid = anotherday_due_paid_amount($from, $to);
  $loan_paid = get_total_loan_paid($from, $to);
  $other_expense = get_total_expense($from, $to);
  $total_expense = $product_purchase+$purchase_tax+$sell_tax+$loan_paid+$other_expense+$prev_due_paid;

  $profit_from_product = '0.00';
  $income_minus_expense = $total_income-$total_expense;

  $report = array(
      'invoice_amount' => $invoice_amount, 
      'tax_collection' => $tax_collection,
      'prev_due_collection' => $prev_due_collection,
      'loan_taken' => $loan_taken,
      'gift_card_sell' => $gift_card_price,
      'gift_card_topup' => $gift_card_topup,
      'total_income' => $total_income,

      'product_purchase' => $product_purchase,
      'purchase_tax' => $purchase_tax,
      'sell_tax' => $sell_tax,
      'prev_due_paid' => $prev_due_paid,
      'loan_paid' => $loan_paid,
      'other_expense' => $other_expense,
      'total_expense' => $total_expense,

      'profit_from_product' => $profit_from_product,
      'income_minus_expense' => $income_minus_expense,
    );

  include 'template/summary_report.php';
  exit();
}