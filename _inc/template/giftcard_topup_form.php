<?php $language->load('giftcard'); ?>
<form id="topup-giftcard-form" class="form-horizontal" action="giftcard.php" method="post" enctype="multipart/form-data">
  <input type="hidden" id="action_type" name="action_type" value="TOPUP">
  <input type="hidden" id="id" name="id" value="<?php echo $giftcard['id'];?>">
  <div class="box-body">

    <div class="form-group">
      <label for="amount" class="col-sm-3 control-label">
        <?php echo $language->get('label_amount'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="number" class="form-control" id="amount" name="amount" required>
      </div>
    </div>

    <div class="form-group">
      <label for="expiry" class="col-sm-3 control-label">
        <?php echo $language->get('label_expiry_date'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="date" class="form-control" id="expiry" name="expiry" autocomplete="off" required>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-7">
        <button class="btn btn-info" id="giftcard-topup-save" type="submit" name="giftcard-topup-save" data-form="#topup-giftcard-form" data-datatable="#giftcard-giftcard-list" data-loading-text="Processing...">
          <span class="fa fa-fw fa-money"></span> 
          <?php echo $language->get('button_topup_now'); ?>
        </button>  
      </div>
    </div>
     
  </div>
</form>