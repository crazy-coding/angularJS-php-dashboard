<?php 
include ("../../_init.php");
$language->load('payment'); 
?>

<style type="text/css">
.modal-lg {
	width: 98%;
}
</style>

<form class="form-horizontal" id="checkout-form" action="payment.php">

<input type="hidden" name="invoice-id" value="{{ order.invoice_id }}">
<input type="hidden" name="customer-id" value="{{ order.customer_id }}">
<input type="hidden" name="pmethod-id" value="{{ pmethodId }}">

<div class="bootbox-body">
	<div class="table-selection">
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 bootbox-tab-menu bootboox-container p-0">
			<div class="list-group">
				<?php $inc = 0;foreach(get_pmethods() as $pmethod) : ?>
					<a class="text-left list-group-item pmethod_item" id="pmethod_<?php echo $pmethod['pmethod_id']; ?>" href="javascript:void(0)" <?php echo $inc == 0 ? 'ng-init="selectPaymentMethod('.$pmethod['pmethod_id'].',\''.$pmethod['name'].'\')"' : null;?> ng-click="selectPaymentMethod('<?php echo $pmethod['pmethod_id']; ?>', '<?php echo $pmethod['name']; ?>')" onClick="return false;" ><span class="fa fa-fw fa-angle-double-right"></span> <b><?php echo $pmethod['name']; ?></b></a>
				<?php $inc++;endforeach; ?>
			</div>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-7 bootboox-container pmethod-option checkout-payment-option">
			<div class="tab-wrapper tab-cheque bootboox-container tab-cheque-payment">
				<h4 ng-show="pmethodId" class="text-center pmehthods"><?php echo $language->get('text_pmethod'); ?> <b>{{ pmethodName }}</b></h4>
				<button ng-click="payNowWithFullPaid()" onClick="return false;" class="btn btn-success full-paid">
					<span class="fa fa-fw fa-money"></span> <?php echo $language->get('button_full_payment'); ?>
				</button>
				<div class="input-group input-group-lg">
					<span class="input-group-addon hidden-sm hidden-xs"><?php echo $language->get('text_pay_amount'); ?></span>
					<input class="form-control" name="paid-amount" ng-model="paidAmount" placeholder="<?php echo $language->get('placeholder_input_an_amount'); ?>">
				</div>
				<div class="input-group input-group-sm mt-5">
					<span class="input-group-addon hidden-sm hidden-xs">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $language->get('text_discount_amount'); ?></span>
					<input class="form-control" name="discount-amount" ng-model="discountAmount" placeholder="<?php echo $language->get('placeholder_input_discount_amount'); ?>">
				</div>
				<div class="mt-5">
					<textarea name="note" id="note" class="form-control note" placeholder="<?php echo $language->get('placeholder_note_here');?>"></textarea>
				</div>
				<div bind-html-compile="rawPaymentMethodHtml"></div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 cart-details hidden-xs bootboox-container">
			<div class="table-responsive mt-30">
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><?php echo $language->get('label_invoice_id'); ?></td>
							<td>{{ order.invoice_id }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="text-center">
				<h4><?php echo $language->get('text_billing_details'); ?></h4>
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
						<!-- <tr>
							<th class="text-right w-60"  colspan="2">
								<?php //echo $language->get('label_discount'); ?> {{ order.discount_type  == 'percentage' ? '('+order.discount_amount+'%)' : '' }}
							</th>
							<input type="hidden" name="discount-type" value="{{ order.discount_type }}">
							<input type="hidden" name="discount-amount" value="{{ order.discount_type  == 'percentage' ? _percentage(order.payable_amount, order.discount_amount) : order.discount_amount }}">
							<td class="text-right w-40" >{{ order.discount_type  == 'percentage' ? (_percentage(order.payable_amount, order.discount_amount) | formatDecimal:2) : (order.discount_amount | formatDecimal:2) }}</td>
						</tr> -->
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_order_tax'); ?>
							</th>
							<input type="hidden" name="tax-amount" value="{{ order.order_tax }}">
							<td class="text-right w-40">{{ order.order_tax  | formatDecimal:2 }}</td>
						</tr>

						<!-- Payments start -->
						<tr ng-repeat="payments in order.payments" class="{{ payments.type=='discount' ? 'danger' : 'success' }}">
							<th ng-show="payments.type=='discount'" class="text-right w-60" colspan="2"><small><i>Discount on</i></small> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='discount'" class="text-right w-40">{{ payments.amount | formatDecimal:2 }}</td>
						</tr>
						<!-- Payments end -->

						<tr>
							<th class="text-right w-60 bg-gray" colspan="2">
								<?php echo $language->get('label_payable_amount'); ?>
								<small>({{ order.total_items }} items)</small>
							</th>
							<input type="hidden" name="payable-amount" value="{{ order.payable_amount }}">
							<td class="text-right w-40 bg-gray">{{ order.payable_amount | formatDecimal:2 }}</td>
						</tr>

						<!-- Payments start -->
						<tr ng-repeat="payments in order.payments" class="{{ payments.type=='return' ? 'danger' : 'success' }}">
							<th ng-show="payments.type=='due_paid'" class="text-right w-60" colspan="2"><small><i>Duepaid on</i></small> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='due_paid'" class="text-right w-40">{{ payments.amount | formatDecimal:2 }}</td>

							<th ng-show="payments.type=='sell'" class="text-right w-60" colspan="2"><small><i>Paid by</i></small> {{ payments.name }} <i>on</i> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='sell'" class="text-right w-40">{{ payments.amount | formatDecimal:2 }}</td>

							<th ng-show="payments.type=='return'" class="text-right w-60" colspan="2"><small><i>Return on</i></small> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='return'" class="text-right w-40">{{ payments.amount | formatDecimal:2 }}</td>
						</tr>
						<!-- Payments end -->

						<tr class="danger">
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_due'); ?>
							</th>
							<input type="hidden" name="due-amount" value="{{ order.due }}">
							<td class="text-right w-40">{{ order.due | formatDecimal:2 }}</td>
						</tr>

						<!-- Payments start -->
						<tr ng-repeat="payments in order.payments" class="{{ 'success' }}">
							<th ng-show="payments.type=='change'" class="text-right w-60" colspan="2"><small><i>Change on</i></small> {{ payments.created_at }} <small><i>by {{ payments.by }}</i></small></th>
							<td ng-show="payments.type=='change'" class="text-right w-40">{{ payments.pos_balance | formatDecimal:2 }}</td>
						</tr>
						<!-- Payments end -->

						<tr ng-show="order.invoice_not" class="active">
							<td colspan="3">
								<b><?php echo $language->get('label_note'); ?>:</b> {{ order.invoice_note }}
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
</form>