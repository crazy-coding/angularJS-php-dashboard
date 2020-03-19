<?php 
include ("../../_init.php");
$language->load('pos'); 
?>

<style type="text/css">
.modal-lg {
	width: 98%;
}
</style>

<form class="form-horizontal" id="checkout-form" action="payment.php">

<input type="hidden" name="invoice-id" value="{{ order.invoice_id }}">
<input type="hidden" name="customer-id" value="{{ order.customer_id }}">

<div class="bootbox-body">
	<div class="table-selection">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 cart-details hidden-xs bootboox-container">
			<div class="table-responsive mt-30">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td>Invoice ID</td>
							<td>{{ order.invoice_id }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="text-center">
				<h4>Order Details</h4>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tbody>
						<tr ng-repeat="items in order.items">
							<td class="text-center w-10">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][item_qty_type]" value="{{ items.item_qty_type }}">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][item_id]" value="{{ items.item_id ? items.item_id : items.group_id }}">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][category_id]" value="{{ items.categoryId }}">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][sup_id]" value="{{ items.supId }}">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][item_name]" value="{{ items.item_name }}">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][item_price]" value="{{ items.item_price  | formatDecimal:2 }}">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][item_quantity]" value="{{ items.item_quantity }}">
								<input type="hidden" name="item['{{ items.item_id ? items.item_id : items.group_id }}'][item_total]" value="{{ items.item_total  | formatDecimal:2 }}">
								{{ $index+1 }}
							</td>
							<td class="w-70">{{ items.item_name }} (x{{ items.item_quantity }})</td>
							<td class="text-right w-20">{{ items.item_total  | formatDecimal:2 }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_subtotal'); ?>
							</th>
							<input type="hidden" name="sub-total" value="{{ order.subtotal }}">
							<td class="text-right w-40">{{ order.subtotal  | formatDecimal:2 }}</td>
						</tr>
						<tr>
							<th class="text-right w-60"  colspan="2">
								<?php echo $language->get('label_discount'); ?> {{ order.discount_type  == 'percentage' ? '('+order.discount_amount+'%)' : '' }}
							</th>
							<input type="hidden" name="discount-type" value="{{ order.discount_type }}">
							<input type="hidden" name="discount-amount" value="{{ order.discount_type  == 'percentage' ? _percentage(order.payable_amount, order.discount_amount) : order.discount_amount }}">
							<td class="text-right w-40" >{{ order.discount_type  == 'percentage' ? (_percentage(order.payable_amount, order.discount_amount) | formatDecimal:2) : (order.discount_amount | formatDecimal:2) }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_tax'); ?>
							</th>
							<input type="hidden" name="tax-amount" value="{{ order.tax_amount }}">
							<td class="text-right w-40">{{ order.tax_amount  | formatDecimal:2 }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_previous_due'); ?>
							</th>
							<input type="hidden" name="previous-due" value="{{ order.previous_due }}">
							<td class="text-right w-40">{{ order.previous_due  | formatDecimal:2 }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_payable_amount'); ?>
								<small>({{ order.total_items }} items)</small>
							</th>
							<input type="hidden" name="payable-amount" value="{{ order.payable_amount }}">
							<td class="text-right w-40">{{ order.payable_amount | formatDecimal:2 }}</td>
						</tr>

						<!-- Payments start -->
						<tr class="success" ng-repeat="payments in order.payments">
							<th class="text-right w-60" colspan="2">{{ payments.name }} <i>on</i> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td class="text-right w-40">{{ payments.amount | formatDecimal:2 }}</td>
						</tr>
						<!-- Payments end -->

						<tr class="danger">
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_due'); ?>
							</th>
							<input type="hidden" name="due-amount" value="{{ order.due }}">
							<td class="text-right w-40">{{ order.due | formatDecimal:2 }}</td>
						</tr>

						<tr class="warning">
							<th class="text-right w-60" colspan="2">Balance</th>
							<td class="text-right w-40">{{ order.balance | formatDecimal:2 }}</td>
						</tr>

						<tr ng-show="order.invoice_not" class="active">
							<td colspan="3">
								<b><?php echo $language->get('label_note'); ?>:</b> {{ order.invoice_note }}
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 bootboox-container pmethod-option checkout-payment-option">
			<div class="tab-wrapper tab-cheque bootboox-container tab-cheque-payment">
				
			</div>
		</div>
	</div>
</div>
</form>