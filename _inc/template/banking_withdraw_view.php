<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			
			<table class="table table-bordered table-striped">
				<tbody>
					<tr>
						<td class="w-30 text-right">
							<?php echo $language->get('label_account'); ?>
						</td>
						<td class="w-70">
							<?php echo get_the_bank_account($invoice['account_id'], 'account_name'); ?>
						</td>
					</tr>
					<tr>
						<td class="w-30 text-right">
							<?php echo $language->get('label_title'); ?>
						</td>
						<td class="w-70">
							<?php echo format_date($invoice['title']); ?>
						</td>
					</tr>
					<tr>
						<td class="w-30 text-right">
							<?php echo $language->get('label_slip_no'); ?>
						</td>
						<td class="w-70">
							<?php echo $invoice['ref_no']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30 text-right">
							<?php echo $language->get('label_datetime'); ?>
						</td>
						<td class="w-70">
							<?php echo format_date($invoice['created_at']); ?>
						</td>
					</tr>
					<tr>
						<td class="w-30 text-right">
							<?php echo $language->get('label_by'); ?>
						</td>
						<td class="w-70">
							<?php echo get_the_user($invoice['created_by'], 'username'); ?>
						</td>
					</tr>
					<tr>
						<td class="w-30 text-right">
							<?php echo $language->get('label_note'); ?>
						</td>
						<td class="w-70">
							<?php echo $invoice['details']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30 text-right">
							<?php echo $language->get('label_attach_file'); ?>
						</td>
						<td class="w-70">
							<?php if (isset($invoice['image']) && ((FILEMANAGERPATH && is_file(FILEMANAGERPATH.$invoice['image']) && file_exists(FILEMANAGERPATH.$invoice['image'])) || (is_file(DIR_STORAGE . 'products' . $invoice['image']) && file_exists(DIR_STORAGE . 'products' . $invoice['image'])))) : ?>
				              <a target="_blink" href="<?php echo FILEMANAGERURL ? FILEMANAGERURL : root_url().'/storage/products'; ?>/<?php echo $invoice['image']; ?>"><img  src="<?php echo FILEMANAGERURL ? FILEMANAGERURL : root_url().'/storage/products'; ?>/<?php echo $invoice['image']; ?>" width="40" height="50"></a>
				            <?php endif; ?>
						</td>
					</tr>
					<tr class="bg-gray">
						<td class="w-30 text-right">
							<?php echo $language->get('label_total'); ?>
						</td>
						<td class="w-70">
							<?php echo currency_format($invoice['amount']); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>