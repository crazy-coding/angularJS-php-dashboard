<?php $language->load('product'); ?>

<form id="create-product-form" class="form-horizontal" action="product.php?box_state=open" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="p_image" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_image'),null); ?>
      </label>
      <div class="col-sm-7">
        <div class="preview-thumbnail">
          <a ng-click="POSFilemanagerModal({target:'p_image',thumb:'p_thumb'})" onClick="return false;" href="#" data-toggle="image" id="p_thumb">
            <img src="../assets/itsolution24/img/noimage.jpg" alt="">
          </a>
          <input type="hidden" name="p_image" id="p_image" value="">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="p_name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'),$language->get('text_product')); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="p_name" value="<?php echo isset($request->post['p_name']) ? $request->post['p_name'] : null; ?>" name="p_name" required>
      </div>
    </div>

    <div class="form-group all">
      <label for="p_code" class="col-sm-3 control-label">
        <?php echo $language->get('label_pcode'); ?> <i class="required">*</i>
      </label>             
      <div class="col-sm-7">           
        <div class="input-group">
          <input type="text" name="p_code" id="p_code" class="form-control" autocomplete="off" required>
          <span id="random_num" class="input-group-addon pointer random_num">
              <i class="fa fa-random"></i>
          </span>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="category_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_category'); ?> <i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <div class="{{ !hideSupAddBtn ? 'input-group' : null }}">
          <select id="category_id" class="form-control select2" name="category_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_category_tree(array('filter_fetch_all' => true)) as $category_id => $category_name) { ?>
              <option value="<?php echo $category_id; ?>"><?php echo $category_name ; ?></option>
            <?php } ?>
          </select>
          <a ng-hide="hideCategoryAddBtn" class="input-group-addon" ng-click="createNewCategory();" onClick="return false;" href="category.php?box_state=open">
            <i class="fa fa-plus"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_supplier'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <div class="{{ !hideSupAddBtn ? 'input-group' : null }}">
          <select id="sup_id" class="form-control" name="sup_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_suppliers() as $supplier) : ?>
              <option value="<?php echo $supplier['sup_id']; ?>">
                <?php echo $supplier['sup_name'] ; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <a ng-hide="hideSupAddBtn" class="input-group-addon" ng-click="createNewSupplier();" onClick="return false;" href="supplier.php?box_state=open">
            <i class="fa fa-plus"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="e_date" class="col-sm-3 control-label">
        <?php echo $language->get('label_expired_date'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="date" class="form-control" id="e_date" name="e_date" value="<?php echo date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 365 day"));?>" autocomplete="off" required>
      </div>
    </div>

    <div class="form-group">
      <label for="alert_quantity" class="col-sm-3 control-label">
        <?php echo $language->get('label_alert_quantity'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="number" class="form-control" id="alert_quantity" value="10" name="alert_quantity" required>
      </div>
    </div>

    <div class="form-group">
      <label for="unit_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_unit'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <div class="{{ !hideUnitAddBtn ? 'input-group' : null }}">
          <select id="unit_id" class="form-control" name="unit_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php $inc=1;foreach (get_units() as $unit_row) : ?>
              <option value="<?php echo $unit_row['unit_id']; ?>" <?php echo $inc == 1 ? 'selected' : null;?>>
                <?php echo $unit_row['unit_name'] ; ?>
              </option>
            <?php $inc++;endforeach; ?>
          </select>
          <a ng-hide="hideUnitAddBtn" class="input-group-addon" ng-click="createNewUnit();" onClick="return false;" href="unit.php?box_state=open">
            <i class="fa fa-plus"></i>
          </a>
        </div>
      </div>
    </div>

    <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
    <div class="form-group all">
      <label for="hsn_code" class="col-sm-3 control-label">
        <?php echo $language->get('label_hsn_code'); ?>
      </label>             
      <div class="col-sm-7">           
        <input type="text" name="hsn_code" id="hsn_code" class="form-control" autocomplete="off" required>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="taxrate_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_product_tax'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <div class="{{ !hideTaxrateAddBtn ? 'input-group' : null }}">
          <select id="taxrate_id" class="form-control" name="taxrate_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_taxrates() as $taxrate_row) : ?>
              <option value="<?php echo $taxrate_row['taxrate_id']; ?>" <?php echo $taxrate_row['taxrate'] == 0 ? 'selected' : null;?>>
                <?php echo $taxrate_row['taxrate_name'] ; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <a ng-hide="hideTaxrateAddBtn" class="input-group-addon" ng-click="createNewTaxrate();" onClick="return false;" href="taxrate.php?box_state=open">
            <i class="fa fa-plus"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="tax_method" class="col-sm-3 control-label">
        <?php echo $language->get('label_tax_method'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <select id="tax_method" class="form-control" name="tax_method" >
          <option value="inclusive" selected>
            <?php echo $language->get('text_inclusive'); ?>
          </option>
          <option value="exclusive">
            <?php echo $language->get('text_exclusive'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="box_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_box'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <div class="{{ !hideBoxAddBtn ? 'input-group' : null }}">
          <select id="box_id" class="form-control" name="box_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php $inc=1;foreach (get_boxes() as $box_row) : ?>
              <option value="<?php echo $box_row['box_id']; ?>" <?php echo $inc == 1 ? 'selected' : null;?>>
                <?php echo $box_row['box_name'] ; ?>
              </option>
            <?php $inc++;endforeach; ?>
          </select>
          <a ng-hide="hideBoxAddBtn" class="input-group-addon" ng-click="createNewBox();" onClick="return false;" href="box.php?box_state=open">
            <i class="fa fa-plus"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7 store-selector">
        <div class="checkbox selector">
          <label>
            <input type="checkbox" onclick="$('input[name*=\'product_store\']').prop('checked', this.checked);"> Select / Deselect
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
                  <input type="checkbox" name="product_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo $the_store['store_id'] == store_id() ? 'checked' : null; ?>>
                  <?php echo $the_store['name']; ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="description" class="col-sm-3 control-label">
        <?php echo $language->get('label_description'); ?>
      </label>
      <div class="col-sm-7">
        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($request->post['description']) ? $request->post['description'] : null; ?></textarea>
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
        <button class="btn btn-info" id="create-product-submit" type="submit" name="create-product-submit" data-form="#create-product-form" data-datatable="product-product-list" data-loading-text="Saving...">
          <span class="fa fa-fw fa-save"></span>
          <?php echo $language->get('button_save'); ?>
        </button>
        <button type="reset" class="btn btn-danger" id="reset" name="reset">
          <span class="fa fa-circle-o"></span>
         <?php echo $language->get('button_reset'); ?></button>
      </div>
    </div>
    
  </div>
</form>

<script type="text/javascript">
$(document).ready(function() {
  setTimeout(function() {
    $("#random_num").trigger("click");
  }, 1000);
})
</script>