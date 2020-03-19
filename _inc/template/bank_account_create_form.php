<?php $language->load('bank'); ?>
<form id="create-account-form" class="form-horizontal" action="bank_account.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  <div class="box-body">
    
    <div class="form-group">
      <label for="account_name" class="col-sm-3 control-label">
        <?php echo $language->get('label_account_name'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="account_name" value="<?php echo isset($request->post['account_name']) ? $request->post['account_name'] : null; ?>" name="account_name" required>
      </div>
    </div>

    <div class="form-group">
      <label for="account_details" class="col-sm-3 control-label">
        <?php echo $language->get('label_account_details'); ?>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="account_details" value="<?php echo isset($request->post['account_details']) ? $request->post['account_details'] : null; ?>" name="account_details" required>
      </div>
    </div>

    <div class="form-group">
      <label for="initial_balance" class="col-sm-3 control-label">
        <?php echo $language->get('label_initial_balance'); ?>
      </label>
      <div class="col-sm-7">
        <input type="number" class="form-control" id="initial_balance" value="<?php echo isset($request->post['initial_balance']) ? $request->post['initial_balance'] : null; ?>" name="initial_balance" required>
      </div>
    </div>

    <div class="form-group">
      <label for="account_no" class="col-sm-3 control-label">
        <?php echo $language->get('label_account_no'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="account_no" value="<?php echo isset($request->post['account_no']) ? $request->post['account_no'] : null; ?>" name="account_no" required>
      </div>
    </div>

    <div class="form-group">
      <label for="contact_person" class="col-sm-3 control-label">
        <?php echo $language->get('label_contact_person'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="contact_person" value="<?php echo isset($request->post['contact_person']) ? $request->post['contact_person'] : null; ?>" name="contact_person" required>
      </div>
    </div>

    <div class="form-group">
      <label for="phone_number" class="col-sm-3 control-label">
        <?php echo $language->get('label_phone_number'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="phone_number" value="<?php echo isset($request->post['phone_number']) ? $request->post['phone_number'] : null; ?>" name="phone_number" required>
      </div>
    </div>

    <div class="form-group">
      <label for="url" class="col-sm-3 control-label">
        <?php echo $language->get('label_internal_banking_url'); ?>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="url" value="<?php echo isset($request->post['url']) ? $request->post['url'] : null; ?>" name="url" required>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7 store-selector">
        <div class="checkbox selector">
          <label>
            <input type="checkbox" onclick="$('input[name*=\'account_store\']').prop('checked', this.checked);"> Select / Deselect
          </label>
        </div>
        <div class="filter-searchbox">
          <input ng-model="search_store" class="form-control" type="text" id="search_store" placeholder="<?php echo $language->get('search'); ?>">
        </div>
        <div class="well well-sm store-well"> 
          <div filter-list="search_store">
          <?php foreach(get_stores() as $the_store) : ?>                    
            <div class="checkbox">
              <label>                         
                <input type="checkbox" name="account_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo $the_store['store_id'] == store_id() ? 'checked' : null; ?>>
                <?php echo $the_store['name']; ?>
              </label>
            </div>
          <?php endforeach; ?>
          </div>
        </div>
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
        <button class="btn btn-info" id="create-account-submit" type="submit" name="create-account-submit" data-form="#create-account-form" data-loading-text="Saving...">
          <span class="fa fa-fw fa-save"></span> 
          <?php echo $language->get('button_save'); ?>
        </button>  
        <button type="reset" class="btn btn-danger" id="reset" name="reset">
          <span class="fa fa-fw fa-circle"></span>
          <?php echo $language->get('button_reset'); ?>
        </button>
      </div>
    </div>
     
  </div>
</form>