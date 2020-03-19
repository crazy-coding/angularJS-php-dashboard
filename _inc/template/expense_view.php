<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_reference_no'); ?>
						</td>
						<td class="w-70">
							<?php echo $expense['reference_no']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_created_at'); ?>
						</td>
						<td class="w-70">
							<?php echo format_date($expense['created_at']); ?>
						</td>
					</tr>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_title'); ?>
						</td>
						<td class="w-70">
							<?php echo $expense['title']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_note'); ?>
						</td>
						<td class="w-70">
							<?php echo $expense['note']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_amount'); ?>
						</td>
						<td class="w-70">
							<?php echo $expense['amount']; ?>
						</td>
					</tr>
				</tbody>
			</table>
			</div>
		</div>
	</div>
</div>