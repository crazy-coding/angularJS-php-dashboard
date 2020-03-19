<table class="table table-striped">
	<thead>
		<tr>
			<th class="w-one"><?php echo $language->get('label_serial_no'); ?></th>
			<th class="w-three"><?php echo $language->get('label_created_at'); ?></th>
			<th class="w-two"><?php echo $language->get('label_invoice_id'); ?></th>
			<th class="w-three"><?php echo $language->get('label_customer_name'); ?></th>
			<th class="w-two text-right" width="9%"><?php echo $language->get('label_due_amount'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
			$todays_due = 0;
		?>

		<?php 
		$report_model = $registry->get('loader')->model('report');
		$inc = 1;
		foreach ($invoices as $invoice) : 
			$invoice_id = $invoice['ref_invoice_id'] ? $invoice['ref_invoice_id'] : $invoice['invoice_id'];
			?>

			<tr>
				<td><?php echo $inc; ?></td>

				<td><?php echo format_date($invoice['created_at']); ?></td>

				<td><a href="view_invoice.php?invoice_id=<?php echo $invoice_id; ?>" target="_blink"><?php echo $invoice_id; ?></a></td>

				<td><?php echo get_the_customer($invoice['customer_id'],'customer_name'); ?></td>

				<td class="text-right">
					<?php $todays_due += $invoice['todays_due']; ?>
					<?php echo number_format($invoice['todays_due'], 2); ?>
				</td>
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
			<th class="text-right">
				<?php echo number_format($todays_due, 2); ?>
			</th>
		</tr>
	</tfoot>
</table>