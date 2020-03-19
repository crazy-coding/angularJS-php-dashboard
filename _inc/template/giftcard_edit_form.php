<?php $language->load('giftcard'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="giftcard-form" action="giftcard.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="id" name="id" value="<?php echo $giftcard['id']; ?>">

  <div class="box-body">

    <div class="form-group">
      <label for="customer_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_customer'); ?>
      </label>
      <div class="col-sm-7">
        <select id="customer_id" class="form-control" name="customer_id" readonly disabled>
          <option value=""><?php echo get_the_customer($giftcard['customer_id'],'customer_name');?></option>
        </select>
      </div>
    </div>

    <div class="form-group all">
      <label for="card_no" class="col-sm-3 control-label">
        <?php echo $language->get('label_card_no'); ?> <i class="required">*</i>
      </label>             
      <div class="col-sm-7">           
        <div class="input-group">
          <input type="text" id="card_no" class="form-control" value="<?php echo $giftcard['card_no'];?>" name="card_no" autocomplete="off" required readonly>
          <span class="input-group-addon pointer random_card_no">
              <i class="fa fa-random"></i>
          </span>
          <span class="input-group-addon pointer" onClick="$('#card_no').removeAttr('readonly').focus().select();">
              <i class="fa fa-pencil"></i>
          </span>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="giftcard_value" class="col-sm-3 control-label">
        <?php echo $language->get('label_giftcard_value'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="giftcard_value" value="<?php echo $giftcard['value'];?>" name="giftcard_value" required>
      </div>
    </div>

    <div class="form-group">
      <label for="expiry" class="col-sm-3 control-label">
        <?php echo $language->get('label_expiry_date'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="date" class="form-control" id="expiry" value="<?php echo $giftcard['expiry'];?>" name="expiry" autocomplete="off" required>
      </div>
    </div>

    <div class="form-group">
      <label for="giftcard_address" class="col-sm-3 control-label"></label>
      <div class="col-sm-8">            
        <button id="giftcard-update" class="btn btn-info"  data-form="#giftcard-form" data-datatable="#giftcard-giftcard-list" name="btn_edit_customer" data-loading-text="Updating...">
          <i class="fa fa-fw fa-pencil"></i>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>

  </div>
</form>