<style type="text/css">
.expense-view [class^="col-"] {
	padding-right: 15px;
}
</style>

<section class="expense-view">
<div class="row">
	<div class="col-md-12">
		<h4><b><?php echo $language->get('label_summary'); ?></b></h4>
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
					<tr class="active">
						<td class="w-60"><?php echo $language->get('label_category_name'); ?></td>
						<td class="w-40 text-right"><?php echo $language->get('label_total'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php 
					$total = 0;
					foreach ($summary as $expense) : ?>
						<tr>
							<td class="w-60">
								<?php echo $expense['category_name']; ?>
							</td>
							<td class="w-40 text-right">
								<?php 
								$total += $expense['total'];
								echo currency_format($expense['total']); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="bg-gray">
					<tr>
						<td class="w-70 text-right"><?php echo $language->get('label_grand_total'); ?></td>
						<td class="w-40 text-right"><?php echo currency_format($total);?></td>
					</tr>
				</tfoot>
			</table>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h4><b><?php echo $language->get('label_this_week'); ?></b></h4>
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
					<tr class="active">
						<td class="w-60"><?php echo $language->get('label_category_name'); ?></td>
						<td class="w-40 text-right"><?php echo $language->get('label_total'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php 
					$total = 0;
					foreach ($week_summary as $expense) : ?>
						<tr>
							<td class="w-60">
								<?php echo $expense['category_name']; ?>
							</td>
							<td class="w-40 text-right">
								<?php 
								$total += $expense['total'];
								echo currency_format($expense['total']); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="bg-gray">
					<tr>
						<td class="w-70 text-right"><?php echo $language->get('label_grand_total'); ?></td>
						<td class="w-40 text-right"><?php echo currency_format($total);?></td>
					</tr>
				</tfoot>
			</table>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h4><b><?php echo $language->get('label_this_month'); ?></b></h4>
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
					<tr class="active">
						<td class="w-60"><?php echo $language->get('label_category_name'); ?></td>
						<td class="w-40 text-right"><?php echo $language->get('label_total'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php 
					$total = 0;
					foreach ($month_summary as $expense) : ?>
						<tr>
							<td class="w-60">
								<?php echo $expense['category_name']; ?>
							</td>
							<td class="w-40 text-right">
								<?php 
								$total += $expense['total'];
								echo currency_format($expense['total']); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="bg-gray">
					<tr>
						<td class="w-70 text-right"><?php echo $language->get('label_grand_total'); ?></td>
						<td class="w-40 text-right"><?php echo currency_format($total);?></td>
					</tr>
				</tfoot>
			</table>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<h4><b><?php echo $language->get('label_this_year'); ?></b></h4>
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
					<tr class="active">
						<td class="w-60"><?php echo $language->get('label_category_name'); ?></td>
						<td class="w-40 text-right"><?php echo $language->get('label_total'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php 
					$total = 0;
					foreach ($year_summary as $expense) : ?>
						<tr>
							<td class="w-60">
								<?php echo $expense['category_name']; ?>
							</td>
							<td class="w-40 text-right">
								<?php 
								$total += $expense['total'];
								echo currency_format($expense['total']); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="bg-gray">
					<tr>
						<td class="w-70 text-right"><?php echo $language->get('label_grand_total'); ?></td>
						<td class="w-40 text-right"><?php echo currency_format($total);?></td>
					</tr>
				</tfoot>
			</table>
			</div>
		</div>
	</div>
</div>
</section>