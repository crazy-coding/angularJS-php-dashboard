<?php $language->load('customer'); ?>

<form id="create-customer-form" class="form-horizontal" action="customer.php" method="post" enctype="multipart/form-data">
  
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="customer_name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="customer_name" value="<?php echo isset($request->post['customer_name']) ? $request->post['customer_name'] : null; ?>" name="customer_name" required>
      </div>
    </div>

    <div class="form-group">
      <label for="customer_email" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_email'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="email" class="form-control" id="customer_email" value="<?php echo unique_id(6);?>@gmail.com" name="customer_email">
      </div>
    </div>

    <div class="form-group">
      <label for="customer_mobile" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_phone'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="customer_mobile" value="<?php echo isset($request->post['customer_mobile']) ? $request->post['customer_mobile'] : null; ?>" name="customer_mobile">
      </div>
    </div>

    <div class="form-group">
      <label for="customer_sex" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_sex'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <select id="customer_sex" name="customer_sex" class="form-control" required>
          <option value="1"<?php echo isset($request->post['customer_sex']) && $request->post['customer_sex'] == '1' ? ' selected' : null; ?>>
            <?php echo $language->get('label_male'); ?>
          </option>
          <option value="2"<?php echo isset($request->post['customer_sex']) && $request->post['customer_sex'] == '2' ? ' selected' : null; ?>>
            <?php echo $language->get('label_female'); ?>
          </option>
          <option value="3"<?php echo isset($request->post['customer_sex']) && $request->post['customer_sex'] == '3' ? ' selected' : null; ?>>
            <?php echo $language->get('label_others'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="customer_age" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_age'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="number" class="form-control" id="customer_age" value="<?php echo isset($request->post['customer_age']) ? $request->post['customer_age'] : null; ?>" name="customer_age" onKeyUp="if(this.value>140){this.value='140';}else if(this.value<0){this.value='0';}">
      </div>
    </div>

    <div class="form-group">
      <label for="customer_address" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_address'), null); ?>
      </label>
      <div class="col-sm-7">
        <textarea class="form-control" id="customer_address" name="customer_address" value="<?php echo isset($request->post['customer_address']) ? $request->post['customer_address'] : null; ?>"></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="customer_city" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_city'), null); ?>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="customer_city" value="<?php echo isset($request->post['customer_city']) ? $request->post['customer_city'] : null; ?>" name="customer_city">
      </div>
    </div>

    <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
    <div class="form-group">
      <label for="customer_state" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_state'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <?php echo stateSelector(isset($request->post['customer_state']) ? $request->post['customer_state'] : null, 'customer_state', 'customer_state'); ?>
      </div>
    </div>
    <?php else : ?>
      <div class="form-group">
        <label for="customer_state" class="col-sm-3 control-label">
          <?php echo sprintf($language->get('label_state'), null); ?>
        </label>
        <div class="col-sm-7">
          <input type="text" class="form-control" id="customer_state" value="<?php echo isset($request->post['customer_state']) ? $request->post['customer_state'] : null; ?>" name="customer_state">
        </div>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="country" class="col-sm-3 control-label">
        <?php echo $language->get('label_country'); ?>
      </label>
      <div class="col-sm-7">
        <?php echo countrySelector(isset($request->post['customer_country']) ? $request->post['customer_country'] : null, 'customer_country', 'customer_country'); ?>
      </div>
    </div>

    <div class="form-group">
      <label for="status" class="col-sm-3 control-label">
        <?php echo $language->get('label_status'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <select id="status" class="form-control" name="status" >
          <option <?php echo isset($request->post['status']) && $request->post['status'] == '1' ? 'selected' : null; ?> value="1">
            <?php echo $language->get('text_active'); ?>
          </option>
          <option <?php echo isset($request->post['status']) && $request->post['status'] == '0' ? 'selected' : null; ?> value="0">
            <?php echo $language->get('text_inactive'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_sort_order'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="number" class="form-control" id="sort_order" value="<?php echo isset($request->post['sort_order']) ? $request->post['sort_order'] : 0; ?>" name="sort_order" required>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-7">
        <button class="btn btn-info" id="create-customer-submit" type="submit" name="create-customer-submit" data-form="#create-customer-form" data-loading-text="Saving...">
          <span class="fa fa-fw fa-save"></span>
          <?php echo $language->get('button_save'); ?>
        </button> 
        <button type="reset" class="btn btn-danger" id="reset" name="reset"><span class="fa fa-fw fa-circle-o"></span>
          <?php echo $language->get('button_reset'); ?>
        </button>
      </div>
    </div>

  </div>
</form>