<?php $language->load('taxrate'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="taxrate-form" action="taxrate.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="taxrate_id" name="taxrate_id" value="<?php echo $taxrate['taxrate_id']; ?>">

  <div class="box-body">
    
    <div class="form-group">
      <label for="taxrate_name" class="col-sm-3 control-label">
        <?php echo $language->get('label_taxrate_name'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="text" class="form-control" id="taxrate_name" value="<?php echo $taxrate['taxrate_name']; ?>" name="taxrate_name">
      </div>
    </div>

    <div class="form-group">
      <label for="taxrate_code" class="col-sm-3 control-label">
        <?php echo $language->get('label_taxrate_code'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="text" class="form-control" id="taxrate_code" value="<?php echo $taxrate['taxrate_code']; ?>" name="taxrate_code">
      </div>
    </div>

    <div class="form-group">
      <label for="taxrate" class="col-sm-3 control-label">
        <?php echo $language->get('label_taxrate'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="taxrate" value="<?php echo $taxrate['taxrate']; ?>" name="taxrate">
      </div>
    </div>

    <div class="form-group">
      <label for="status" class="col-sm-3 control-label">
        <?php echo $language->get('label_status'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select id="status" class="form-control" name="status" >
          <option <?php echo isset($taxrate['status']) && $taxrate['status'] == '1' ? 'selected' : null; ?> value="1">
            <?php echo $language->get('text_active'); ?>
           </option>
          <option <?php echo isset($taxrate['status']) && $taxrate['status'] == '0' ? 'selected' : null; ?> value="0">
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
        <input type="number" class="form-control" id="sort_order" value="<?php echo $taxrate['sort_order']; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label for="taxrate_address" class="col-sm-3 control-label"></label>
      <div class="col-sm-8">            
        <button id="taxrate-update" class="btn btn-info"  data-form="#taxrate-form" data-datatable="#taxrate-taxrate-list" name="btn_edit_customer" data-loading-text="Updating...">
          <i class="fa fa-fw fa-pencil"></i>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>

  </div>
</form>