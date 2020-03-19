<?php $language->load('supplier'); ?>
<form id="create-supplier-form" class="form-horizontal" action="supplier.php" method="post" enctype="multipart/form-data">
  
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="sup_name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="sup_name" name="sup_name" value="<?php echo isset($request->post['sup_name']) ? $request->post['sup_name'] : null; ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_email" class="col-sm-3 control-label">
        <?php echo $language->get('label_email'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="email" class="form-control" id="sup_email" name="sup_email" value="<?php echo isset($request->post['sup_email']) ? $request->post['sup_email'] : null; ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_mobile" class="col-sm-3 control-label">
        <?php echo $language->get('label_mobile'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="sup_mobile" name="sup_mobile" value="<?php echo isset($request->post['sup_mobile']) ? $request->post['sup_mobile'] : null; ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_address" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_address'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <textarea class="form-control" id="sup_address"  name="sup_address" value="<?php echo isset($request->post['sup_address']) ? $request->post['sup_address'] : null; ?>" required></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_city" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_city'), null); ?>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="sup_city" value="<?php echo isset($request->post['sup_city']) ? $request->post['sup_city'] : null; ?>" name="sup_city">
      </div>
    </div>

    <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
    <div class="form-group">
      <label for="sup_state" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_state'), null); ?>
      </label>
      <div class="col-sm-7">
        <?php echo stateSelector(isset($request->post['sup_state']) ? $request->post['sup_state'] : null, 'sup_state', 'sup_state'); ?>
      </div>
    </div>
    <?php else : ?>
      <div class="form-group">
        <label for="sup_state" class="col-sm-3 control-label">
          <?php echo sprintf($language->get('label_state'), null); ?>
        </label>
        <div class="col-sm-7">
          <input type="text" class="form-control" id="sup_state" value="<?php echo isset($request->post['sup_state']) ? $request->post['sup_state'] : null; ?>" name="sup_state">
        </div>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="country" class="col-sm-3 control-label">
        <?php echo $language->get('label_country'); ?>
      </label>
      <div class="col-sm-7">
        <?php echo countrySelector(isset($request->post['sup_country']) ? $request->post['sup_country'] : null, 'sup_country', 'sup_country'); ?>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7 store-selector">
        <div class="checkbox selector">
          <label>
            <input type="checkbox" onclick="$('input[name*=\'supplier_store\']').prop('checked', this.checked);"> Select / Deselect
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
                  <input type="checkbox" name="supplier_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo $the_store['store_id'] == store_id() ? 'checked' : null; ?>>
                  <?php echo $the_store['name']; ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_details" class="col-sm-3 control-label">
        <?php echo $language->get('label_details'); ?>
      </label>
      <div class="col-sm-7">
        <textarea class="form-control" id="sup_details"  name="sup_details" value="<?php echo isset($request->post['sup_details']) ? $request->post['sup_details'] : null; ?>" required></textarea>
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
            <?php echo $language->get('text_in_active'); ?>
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
        <button class="btn btn-info" id="create-supplier-submit" type="submit" name="create-supplier-submit" data-form="#create-supplier-form" data-loading-text="Saving...">
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