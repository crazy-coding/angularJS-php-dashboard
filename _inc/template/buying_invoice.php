<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<tbody>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_invoice_id'); ?>
						</td>
						<td class="w-70">
							<?php echo $invoice['invoice_id']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_datetime'); ?>
						</td>
						<td class="w-70">
							<?php echo $invoice['buy_date'] . ' ' . $invoice['buy_time']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_note'); ?>
						</td>
						<td class="w-70">
							<?php echo $invoice['invoice_note']; ?>
						</td>
					</tr>
					<tr>
						<td class="w-30">
							<?php echo $language->get('label_attachment'); ?>
						</td>
						<td class="w-70">
							<?php if (isset($invoice['attachment']) && (is_file(DIR_STORAGE . 'buying-invoices/' . $invoice['attachment']) && file_exists(DIR_STORAGE . 'buying-invoices/' . $invoice['attachment']))) : ?>
								<a href="<?php echo root_url().'/storage/buying-invoices'; ?>/<?php echo $invoice['attachment']; ?>" target="_blink" class="pointer">
				              		<img  src="<?php echo root_url().'/storage/buying-invoices'; ?>/<?php echo $invoice['attachment']; ?>" width="40" height="40">
				              	</a>
				            <?php endif;?>
						</td>
					</tr>
				</tbody>
			</table>

			<h4><?php echo $language->get('text_product_list'); ?></h4>
			
			<div class="table-responsive">
				<table class="table table-bordered margin-b0">
					<thead>
					<tr class="bg-gray">
						<th class="w-60"><?php echo $language->get('label_product'); ?></th>
						<th class="w-20 text-right">
							<?php echo $language->get('label_cost'); ?>
						</th>
						<th class="w-20 text-right"">
							<?php echo $language->get('label_sub_total'); ?>
						</th>
					</tr>
					</thead>
					<tbody>
						<?php foreach ($invoice_items as $product) : ?>
							<tr>
								<td>
									<?php echo $product['item_name']; ?> (x<?php echo $product['item_quantity']; ?>)
								</td>
								<td class="text-right">
									<?php echo currency_format($product['item_buying_price']); ?>
								</td>
								<td class="text-right">
									<?php echo currency_format($product['item_total']); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr class="bg-gray">
							<td class="text-right" colspan="2">
								<?php echo $language->get('label_payable_amount'); ?>
							</td>
							<td class="w-20 text-right">
								<?php echo currency_format($invoice['payable_amount']); ?>
							</td>
						</tr>
						<tr class="bg-gray">
							<td class="text-right" colspan="2">
								<?php echo $language->get('label_paid_amount'); ?>
							</td>
							<td class="w-20 text-right">
								<?php echo currency_format($invoice['paid_amount']); ?>
							</td>
						</tr>
						<tr class="danger">
							<td class="text-right" colspan="2">
								<?php echo $language->get('label_due_amount'); ?>
							</td>
							<td class="w-20 text-right">
								<?php echo currency_format($invoice['due']); ?>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>

			<h4><?php echo $language->get('text_payments'); ?></h4>
			
			<div class="table-responsive">
				<table class="table table-bordered margin-b0">
					<tbody>
						<?php if (!empty($payments)) : ?>
                        <?php 
	                        foreach ($payments as $row) : 
	                          if ($row['type'] == 'return') {
	                            $color = 'danger';
	                          } elseif ($row['type'] == 'change') {
	                            $color = 'info';
	                          } elseif ($row['type'] == 'discount') {
	                            $color = 'warning';
	                          } else {
	                            $color = 'success';
	                          }
	                          ?>
	                          <tr class="bt-1 <?php echo $color;?>">
	                            <td class="w-50 text-right">
	                              <?php if ($row['type'] == 'return') : ?>
	                                <small><i>Return on</i></small> <?php echo $row['created_at'];?> <small><i>by</i></small> <?php echo get_the_user($row['created_by'], 'username');?>
	                              <?php elseif ($row['type'] == 'change') : ?>
	                                <small><i>Change on</i></small> <?php echo $row['created_at'];?> <small><i>by</i></small> <?php echo get_the_user($row['created_by'], 'username');?>
	                              <?php elseif ($row['type'] == 'discount') : ?>
	                                <small><i>Discount on</i></small> <?php echo $row['created_at'];?> <small><i>by</i></small> <?php echo get_the_user($row['created_by'], 'username');?>
	                              <?php elseif ($row['type'] == 'due_paid') : ?>
	                                <small><i>Duepaid on</i></small> <?php echo $row['created_at'];?> 
	                                <?php if ($row['pmethod_id']) : ?>
	                                (via <?php echo get_the_pmethod($row['pmethod_id'], 'name');?>)
	                                <?php endif; ?>
	                                by <?php echo get_the_user($row['created_by'], 'username');?>
	                              <?php else : ?>
	                                <small><i>Paid on</i></small> <?php echo $row['created_at'];?> 
	                                <?php if ($row['pmethod_id']) : ?>
	                                (via <?php echo get_the_pmethod($row['pmethod_id'], 'name');?>)
	                                <?php endif; ?>
	                                by <?php echo get_the_user($row['created_by'], 'username');?>
	                              <?php endif; ?>
	                            </td>
	                            <td class="w-25 text-right">
	                              <?php if ($row['type'] == 'return') : ?>
	                                <?php echo $language->get('label_amount'); ?>:&nbsp; <?php echo currency_format($row['amount']); ?>
	                              <?php elseif ($row['type'] == 'change') : ?>
	                                &nbsp;
	                              <?php else : ?>
	                                <?php echo $language->get('label_amount'); ?>:&nbsp; <?php echo currency_format($row['total_paid']); ?>
	                              <?php endif; ?>
	                            </td>
	                            <td class="w-25 text-right">
	                              <?php if ($row['type'] != 'return' && $row['balance'] > 0) : ?>
	                                <?php echo $language->get('label_change'); ?>:&nbsp; <?php echo currency_format($row['balance']); ?>
	                              <?php else: ?>
	                                &nbsp;
	                              <?php endif; ?>
	                            </td>
	                          </tr>
	                        <?php endforeach; ?>
	                      <?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>