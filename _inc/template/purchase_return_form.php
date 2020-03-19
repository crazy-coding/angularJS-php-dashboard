<?php 
include ("../../_init.php");
$language->load('buy'); 
?>

<style type="text/css">
.modal-lg {
	width: 98%;
}
</style>

<form class="form-horizontal" id="purchase-return-form" action="purchase_return.php">

<input type="hidden" name="invoice-id" value="{{ order.invoice_id }}">
<input type="hidden" name="sup-id" value="{{ order.sup_id }}">

<div class="bootbox-body">
	<div class="table-selection">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 cart-details hidden-xs bootboox-container">
			<div class="table-responsive mt-30">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><?php echo $language->get('text_invoice_id'); ?></td>
							<td>{{ order.invoice_id }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="text-center">
				<h4><?php echo $language->get('text_order_summary'); ?></h4>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tbody>
						<tr ng-repeat="items in order.items">
							<td class="text-center w-10">
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
							<td class="text-right w-40">{{ order.subtotal  | formatDecimal:2 }}</td>
						</tr>
						<tr>
							<th class="text-right w-60"  colspan="2">
								<?php echo $language->get('label_discount'); ?> {{ order.discount_type  == 'percentage' ? '('+order.discount_amount+'%)' : '' }}
							</th>
							<td class="text-right w-40" >{{ order.discount_type  == 'percentage' ? (_percentage(order.payable_amount, order.discount_amount) | formatDecimal:2) : (order.discount_amount | formatDecimal:2) }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_order_tax'); ?>
							</th>
							<td class="text-right w-40">{{ order.order_tax  | formatDecimal:2 }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_previous_due'); ?>
							</th>
							<td class="text-right w-40">{{ order.previous_due  | formatDecimal:2 }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_payable_amount'); ?>
								<small>({{ order.total_items }} items)</small>
							</th>
							<td class="text-right w-40">{{ order.payable_amount | formatDecimal:2 }}</td>
						</tr>

						<!-- Payments start -->
						<tr ng-repeat="payments in order.payments" class="{{ payments.type=='return' ? 'danger' : 'success' }}">
							<th ng-show="payments.type=='purchase'" class="text-right w-60" colspan="2">
								<small><i>Paid by</i></small> {{ payments.name }} <i>on</i> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='purchase'" class="text-right w-40">{{ payments.amount | formatDecimal:2 }}</td>

							<th ng-show="payments.type=='return'" class="text-right w-60" colspan="2"><small><i>Return on</i></small> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='return'" class="text-right w-40">{{ payments.amount | formatDecimal:2 }}</td>

							<th ng-show="payments.type=='change'" class="text-right w-60" colspan="2"><small><i>Balance on</i></small> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='change'" class="text-right w-40">{{ payments.balance | formatDecimal:2 }}</td>
						</tr>
						<!-- Payments end -->

						<tr class="danger">
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_due'); ?>
							</th>
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
			<div class="pl-10">
				<div class="text-center">
					<h4><?php echo $language->get('text_return_item'); ?></h4>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr class="bg-gray">
								<th class="text-center w-10">Yes/No</th>
								<th class="w-20"><?php echo $language->get('label_product_name'); ?></th>
								<th class="text-center w-70"><?php echo $language->get('label_return_quantity'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="items in order.items">
								<td class="text-center w-10">
									<input type="hidden" name="items['{{ items.item_id }}'][item_id]" value="{{ items.item_id }}">									
									<input type="checkbox" name="items['{{ items.item_id }}'][check]" value="1" style="width:30px;height:30px;">
								</td>
								<td class="w-70">{{ items.item_name }} (x{{ items.item_quantity }})</td>
								<td class="text-center w-20">
									<input class="text-center" type="number" name="items['{{ items.item_id }}'][item_quantity]" value="{{ items.item_quantity }}" onClick="this.select();">
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<textarea class="form-control" name="note" placeholder="<?php echo $language->get('placeholder_type_any_note'); ?>"></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</form>