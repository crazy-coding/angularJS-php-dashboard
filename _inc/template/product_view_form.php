<div class="form-horizontal">
  <div class="box-body">
    
    <div class="form-group">
      <label for="p_image" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_image'),null); ?>
      </label>
      <div class="col-sm-1">
        <div class="preview-thumbnail">
          <a onClick="return false;" id="p_thumb" href="#">
            <?php if (isset($product['p_image']) && ((FILEMANAGERPATH && is_file(FILEMANAGERPATH.$product['p_image']) && file_exists(FILEMANAGERPATH.$product['p_image'])) || (is_file(DIR_STORAGE . 'products' . $product['p_image']) && file_exists(DIR_STORAGE . 'products' . $product['p_image'])))) : ?>
              <img  src="<?php echo FILEMANAGERURL ? FILEMANAGERURL : root_url().'/storage/products'; ?>/<?php echo $product['p_image']; ?>">
            <?php else : ?>
              <img src="../assets/itsolution24/img/noimage.jpg">
            <?php endif; ?>
          </a>
        </div>

      </div>
    </div>

    <div class="form-group">
      <label for="p_name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'),null); ?>
      </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="p_name" value="<?php echo $product['p_name']; ?>" name="p_name" readonly>
      </div>
    </div>

    <div class="form-group all">
      <label for="p_code" class="col-sm-3 control-label">
        <?php echo $language->get('label_pcode'); ?>
      </label>             
      <div class="col-sm-6">           
        <input type="text" name="p_code" value="<?php echo $product['p_code']; ?>" class="form-control" id="xp_code" readonly>
      </div>
    </div>

    <div class="form-group">
      <label for="category_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_category'); ?>
      </label>
      <div class="col-sm-6">
        <select class="form-control select2" name="category_id" disabled>
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
        <?php echo $language->get('label_supplier'); ?>
      </label>
      <div class="col-sm-6">
        <select class="form-control" name="sup_id" readonly disabled>
          <option value="">
            <?php echo $language->get('label_select'); ?>
          </option>
          <?php foreach(get_suppliers() as $supplier) {
              if($supplier['sup_id'] == $product['sup_id']) { ?>
                <option value="<?php echo $supplier['sup_id']; ?>" selected><?php echo $supplier['sup_name']; ?></option><?php
              } else { ?>
                <option value="<?php echo $supplier['sup_id']; ?>"><?php echo $supplier['sup_name']; ?></option><?php
              }
            }
          ?>
        </select>
      </div>
    </div>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'show_buy_price')) : ?>
      <div class="form-group">
        <label for="buy_price"  class="col-sm-3 control-label">
          <?php echo $language->get('label_buy_price'); ?>
        </label>
        <div class="col-sm-6">
          <input type="number" step="0.01" class="form-control" id="buy_price" value="<?php echo $product['buy_price']; ?>" name="buy_price" readonly>
        </div>
      </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="sell_price" class="col-sm-3 control-label">
        <?php echo $language->get('label_sell_price'); ?>
      </label>
      <div class="col-sm-6">
        <input type="number" step="0.01" class="form-control" id="sell_price" value="<?php echo $product['sell_price']; ?>" name="sell_price" readonly>
      </div>
    </div>

    <div class="form-group">
      <label for="quantity_in_stock" class="col-sm-3 control-label">
        <?php echo $language->get('label_stock'); ?>
       </label>
      <div class="col-sm-6">
        <input type="number" class="form-control" id="quantity_in_stock" value="<?php echo $product['quantity_in_stock']; ?>" name="quantity_in_stock" readonly>
      </div>
    </div>  

    <div class="form-group">
      <label for="e_date" class="col-sm-3 control-label">
        <?php echo $language->get('label_expired_date'); ?>
       </label>
      <div class="col-sm-6">
        <input type="date" class="form-control" id="e_date" value="<?php echo $product['e_date']; ?>" name="e_date" readonly>
      </div>
    </div>

    <div class="form-group">
      <label for="alert_quantity" class="col-sm-3 control-label">
        <?php echo $language->get('label_alert_quantity'); ?>
      </label>
      <div class="col-sm-6">
        <input type="number" class="form-control" id="alert_quantity" name="alert_quantity" value="<?php echo $product['alert_quantity']; ?>" readonly>
      </div>
    </div>

    <div class="form-group">
      <label for="unit_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_unit'); ?>
      </label>
      <div class="col-sm-6">
        <select class="form-control" name="unit_id" readonly disabled>
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
      <div class="col-sm-6">           
        <input type="text" name="hsn_code" id="hsn_code" class="form-control" value="<?php echo $product['hsn_code']; ?>" autocomplete="off" readonly>
      </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
      <label for="taxrate_id" class="col-sm-3 control-label">
        <?php echo $language->get('label_product_tax'); ?>
      </label>
      <div class="col-sm-6">
        <select class="form-control" name="taxrate_id" readonly disabled>
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
        <?php echo $language->get('label_tax_method'); ?>
      </label>
      <div class="col-sm-6">
        <select id="tax_method" class="form-control" name="tax_method" readonly disabled>
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
        <?php echo $language->get('label_box'); ?>
      </label>
      <div class="col-sm-6">
        <select class="form-control" name="box_id" readonly disabled>
           <?php foreach(get_boxes() as $box) {
                if($box['box_id'] == $product['box_id']) { ?>
                  <option value="<?php echo $box['box_id']; ?>" selected>
                    <?php echo $box['box_name']; ?>
                  </option>
                <?php
                } else { ?>
                  <option value="<?php echo $box['box_id']; ?>">
                    <?php echo $box['box_name']; ?>
                  </option>
                  <?php
                }
              }
            ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="e_date" class="col-sm-3 control-label">
        <?php echo $language->get('label_description'); ?>
      </label>
      <div class="col-sm-6">
        <textarea class="form-control" id="description" name="description" rows="3" readonly><?php echo $product['description']; ?></textarea>
      </div>
    </div>

  </div>
</div>