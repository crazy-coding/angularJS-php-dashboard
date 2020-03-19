<style type="text/css">
  .modal-lg {
    max-width: 80%;
  }
</style>
<div class="row">
	<div class="col-md-12 text-center">
		<div class="btn-group btn-justify">
			<button ng-click="loadSummary('today', 'Today');" onClick="return false;" id="btn_today" class="btn btn-info"><?php echo $language->get('label_today'); ?></button>
			<button ng-click="loadSummary('this_week', 'This Week');" onClick="return false;" id="btn_this_week" class="btn btn-warning"><?php echo $language->get('label_this_week'); ?></button>
			<button ng-click="loadSummary('this_month', 'This Month');" onClick="return false;" id="btn_this_month" class="btn btn-primary"><?php echo $language->get('label_this_month'); ?></button>
			<button ng-click="loadSummary('this_year', 'This Year');" onClick="return false;" id="btn_this_year" class="btn btn-success"><?php echo $language->get('label_this_year'); ?></button>
		</div>
	</div>
</div>

<div class="row" style="padding-right:10px;">
<div class="col-md-6">
	<h4 class="text-center"><b><?php echo $language->get('text_income'); ?></b></h4>
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tbody>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_invoice_amount'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['invoice_amount']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_tax_collection'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['tax_collection']);?>
					</td>
				</tr>
				<!-- <tr>
					<td class="w-60">
						<?php //echo $language->get('label_due_collection'); ?>
					</td>
					<td class="w-40 text-right">
						<?php //echo currency_format($report['due_collection']);?>
					</td>
				</tr> -->
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_prev_due_collection'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['prev_due_collection']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_loan_taken'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['loan_taken']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_gift_card_sell'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['gift_card_sell']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_gift_card_topup'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['gift_card_topup']);?>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr class="bg-gray">
					<td class="w-60 text-right">
						<?php echo $language->get('label_total_income'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['total_income']);?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="col-md-6">
	<h4 class="text-center"><b><?php echo $language->get('text_expense'); ?></b></h4>
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tbody>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_product_purchase'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['product_purchase']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_purchase_tax'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['purchase_tax']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_sell_tax'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['sell_tax']);?>
					</td>
				</tr>
				<!-- <tr>
					<td class="w-60">
						<?php //echo $language->get('label_due_paid'); ?>
					</td>
					<td class="w-40 text-right">
						<?php //echo currency_format($report['due_paid']);?>
					</td>
				</tr> -->
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_prev_due_paid'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['prev_due_paid']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_loan_paid'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['loan_paid']);?>
					</td>
				</tr>
				<tr>
					<td class="w-60">
						<?php echo $language->get('label_other_expense'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['other_expense']);?>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr class="bg-gray">
					<td class="w-60 text-right">
						<?php echo $language->get('label_total_expense'); ?>
					</td>
					<td class="w-40 text-right">
						<?php echo currency_format($report['total_expense']);?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tbody>
				<tr>
					<td class="w-30 bg-green text-right">
						<?php echo $language->get('label_profit_from_product'); ?>
					</td>
					<td class="w-20 bg-green text-right">
						<?php
							$order_tax = get_tax('order_tax', from(), to());
				          	$item_tax = get_tax('item_tax', from(), to());
				          	$tax = $order_tax + $item_tax;
				          	$totalSellingPrice = selling_price(from(), to()) - $tax;
				          	$totalPurchasePrice = sell_buying_price(from(), to());
				        ?>
						<?php echo currency_format($totalSellingPrice - $totalPurchasePrice);?>
					</td>
					<td class="w-30 bg-blue text-right">
						<?php echo $language->get('label_income'); ?> - <?php echo $language->get('label_expense'); ?>
					</td>
					<td class="w-20 bg-blue text-right">
						<?php echo currency_format($report['income_minus_expense']);?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	</div>
</div>