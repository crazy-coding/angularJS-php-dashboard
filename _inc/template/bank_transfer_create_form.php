<?php $language->load('accounting'); ?>
<form id="form-transfer" class="form-horizontal" action="banking.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="TRANSFER">  
  <div class="box-body">
    <div class="form-group">
      <label for="from_account_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_from'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <select id="from_account_id" class="form-control" name="from_account_id" >
          <option value="">
            <?php echo $language->get('text_select'); ?>
          </option>
          <?php foreach (get_bank_accounts() as $account) : ?>
            <option value="<?php echo $account['id'];?>">
              <?php echo $account['account_name']; ?> (<?php echo currency_format(get_the_account_balance($account['id']));?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label for="to_account_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_to'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <select id="to_account_id" class="form-control" name="to_account_id" >
          <option value="">
            <?php echo $language->get('text_select'); ?>
          </option>
          <?php foreach (get_bank_accounts() as $account) : ?>
            <option value="<?php echo $account['id'];?>">
              <?php echo $account['account_name']; ?> (<?php echo currency_format(get_the_account_balance($account['id']));?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <?php $ref_no = isset($invoice['ref_no']) ? $invoice['ref_no'] : null; ?>
      <label for="ref_no" class="col-sm-3 control-label">
          <?php echo $language->get('label_ref_no'); ?> 
          <span data-toggle="tooltip" title="" data-original-title="e.g. Transaction ID, Check No."></span>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="ref_no" value="<?php echo $ref_no; ?>" name="ref_no" <?php echo $ref_no ? 'readonly' : null; ?> autocomplete="off">
      </div>
    </div>
    <div class="form-group">
      <label for="title" class="col-sm-3 control-label">
        <?php echo $language->get('label_about'); ?>
      </label>
      <div class="col-sm-7">
        <input type="text" id="title" class="form-control" name="title">
      </div>
    </div>
    <div class="form-group">
      <label for="amount" class="col-sm-3 control-label">
        <?php echo $language->get('label_amount'); ?>
       </label>
      <div class="col-sm-7">
        <input type="number" id="amount" class="form-control" name="amount" onclick="this.select();">
      </div>
    </div>
    <div class="form-group">
      <label for="details" class="col-sm-3 control-label">
        <?php echo $language->get('label_details'); ?>
      </label>
      <div class="col-sm-7">
        <textarea name="details" id="details" class="form-control"><?php echo isset($invoice) ? $invoice['details'] : null; ?></textarea>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-7">            
        <button id="transfer-confirm-btn" class="btn btn-info" data-form="#form-transfer" data-datatable="#invoice-invoice-list" name="submit" data-loading-text="Processing...">
          <i class="fa fa-fw fa-plus"></i>
          <?php echo $language->get('button_transfer_now'); ?>
        </button>
      </div>
    </div>
  </div>
</form>