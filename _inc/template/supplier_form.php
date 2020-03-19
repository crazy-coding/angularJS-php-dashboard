<?php $language->load('supplier'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="supplier-form" action="supplier.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="sup_id" name="sup_id" value="<?php echo $supplier['sup_id']; ?>">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="sup_name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
     </label>
      <div class="col-sm-8">
        <input type="text" class="form-control" id="sup_name" value="<?php echo $supplier['sup_name']; ?>" name="sup_name">
      </div>
    </div>

    <div class="form-group">
      <label for="sup_mobile" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_mobile'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="sup_mobile" value="<?php echo $supplier['sup_mobile']; ?>" name="sup_mobile">
      </div>
    </div>

    <div class="form-group">
      <label for="sup_email" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_email'), null); ?><i class="required">*</i>
     </label>
      <div class="col-sm-8">
        <input type="email" class="form-control" id="sup_email" value="<?php echo $supplier['sup_email']; ?>" name="sup_email">
      </div>
    </div>

    <div class="form-group">
      <label for="sup_address" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_address'), null); ?><i class="required">*</i>
     </label>
      <div class="col-sm-8">
        <textarea class="form-control" id="sup_address" name="sup_address"><?php echo $supplier['sup_address']; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_city" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_city'), null); ?>
      </label>
      <div class="col-sm-8">
        <input type="text" class="form-control" id="sup_city" value="<?php echo $supplier['sup_city']; ?>" name="sup_city">
      </div>
    </div>

    <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
    <div class="form-group">
      <label for="sup_state" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_state'), null); ?>
      </label>
      <div class="col-sm-8">
        <?php echo stateSelector($supplier['sup_state'], 'sup_state', 'sup_state'); ?>
      </div>
    </div>
    <?php else : ?>
      <div class="form-group">
        <label for="sup_state" class="col-sm-3 control-label">
          <?php echo sprintf($language->get('label_state'), null); ?>
        </label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="sup_state" value="<?php echo $supplier['sup_state']; ?>" name="sup_state">
        </div>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="country" class="col-sm-3 control-label">
        <?php echo $language->get('label_country'); ?>
      </label>
      <div class="col-sm-8">
        <?php echo countrySelector($supplier['sup_country'], 'sup_country', 'sup_country'); ?>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8 store-selector">
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
                  <input type="checkbox" name="supplier_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo in_array($the_store['store_id'], $supplier['stores']) ? 'checked' : null; ?>>
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
        <?php echo sprintf($language->get('label_details'), null); ?>
      </label>
      <div class="col-sm-8">
        <textarea class="form-control" id="sup_details" name="sup_details"><?php echo $supplier['sup_details']; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="status" class="col-sm-3 control-label">
        <?php echo $language->get('label_status'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select id="status" class="form-control" name="status" >
          <option <?php echo isset($supplier['status']) && $supplier['status'] == '1' ? 'selected' : null; ?> value="1"><?php echo $language->get('text_active'); ?></option>
          <option <?php echo isset($supplier['status']) && $supplier['status'] == '0' ? 'selected' : null; ?> value="0"><?php echo $language->get('text_in_active'); ?></option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_sort_order'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="sort_order" value="<?php echo $supplier['sort_order']; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label for="supplier_address" class="col-sm-3 control-label"></label>
      <div class="col-sm-8">
        <button id="supplier-update" data-form="#supplier-form" data-datatable="#supplier-supplier-list" class="btn btn-info" name="btn_edit_supplier" data-loading-text="Updating...">
          <span class="fa fa-fw fa-pencil"></span>
          <?php echo sprintf($language->get('button_update'), null); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>