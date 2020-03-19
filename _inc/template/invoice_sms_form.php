<?php $language->load('invoice'); ?>
<form id="send-form" class="form form-horizontal" action="sms/index.php" method="post">
  <input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo $invoice_id; ?>">
  <input type="hidden" id="action_type" name="action_type" value="SEND">
  <div class="box-body">
    <div class="form-group">
      <label for="phone_number" class="col-sm-3 control-label">
        <?php echo $language->get('label_phone_number'); ?>
      </label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $invoice['customer_mobile'] ? $invoice['customer_mobile'] : $invoice['mobile_number'];?>" required>
      </div>
    </div>
    <div class="form-group">
      <label for="message" class="col-sm-3 control-label">
        <?php echo $language->get('label_message'); ?>
      </label>
      <div class="col-sm-9">
        <textarea class="form-control" id="message" name="message" rows="5" required><?php echo $language->get('invoice_sms_text'); ?></textarea>
        <p><?php echo $tags; ?></p>
      </div>,
    </div>
    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-6">
        <button id="send" data-form="#send-form" class="btn btn-info" data-loading-text="Sending...">
          <span class="fa fa-fw fa-paper-plane"></span>
          <?php echo $language->get('button_send'); ?>
        </button>
      </div>
    </div>
  </div>
</form>