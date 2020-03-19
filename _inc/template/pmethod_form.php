<?php $language->load('pmethod'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="pmethod-form" action="pmethod.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="pmethod_id" name="pmethod_id" value="<?php echo $pmethod['pmethod_id']; ?>">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="pmethod_name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="text" class="form-control" id="pmethod_name" value="<?php echo $pmethod['name']; ?>" name="pmethod_name">
      </div>
    </div>

    <div class="form-group">
      <label for="pmethod_name" class="col-sm-3 control-label">
        <?php echo $language->get('label_details'); ?>
      </label>
      <div class="col-sm-8">
        <textarea class="form-control" id="pmethod_details" name="pmethod_details"><?php echo isset($pmethod['details']) ? $pmethod['details'] : null; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8 store-selector">
        <div class="checkbox selector">
          <label>
            <input type="checkbox" onclick="$('input[name*=\'pmethod_store\']').prop('checked', this.checked);"> Select / Deselect
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
                  <input type="checkbox" name="pmethod_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo in_array($the_store['store_id'], $pmethod['stores']) ? 'checked' : null; ?>>
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
      <div class="col-sm-8">
        <select id="status" class="form-control" name="status" >
          <option <?php echo isset($pmethod['status']) && $pmethod['status'] == '1' ? 'selected' : null; ?> value="1"><?php echo $language->get('text_active'); ?></option>
          <option <?php echo isset($pmethod['status']) && $pmethod['status'] == '0' ? 'selected' : null; ?> value="0"><?php echo $language->get('text_in_active'); ?></option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_sort_order'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="sort_order" value="<?php echo $pmethod['sort_order']; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label for="box_address" class="col-sm-3 control-label"></label>
      <div class="col-sm-8">            
        <button id="pmethod-update" class="btn btn-info"  data-form="#pmethod-form" data-datatable="#pmethod-pmethod-list" name="btn_edit_customer" data-loading-text="Updating...">
          <i class="fa fa-fw fa-pencil"></i>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>