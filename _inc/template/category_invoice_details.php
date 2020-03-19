<table class="table table-striped">
	<thead>
		<tr class="active">
			<th>Sl. No.</th>
			<th>Invoice ID</th>
			<th>Patient ID</th>
			<th>Received At</th>
			<th class="text-right">Item Total</th>
			<th class="text-right">Discount Amount</th>
			<th class="text-right">Net Amount</th>
		</tr>
	</thead>
	<tbody>
		<tbody>
			<?php 
			$total_price = 0;
			$total_item_discount = 0;
			$total_net_amount = 0;
			$inc = 1;
			foreach ($invoices as $invoice) : ?>

				<tr>
					<td><?php echo $inc; ?></td>
					<td>
						<?php 
							$invoice_id = $invoice['ref_invoice_id'] ? $invoice['ref_invoice_id'] : $invoice['invoice_id'];
							echo $invoice_id; ?>
					</td>
					<td>
						<?php 
							$patient_id = $invoice['patient_id'];
							echo patient_prefix().$patient_id; ?>
					</td>
					<td><?php echo format_date($invoice['created_at']); ?></td>
					<td class="text-right">
						<?php 
						$total_price += $invoice['item_total_price'];
						echo number_format($invoice['item_total_price'], 2); ?></td>
					<td class="text-right">
						<?php 
						$total_item_discount += $invoice['item_discount'];
						echo number_format($invoice['item_discount'], 2); ?></td>
					<td class="text-right">
						<?php 
						$total_net_amount += $invoice['item_total_price'] - $invoice['item_discount'];
						echo number_format($invoice['item_total_price'] - $invoice['item_discount'], 2); ?></td>
				</tr>

			<?php 
			$inc++;
			endforeach; ?>
		</tbody>
		<tfoot>
			<tr class="bg-gray">
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th class="text-right"><?php echo number_format($total_price, 2); ?></th>
				<th class="text-right"><?php echo number_format($total_item_discount, 2); ?></th>
				<th class="text-right"><?php echo number_format($total_net_amount, 2); ?></th>
			</tr>
		</tfoot>
		
	</tbody>
</table>