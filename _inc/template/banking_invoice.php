<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td class="col-xs-2">
							<?php echo $language->get('label_invoice_id'); ?>
						</td>
						<td class="col-xs-10">
							<?php echo $invoice['invoice_id']; ?>
						</td>
					</tr>
					<tr>
						<td class="col-xs-2">
							<?php echo $language->get('label_datetime'); ?>
						</td>
						<td class="col-xs-10">
							<?php echo format_date($invoice['buy_date'] . ' ' . $invoice['buy_time']); ?>
						</td>
					</tr>
					<tr>
						<td class="col-xs-2">
							<?php echo $language->get('label_note'); ?>
						</td>
						<td class="col-xs-10">
							<?php echo $invoice['invoice_note']; ?>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="table-responsive">
				<table class="table table-bordered margin-b0">
					<thead>
					<tr class="active">
						<th>Product</th>
						<th class="col-xs-2">
							<?php echo $language->get('label_cost'); ?>
						</th>
						<th class="text-right" class="col-xs-2">
							<?php echo $language->get('label_sub_total'); ?>
						</th>
					</tr>
					</thead>
					<tbody>
						<?php foreach ($invoice_items as $product) : ?>
							<tr>
								<td>
									<?php echo $product['item_name']; ?>
								</td>
								<td class="text-right">
									<?php echo $product['item_buying_price']; ?>
								</td>
								<td class="text-right">
									<?php echo $product['item_total']; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<thead>
						<tr class="active">
							<td class="text-right" colspan="2">
								<?php echo $language->get('label_total'); ?>
							</td>
							<td class="col-xs-2 text-right">
								<?php echo $invoice['paid_amount']; ?>
							</td>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>