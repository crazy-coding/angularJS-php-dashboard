<?php include ("../../_init.php");

$language->load('pos'); ?>

<form class="form-inline" id="pay-form" action="pos_processing.php">

<input type="hidden" name="invoice-id" value="{{ invoiceId }}">
<input type="hidden" name="customer-id" value="{{ customerId }}">
<input type="hidden" name="customer-mobile" value="{{ customerMobileNumber }}">
<textarea class="hidden" name="invoice-note">{{ invoiceNote }}</textarea>

	<div class="row line-vertical">
		<div class="col-sm-6">
			<div  class="panel panel-default min-height">
				
				<div class="panel-heading">
					<h4 class="panel-title">
						<span>
							<?php echo $language->get('text_summary'); ?>
						</span>
					</h4>
				</div>

				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<tbody>
								<tr ng-repeat="items in itemArray">
									<td class="text-center w-10">
										<input type="hidden" name="product-item['{{ items.id }}'][item_id]" value="{{ items.id }}">
										<input type="hidden" name="product-item['{{ items.id }}'][category_id]" value="{{ items.categoryId }}">
										<input type="hidden" name="product-item['{{ items.id }}'][sup_id]" value="{{ items.supId }}">
										<input type="hidden" name="product-item['{{ items.id }}'][item_name]" value="{{ items.name }}">
										<input type="hidden" name="product-item['{{ items.id }}'][item_price]" value="{{ items.price  | formatDecimal:2 }}">
										<input type="hidden" name="product-item['{{ items.id }}'][item_quantity]" value="{{ items.quantity }}">
										<input type="hidden" name="product-item['{{ items.id }}'][item_total]" value="{{ items.subTotal  | formatDecimal:2 }}">
										{{ $index+1 }}
									</td>
									<td class="w-70">{{ items.name }} (x{{ items.quantity }})</td>
									<td class="text-right w-20">{{ items.subTotal | formatDecimal:2 }}</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th class="text-right w-60" colspan="2">
										<?php echo $language->get('label_subtotal'); ?>
									</th>
									<input type="hidden" name="sub-total" value="{{ totalAmount }}">
									<td class="text-right w-40">{{ totalAmount | formatDecimal:2 }}</td>
								</tr>
								<tr>
									<th class="text-right w-60"  colspan="2">
										<?php echo $language->get('label_discount'); ?> {{ discountType  == 'percentage' ? '('+discountAmount+'%)' : '' }}
									</th>
									<input type="hidden" name="discount-amount" value="{{ discountType  == 'percentage' ? _percentage(totalAmount, discountAmount) : discountAmount }}">
									<input type="hidden" name="discount-type" value="{{ discountType }}">
									<td class="text-right w-40" >{{ discountType  == 'percentage' ? (_percentage(totalAmount, discountAmount) | formatDecimal:2) : (discountAmount | formatDecimal:2) }}</td>
								</tr>
								<tr>
									<th class="text-right w-60" colspan="2">
										<?php echo $language->get('label_tax_amount'); ?>
									</th>
									<input type="hidden" name="tax-amount" value="{{ taxAmount | formatDecimal:2 }}">
									<td class="text-right w-40">{{ taxAmount | formatDecimal:2 }}</td>
								</tr>
								<tr>
									<th class="text-right w-60" colspan="2">
										<?php echo $language->get('label_previous_due'); ?>
									</th>
									<input type="hidden" name="previous-due" value="{{ dueAmount }}">
									<td class="text-right w-40">{{ dueAmount  | formatDecimal:2 }}</td>
								</tr>
								<tr>
									<th class="text-right w-60" colspan="2">
										<?php echo $language->get('label_payable_amount'); ?>
										<small>({{ totalItem }} items)</small>
									</th>
									<input type="hidden" name="payable-amount" value="{{ totalPayable | formatDecimal:2 }}">
									<td class="text-right w-40">{{ totalPayable  | formatDecimal:2 }}</td>
								</tr>
								<tr ng-show="done">
									<th class="text-right w-60" colspan="2">
										<?php echo $language->get('label_paid_amount'); ?>({{ paymentMethod }})
									</th>
									<input type="hidden" name="paid-amount" value="{{ paidAmount }}">
									<td class="text-right w-40">{{ paidAmount  | formatDecimal:2 }}</td>
								</tr>
								<tr ng-show="done">
									<th class="text-right w-60" colspan="2">
										<?php echo $language->get('label_due'); ?>
									</th>
									<input type="hidden" name="due-amount" value="{{ balance }}">
									<td class="text-right w-40">{{ balance | formatDecimal:2 }}</td>
								</tr>
								<tr ng-show="done">
									<th class="text-right w-60" colspan="2">
										<?php echo $language->get('label_change'); ?>
									</th>
									<td class="text-right w-40">{{ change | formatDecimal:2 }}</td>
								</tr>
								<tr><td colspan="3">&nbsp;</td></tr>
								<tr ng-show="invoiceNote" class="active">
									<td colspan="3">
										<b><?php echo $language->get('label_note'); ?>:</b> {{ invoiceNote }}
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- col end -->

		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-body text-center">

					<h2 ng-show="done" class="done-meseage title">
						<i class="fa fa-check"></i>
						<?php echo $language->get('label_payment_received'); ?> 
						<div class="invoice-id">
							<?php echo $language->get('label_invoice_id'); ?> &rarr; 
							<a href="view_invoice.php?invoice_id={{ invoiceId }}" target="_blink">{{ invoiceId }}
							</a>
						</div>
					</h2>
					
					<div ng-show="!done" class="form-group form-group-lg mb-20">
						<div class="col-sm-12">
							<h2 class="title">
								<?php echo $language->get('label_pay_amount'); ?>
							</h2>
						</div>
						<div class="col-sm-12">
							<input onClick="this.select();" type="text" class="form-control text-center paid-bg" id="payable-amount" ng-model="paidAmount" onKeyUp="if(this.value<0){this.value=0}" onKeyUp="if(this.value<0){this.value=0;}"  ng-keypress="payByEnterKey($event)" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" autocomplete="off" autofocus>
						</div>
					</div>

					<div ng-show="!done" class="form-group mt-20">
						<?php foreach(get_payment_methods() as $payment) : ?>
							<button ng-click="pay(<?php echo $payment['payment_id']; ?>, '<?php echo $payment['name']; ?>');" onClick="return false;" id="payment-method-<?php echo $payment['payment_id']; ?>" class="btn btn-warning" type="button">
								<span class="fa fa-fw fa-money"></span> <?php echo $payment['name']; ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div ng-show="done" class="panel panel-default">
				<div class="panel-body text-center">
					<div>
						<div class="btn-group">
							<a class="btn btn-sm btn-info" href="view_invoice.php?invoice_id={{invoiceId}}" target="_blink">
								<span class="fa fa-fw fa-print"></span>
								<?php echo $language->get('label_print_receipt'); ?>
							</a> 
							<a class="btn btn-sm btn-warning" ng-click="sendInvoiceViaEmail(invoiceId)" onClick="return false;" href="#"><span class="fa fa-fw fa-envelope"></span>
								<?php echo $language->get('label_email_receipt'); ?>
							</a>
						</div>
					</div>
					<div class="text-center pos-payment-done">
						<button ng-click="closePayNowModal();" id="done" class="btn btn-block btn-lg btn-success" type="button">
							<span class="fa fa-fw fa-check"></span>
							<?php echo $language->get('button_done'); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- col end -->
	</div>
</form>

<script type="text/javascript">
$(function() {
	$("#pay-form").on("keyup keypress", function(e) {
	    var keyCode = e.keyCode || e.which;
	    if (keyCode == 13) {
	        e.preventDefault();
	    }
	});
});
</script>