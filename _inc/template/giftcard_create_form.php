<?php $language->load('giftcard'); ?>
<form id="create-giftcard-form" class="form-horizontal" action="giftcard.php" method="post" enctype="multipart/form-data">
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  <div class="box-body">

    <div class="form-group all">
      <label for="card_no" class="col-sm-3 control-label">
        <?php echo $language->get('label_card_no'); ?> <i class="required">*</i>
      </label>             
      <div class="col-sm-7">           
        <div class="input-group">
          <input type="text" name="card_no" id="card_no" class="form-control" autocomplete="off" required readonly>
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
        <input type="number" class="form-control" id="giftcard_value" value="0" name="giftcard_value" required>
      </div>
    </div>

    <div class="form-group">
      <label for="balance" class="col-sm-3 control-label">
        <?php echo $language->get('label_balance'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="number" class="form-control" id="balance" value="0" name="balance" required>
      </div>
    </div>

    <div class="form-group">
      <label for="customer_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_customer'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <select id="customer_id" class="form-control" name="customer_id" >
          <option value=""><?php echo $language->get('text_select'); ?></option>
          <?php foreach (get_customers(array('exclude'=>1,'filter_has_giftcard'=>0)) as $the_customer) : ?>
            <option value="<?php echo $the_customer['customer_id'];?>">
            <?php echo $the_customer['customer_name'];?>
          </option>
        <?php endforeach;?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="expiry" class="col-sm-3 control-label">
        <?php echo $language->get('label_expiry_date'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="date" class="form-control" id="expiry" value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" name="expiry" autocomplete="off" required>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-7">
        <button class="btn btn-info" id="create-giftcard-submit" type="submit" name="create-giftcard-submit" data-form="#create-giftcard-form" data-loading-text="Saving...">
          <span class="fa fa-fw fa-save"></span> 
          <?php echo $language->get('button_create_giftcard'); ?>
        </button>
        <button type="reset" class="btn btn-danger" id="reset" name="reset">
          <span class="fa fa-fw fa-circle"></span>
          <?php echo $language->get('button_reset'); ?>
        </button>
      </div>
    </div>
     
  </div>
</form>