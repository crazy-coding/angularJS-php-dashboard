<?php $language->load('unit'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="unit-form" action="unit.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="unit_id" name="unit_id" value="<?php echo $unit['unit_id']; ?>">

  <div class="box-body">
    
    <div class="form-group">
      <label for="unit_name" class="col-sm-3 control-label">
        <?php echo $language->get('label_unit_name'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="text" class="form-control" id="unit_name" value="<?php echo $unit['unit_name']; ?>" name="unit_name">
      </div>
    </div>

    <div class="form-group">
      <label for="unit_details" class="col-sm-3 control-label">
        <?php echo $language->get('label_unit_details'); ?>
      </label>
      <div class="col-sm-8">
        <textarea class="form-control" id="unit_details" name="unit_details" rows="3"><?php echo $unit['unit_details']; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8 store-selector">
        <div class="checkbox selector">
          <label>
            <input type="checkbox" onclick="$('input[name*=\'unit_store\']').prop('checked', this.checked);"> Select / Deselect
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
                  <input type="checkbox" name="unit_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo in_array($the_store['store_id'], $unit['stores']) ? 'checked' : null; ?>>
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
          <option <?php echo isset($unit['status']) && $unit['status'] == '1' ? 'selected' : null; ?> value="1">
            <?php echo $language->get('text_active'); ?>
           </option>
          <option <?php echo isset($unit['status']) && $unit['status'] == '0' ? 'selected' : null; ?> value="0">
            <?php echo $language->get('text_in_active'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_sort_order'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="sort_order" value="<?php echo $unit['sort_order']; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label for="unit_address" class="col-sm-3 control-label"></label>
      <div class="col-sm-8">            
        <button id="unit-update" class="btn btn-info"  data-form="#unit-form" data-datatable="#unit-unit-list" name="btn_edit_customer" data-loading-text="Updating...">
          <i class="fa fa-fw fa-pencil"></i>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>

  </div>
</form>