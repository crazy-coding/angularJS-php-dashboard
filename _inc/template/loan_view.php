<?php $language->load('loan'); ?>
<div class="box-body">
  
  <div class="table-responsive">
    <h4><b><?php echo $language->get('text_loan_details'); ?></b></h4>
    <table class="table table-bordered table-condenced">
      <tbody>
        <tr>
          <th class="text-right w-25"><?php echo $language->get('label_ref_no'); ?></th>
          <td>#<?php echo $loan['ref_no'];?></td>
        </tr>
        <tr>
          <th class="text-right w-25"><?php echo $language->get('label_datetime'); ?></th>
          <td><?php echo $loan['created_at'];?></td>
        </tr>
        <tr>
          <th class="text-right w-25"><?php echo $language->get('label_created_by'); ?></th>
          <td><?php echo get_the_user($loan['created_by'],'username');?></td>
        </tr>
        <tr>
          <th class="text-right w-25"><?php echo $language->get('label_loan_from'); ?></th>
          <td><?php echo $loan['loan_from'];?></td>
        </tr>
        <tr>
          <th class="text-right w-25"><?php echo $language->get('label_title'); ?></th>
          <td><?php echo $loan['title'];?></td>
        </tr>
        <tr>
          <th class="text-right w-25"><?php echo $language->get('label_details'); ?></th>
          <td><?php echo $loan['details'];?></td>
        </tr>
        <tr>
          <th class="w-25 text-right">
            <?php echo $language->get('label_attach_file'); ?>
          </th>
          <td class="w-70">
            <?php if (isset($loan['attachment']) && ((FILEMANAGERPATH && is_file(FILEMANAGERPATH.$loan['attachment']) && file_exists(FILEMANAGERPATH.$loan['attachment'])) || (is_file(DIR_STORAGE . 'products' . $loan['attachment']) && file_exists(DIR_STORAGE . 'products' . $loan['attachment'])))) : ?>
                    <a target="_blink" href="<?php echo FILEMANAGERURL ? FILEMANAGERURL : root_url().'/storage/products'; ?>/<?php echo $loan['attachment']; ?>"><img  src="<?php echo FILEMANAGERURL ? FILEMANAGERURL : root_url().'/storage/products'; ?>/<?php echo $loan['attachment']; ?>" width="40" height="50"></a>
                  <?php endif; ?>
          </td>
        </tr>
        <tr class="bg-gray">
          <th class="text-right w-25"><?php echo $language->get('label_basic_amount'); ?></th>
          <td><?php echo currency_format($loan['amount']);?></td>
        </tr>
        <tr class="bg-gray">
          <th class="text-right w-25"><?php echo $language->get('label_interest'); ?>(%)</th>
          <td><?php echo currency_format($loan['interest']);?></td>
        </tr>
        <tr class="bg-blue">
          <th class="text-right w-25"><?php echo $language->get('label_payable_amount'); ?></th>
          <td><?php echo currency_format($loan['payable']);?></td>
        </tr>
        <tr class="bg-green">
          <th class="text-right w-25"><?php echo $language->get('label_paid_amount'); ?></th>
          <td><?php echo currency_format($loan['paid']);?></td>
        </tr>
        <tr class="bg-red">
          <th class="text-right w-25"><?php echo $language->get('label_due_amount'); ?></th>
          <td><?php echo currency_format($loan['due']);?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="table-responsive">
    <h4><b><?php echo $language->get('text_payments'); ?></b></h4>
    <table class="table table-bordered table-condenced table-striped">
      <thead>
        <tr class="bg-gray">
          <th class="text-center w-20"><?php echo $language->get('label_ref_no'); ?></th>
          <th class="text-center w-20"><?php echo $language->get('label_datetime'); ?></th>
          <th class="w-20"><?php echo $language->get('label_note'); ?></th>
          <th class="w-20"><?php echo $language->get('label_paid_by'); ?></th>
          <th class="text-right w-20"><?php echo $language->get('label_paid_amount'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if ($payments) : ?>
        <?php foreach ($payments as $p) : ?>
          <tr class="bg-green">
            <td class="text-center w-20">#<?php echo $p['ref_no'];?></td>
            <td><?php echo $p['created_at'];?></td>
            <td><?php echo $p['note'];?></td>
            <td><?php echo get_the_user($p['created_by'], 'username');?></td>
            <td class="text-right w-25"><?php echo currency_format($p['paid']);?></td>
          </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
