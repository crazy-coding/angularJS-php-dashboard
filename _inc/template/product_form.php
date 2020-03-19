<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form id="product-update-form" class="form-horizontal" action="product.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="p_id" name="p_id" value="<?php echo $product['p_id']; ?>">
  
  <div class="box-body">
    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_image'),null); ?>
      </label>
      <div class="col-sm-2">
        <div class="preview-thumbnail">
          <a ng-click="POSFilemanagerModal({target:'product_image',thumb:'product_thumbnail'})" onClick="return false;" href="" data-toggle="image" id="product_thumbnail">
            <?php if (isset($product['p_image']) && ((FILEMANAGERPATH && is_file(FILEMANAGERPATH.$product['p_image']) && file_exists(FILEMANAGERPATH.$product['p_image'])) || (is_file(DIR_STORAGE . 'products' . $product['p_image']) && file_exists(DIR_STORAGE . 'products' . $product['p_image'])))) : ?>
              <img  src="<?php echo FILEMANAGERURL ? FILEMANAGERURL : root_url().'/storage/products'; ?>/<?php echo $product['p_image']; ?>">
            <?php else : ?>
              <img src="../assets/itsolution24/img/noimage.jpg">
            <?php endif; ?>
          </a>
          <input type="hidden" name="p_image" id="product_image" value="<?php echo isset($product['p_image']) ? $product['p_image'] : null; ?>">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="p_name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'),null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="text" class="form-control" id="p_name" name="p_name" value="<?php echo $product['p_name']; ?>" required>
      </div>
    </div>

    <div class="form-group all">
      <label for="p_code" class="col-sm-3 control-label">
        <?php echo $language->get('label_pcode'); ?> <i class="required">*</i>
      </label>             
      <div class="col-sm-8">           
        <input type="text" name="p_code" value="<?php echo $product['p_code']; ?>" class="form-control" id="xp_code" required>
      </div>
    </div>

    <div class="form-group">
      <label for="category_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_category'); ?>
      </label>
      <div class="col-sm-8">
        <select class="form-control select2" name="category_id" required>
          <option value="">
            <?php echo $language->get('text_select'); ?>
          </option>
          <?php foreach (get_category_tree(array('filter_fetch_all' => true)) as $category_id => $category_name) { ?>
              <?php if($product['category_id'] == $category_id) : ?>
                <option value="<?php echo $category_id; ?>" selected><?php echo $category_name ; ?></option>
              <?php else: ?>
                <option value="<?php echo $category_id; ?>"><?php echo $category_name ; ?></option>
              <?php endif; ?>
          <?php } ?>
       </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sup_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_supplier'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select class="form-control" name="sup_id" required>
          <option value="">
            <?php echo $language->get('text_select'); ?>
          </option>
          <?php foreach(get_suppliers() as $supplier) {
              if($supplier['sup_id'] == $product['sup_id']) { ?>
                <option value="<?php echo $supplier['sup_id']; ?>" selected>
                  <?php echo $supplier['sup_name']; ?>
                </option>
              <?php
              } else { ?>
                <option value="<?php echo $supplier['sup_id']; ?>">
                  <?php echo $supplier['sup_name']; ?>
                </option>
              <?php
              }
            }
          ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sell_price" class="col-sm-3 control-label">
        <?php echo $language->get('label_selling_price'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" step="0.01" class="form-control" id="sell_price" value="<?php echo $product['sell_price']; ?>" name="sell_price" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onKeyUp="if(this.value<0){this.value='1';}" required>
      </div>
    </div>

    <div class="form-group">
      <label for="e_date" class="col-sm-3 control-label">
        <?php echo $language->get('label_expired_date'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="date" class="form-control date" id="e_date" value="<?php echo $product['e_date']; ?>" name="e_date" autocomplete="off" required>
      </div>
    </div>

    <div class="form-group">
      <label for="alert_quantity" class="col-sm-3 control-label">
        <?php echo $language->get('label_alert_quantity'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="alert_quantity" name="alert_quantity" value="<?php echo $product['alert_quantity']; ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label for="unit_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_unit'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select class="form-control" name="unit_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach(get_units() as $unit_row) {
                if($unit_row['unit_id'] == $product['unit_id']) { ?>
                  <option value="<?php echo $unit_row['unit_id']; ?>" selected><?php echo $unit_row['unit_name']; ?></option><?php
                } else {
                  ?>
                  <option value="<?php echo $unit_row['unit_id']; ?>">
                    <?php echo $unit_row['unit_name']; ?>
                  </option>
                <?php
                }
              }
            ?>
        </select>
      </div>
    </div>

    <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
    <div class="form-group all">
      <label for="hsn_code" class="col-sm-3 control-label">
        <?php echo $language->get('label_hsn_code'); ?>
      </label>             
      <div class="col-sm-8">           
        <input type="text" name="hsn_code" id="hsn_code" class="form-control" value="<?php echo $product['hsn_code']; ?>" autocomplete="off" required>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="taxrate_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_product_tax'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select class="form-control" name="taxrate_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach(get_taxrates() as $taxrate_row) {
                if($taxrate_row['taxrate_id'] == $product['taxrate_id']) { ?>
                  <option value="<?php echo $taxrate_row['taxrate_id']; ?>" selected><?php echo $taxrate_row['taxrate_name']; ?></option><?php
                } else {
                  ?>
                  <option value="<?php echo $taxrate_row['taxrate_id']; ?>">
                    <?php echo $taxrate_row['taxrate_name']; ?>
                  </option>
                <?php
                }
              }
            ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="tax_method" class="col-sm-3 control-label">
        <?php echo $language->get('label_tax_method'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select id="tax_method" class="form-control" name="tax_method" >
          <option <?php echo isset($product['tax_method']) && $product['tax_method'] == 'inclusive' ? 'selected' : null; ?> value="inclusive">
            <?php echo $language->get('text_inclusive'); ?>
          </option>
          <option <?php echo isset($product['tax_method']) && $product['tax_method'] == 'exclusive' ? 'selected' : null; ?> value="exclusive">
            <?php echo $language->get('text_exclusive'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="box_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_box'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select class="form-control" name="box_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach(get_boxes() as $box_row) {
                if($box_row['box_id'] == $product['box_id']) { ?>
                  <option value="<?php echo $box_row['box_id']; ?>" selected><?php echo $box_row['box_name']; ?></option><?php
                } else {
                  ?>
                  <option value="<?php echo $box_row['box_id']; ?>">
                    <?php echo $box_row['box_name']; ?>
                  </option>
                <?php
                }
              }
            ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8 store-selector">
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
                  <input type="checkbox" name="product_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo in_array($the_store['store_id'], $product['stores']) ? 'checked' : null; ?>>
                  <?php echo $the_store['name']; ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="e_date" class="col-sm-3 control-label">
        <?php echo $language->get('label_description'); ?>
      </label>
      <div class="col-sm-8">
        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $product['description']; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label for="status" class="col-sm-3 control-label">
        <?php echo $language->get('label_status'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select id="status" class="form-control" name="status" >
          <option <?php echo isset($product['status']) && $product['status'] == '1' ? 'selected' : null; ?> value="1">
            <?php echo $language->get('text_active'); ?>
          </option>
          <option <?php echo isset($product['status']) && $product['status'] == '0' ? 'selected' : null; ?> value="0">
            <?php echo $language->get('text_inactive'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_sort_order'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="sort_order" value="<?php echo $product['sort_order']; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-8">
        <button class="btn btn-info" id="product-update-submit" name="form_update" data-form="#product-update-form" data-loading-text="Updating...">
          <i class="fa fa-fw fa-pencil"></i> 
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>
  </div>
</form>