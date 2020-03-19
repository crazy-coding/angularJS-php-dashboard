<?php $language->load('invoice'); ?>
<form id="send-form" class="form form-horizontal" action="sms/index.php" method="post">
  <input type="hidden" id="id" name="id" value="<?php echo $row['id'];?>">
  <input type="hidden" id="action_type" name="action_type" value="RESEND">
  <div class="box-body">
    <div class="form-group">
      <label for="mobile_number" class="col-sm-3 control-label">
        <?php echo $language->get('label_mobile_number'); ?>
      </label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $row['mobile_number'];?>" required>
      </div>
    </div>
    <div class="form-group">
      <label for="sms_text" class="col-sm-3 control-label">
        <?php echo $language->get('label_message'); ?>
      </label>
      <div class="col-sm-9">
        <textarea class="form-control" id="sms_text" name="sms_text" rows="5" required><?php echo $row['sms_text']; ?></textarea>
      </div>,
    </div>
    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-6">
        <button id="resend" data-form="#send-form" class="btn btn-info" data-datatable="#sms-sms-list" data-loading-text="Sending...">
          <span class="fa fa-fw fa-paper-plane"></span>
          <?php echo $language->get('button_resend'); ?>
        </button>
      </div>
    </div>
  </div>
</form>