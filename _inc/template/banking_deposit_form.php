<?php $language->load('accounting'); ?>
<form id="form-deposit" class="form-horizontal" action="banking.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="DEPOSIT">  
  <div class="box-body">
    <div class="form-group">
      <label for="account_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_account'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <select id="account_id" class="form-control" name="account_id" >
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
          <span data-toggle="tooltip" title="" data-original-title="e.g. Transaction ID, Check No."></span><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="ref_no" value="<?php echo $ref_no; ?>" name="ref_no" <?php echo $ref_no ? 'readonly' : null; ?> autocomplete="off">
      </div>
    </div>
    <div class="form-group">
      <label for="title" class="col-sm-3 control-label">
        <?php echo $language->get('label_about'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" id="title" class="form-control" name="title">
      </div>
    </div>
    <div class="form-group">
      <label for="amount" class="col-sm-3 control-label">
        <?php echo $language->get('label_amount'); ?><i class="required">*</i>
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
      <label for="image" class="col-sm-3 control-label">
        <?php echo $language->get('label_attach_file'); ?>
      </label>
      <div class="col-sm-7">
        <div class="preview-thumbnail">
          <a ng-click="POSFilemanagerModal({target:'image',thumb:'image_thumb'})" onClick="return false;" href="#" data-toggle="image" id="image_thumb">
            <img src="../assets/itsolution24/img/noimage.jpg">
          </a>
          <input type="hidden" name="image" id="image" value="">
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-7">            
        <button id="deposit-confirm-btn" class="btn btn-info" data-form="#form-deposit" data-datatable="#invoice-invoice-list" name="submit" data-loading-text="Processing...">
          <i class="fa fa-fw fa-plus"></i>
          <?php echo $language->get('button_deposit_now'); ?>
        </button>
      </div>
    </div>
  </div>
</form>