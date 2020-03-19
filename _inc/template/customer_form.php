<?php $language->load('customer'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="customer-form" action="customer.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="customer_name" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="customer_name" value="<?php echo $customer['customer_name']; ?>" name="customer_name" required>
      </div>
    </div>

    <div class="form-group">
      <label for="customer_email" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_email'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="email" class="form-control" id="customer_email" value="<?php echo $customer['customer_email']; ?>" name="customer_email">
      </div>
    </div>

    <div class="form-group">
      <label for="customer_mobile" class="col-sm-4 control-label">
        <?php echo $language->get('label_mobile'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="customer_mobile" value="<?php echo $customer['customer_mobile']; ?>" name="customer_mobile">
      </div>
    </div>

    <div class="form-group">
      <label for="customer_sex" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_sex'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <select name="customer_sex" class="form-control" required>
          <option <?php echo $customer['customer_sex'] == 1 ? 'selected' : null; ?> value="1">
            <?php echo $language->get('text_male'); ?>
          </option>
          <option <?php echo $customer['customer_sex'] == 2 ? 'selected' : null; ?> value="2">
            <?php echo $language->get('text_female'); ?>
          </option>
        </select>
      </div>
    </div>

     <div class="form-group">
      <label for="customer_age" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_age'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="number" class="form-control" id="customer_age" value="<?php echo $customer['customer_age']; ?>" name="customer_age" onKeyUp="if(this.value>140){this.value='140';}else if(this.value<0){this.value='0';}">
      </div>
    </div>

    <div class="form-group">
      <label for="customer_address" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_address'), null); ?>
      </label>
      <div class="col-sm-6">
        <textarea class="form-control" id="customer_address" name="customer_address"><?php echo $customer['customer_address']; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="customer_city" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_city'), null); ?>
      </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="customer_city" value="<?php echo $customer['customer_city']; ?>" name="customer_city">
      </div>
    </div>

    <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
    <div class="form-group">
      <label for="customer_state" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_state'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <?php echo stateSelector($customer['customer_state'], 'customer_state', 'customer_state'); ?>
      </div>
    </div>
    <?php else : ?>
      <div class="form-group">
        <label for="customer_state" class="col-sm-4 control-label">
          <?php echo sprintf($language->get('label_state'), null); ?>
        </label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="customer_state" value="<?php echo $customer['customer_state']; ?>" name="customer_state">
        </div>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="country" class="col-sm-4 control-label">
        <?php echo $language->get('label_country'); ?>
      </label>
      <div class="col-sm-6">
        <?php echo countrySelector($customer['customer_country'], 'customer_country', 'customer_country'); ?>
      </div>
    </div>

    <div class="form-group">
      <label for="status" class="col-sm-4 control-label">
        <?php echo $language->get('label_status'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <select id="status" class="form-control" name="status" >
          <option <?php echo isset($customer['status']) && $customer['status'] == '1' ? 'selected' : null; ?> value="1">
            <?php echo $language->get('text_active'); ?>
          </option>
          <option <?php echo isset($customer['status']) && $customer['status'] == '0' ? 'selected' : null; ?> value="0">
            <?php echo $language->get('text_inactive'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_sort_order'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="number" class="form-control" id="sort_order" value="<?php echo $customer['sort_order']; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label for="customer_address" class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="customer-update" data-form="#customer-form" data-datatable="#customer-customer-list" class="btn btn-info" name="btn_edit_customer" data-loading-text="Updating...">
          <span class="fa fa-fw fa-pencil"></span>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>