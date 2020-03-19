<?php include ("../../_init.php");
$language->load('payment'); 
?>

<form class="form-horizontal" id="checkout-form" action="place_order.php">
<input type="hidden" name="invoice-id" value="{{ invoiceId }}">
<input type="hidden" name="customer-id" value="{{ customerId }}">
<input type="hidden" name="customer-mobile-number" value="{{ customerMobileNumber }}">
<input type="hidden" name="pmethod-id" value="{{ pmethodId }}">
<div class="bootbox-body">
	<div class="table-selection">
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 bootboox-tab-menu bootboox-container p-0">
			<div class="list-group">
				<?php $inc = 0;foreach(get_pmethods() as $pmethod) :?>
					<a class="text-left list-group-item pmethod_item" id="pmethod_<?php echo $pmethod['pmethod_id']; ?>" href="javascript:void(0)" <?php echo $inc == 0 ? 'ng-init="selectPaymentMethod('.$pmethod['pmethod_id'].',\''.$pmethod['name'].'\')"' : null;?> ng-click="selectPaymentMethod('<?php echo $pmethod['pmethod_id']; ?>', '<?php echo $pmethod['name']; ?>')" onClick="return false;"><span class="fa fa-fw fa-angle-double-right"></span> <b><?php echo $pmethod['name']; ?></b></a>
				<?php $inc++;endforeach; ?>
			</div>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-7 checkout-payment-option bootboox-container">
			<div class="tab-wrapper tab-cheque bootboox-container tab-cheque-payment">
				<h4 ng-show="pmethodId" class="text-center title"><?php echo $language->get('text_pmethod'); ?> <b>{{ pmethodName }}</b></h4>
				<button ng-click="checkoutWithFullPaid()" onClick="return false;" class="btn btn-success full-paid">
					<span class="fa fa-fw fa-money"></span> <?php echo $language->get('button_full_payment'); ?>
				</button>
				<button ng-click="checkoutWithFullDue()" onClick="return false;" class="btn btn-danger full-due">
					<span class="fa fa-fw fa-minus"></span> <?php echo $language->get('button_full_due'); ?>
				</button>
				<div class="input-group input-group-lg pmethod-field-wrapper">
					<span class="input-group-addon hidden-sm hidden-xs"><?php echo $language->get('text_pay_amount'); ?></span>
					<input class="form-control" name="paid-amount" ng-model="paidAmount" placeholder="<?php echo $language->get('placeholder_input_an_amount'); ?>">
				</div>
				<div class="mt-5">
					<textarea name="invoice-note" class="form-control invoice-note" placeholder="<?php echo $language->get('placeholder_note_here');?>">{{ invoiceNote }}</textarea>
				</div>
				<div bind-html-compile="rawPaymentMethodHtml"></div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 cart-details hidden-xs bootboox-container">
			<div class="text-center">
				<h4><?php echo $language->get('text_billing_details'); ?></h4>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tbody>
						<tr ng-repeat="items in itemArray">
							<td class="text-center w-10">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][item_qty_type]" value="{{ items.qtytype }}">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][item_id]" value="{{ items.id ? items.id : items.group_id }}">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][category_id]" value="{{ items.categoryId }}">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][sup_id]" value="{{ items.supId }}">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][item_name]" value="{{ items.name }}">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][item_price]" value="{{ items.price  | formatDecimal:2 }}">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][item_quantity]" value="{{ items.quantity }}">
								<input type="hidden" name="product-item['{{ items.id ? items.id : items.group_id }}'][item_total]" value="{{ items.subTotal  | formatDecimal:2 }}">
								{{ $index+1 }}
							</td>
							<td class="w-70">{{ items.name }} (x{{ items.quantity }})</td>
							<td class="text-right w-20">{{ items.subTotal  | formatDecimal:2 }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_subtotal'); ?>
							</th>
							<input type="hidden" name="sub-total" value="{{ totalAmount }}">
							<td class="text-right w-40">{{ totalAmount  | formatDecimal:2 }}</td>
						</tr>
						<tr>
							<th class="text-right w-60"  colspan="2">
								<?php echo $language->get('label_discount'); ?> {{ discountType  == 'percentage' ? '('+discountAmount+'%)' : '' }}
							</th>
							<input type="hidden" name="discount-type" value="{{ discountType }}">
							<input type="hidden" name="discount-amount" value="{{ discountType  == 'percentage' ? _percentage(totalAmount, discountAmount) : discountAmount }}">
							<td class="text-right w-40" >{{ discountType  == 'percentage' ? (_percentage(totalAmount, discountAmount) | formatDecimal:2) : (discountAmount | formatDecimal:2) }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_order_tax'); ?>
							</th>
							<input type="hidden" name="tax-amount" value="{{ taxAmount }}">
							<td class="text-right w-40">{{ taxAmount  | formatDecimal:2 }}</td>
						</tr>
						<tr ng-show="restaurantOrderType == 'delivery'">
							<th class="text-right w-60"  colspan="2">
								<?php echo $language->get('label_shipping'); ?> {{ shippingType  == 'percentage' ? '('+shippingAmount+'%)' : '' }}
							</th>
							<input type="hidden" name="shipping-type" value="{{ shippingType }}">
							<input type="hidden" name="shipping-amount" value="{{ shippingType  == 'percentage' ? _percentage(totalAmount, shippingAmount) : shippingAmount }}">
							<td class="text-right w-40" >{{ shippingType  == 'percentage' ? (_percentage(totalAmount, shippingAmount) | formatDecimal:2) : (shippingAmount | formatDecimal:2) }}</td>
						</tr>
						<tr>
							<th class="text-right w-60" colspan="2">
								<?php echo $language->get('label_payable_amount'); ?>
								<small>({{ totalItem }} items)</small>
							</th>
							<input type="hidden" name="payable-amount" value="{{ totalPayable }}">
							<td class="text-right w-40">{{ totalPayable  | formatDecimal:2 }}</td>
						</tr>
						<tr ng-show="invoiceNote"><td colspan="3">&nbsp;</td></tr>
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
</form>