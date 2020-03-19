<?php $language->load('category'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="category-form" action="category.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="category_id" name="category_id" value="<?php echo $category['category_id']; ?>">
  <div class="box-body">
    
      <div class="form-group">
        <label for="category_name" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_name'); ?>
        </label>
        <div class="col-sm-7">
          <input type="text" class="form-control" id="category_name" ng-model="categoryName" ng-init="categoryName='<?php echo $category['category_name']; ?>'" value="<?php echo $category['category_name']; ?>" name="category_name" required>
        </div>
      </div>

      <div class="form-group">
        <label for="category_slug" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_slug'); ?>
        </label>
        <div class="col-sm-7">
          <input type="text" class="form-control" id="category_slug" value="{{ categoryName | strReplace:' ':'_' | lowercase }}" name="category_slug" required readonly>
        </div>
      </div>

      <div class="form-group">
        <label for="parent_id" class="col-sm-3 control-label">
          <?php echo $language->get('label_parent'); ?>
        </label>
        <div class="col-sm-7">
          <select class="form-control select2" name="parent_id" required>
            <option value="0">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_categorys(array('exclude' => $category['category_id'])) as $the_category) { ?>
                <?php if($category['parent_id'] == $the_category['category_id']) : ?>
                  <option value="<?php echo $the_category['category_id']; ?>" selected><?php echo $the_category['category_name'] ; ?></option>
                <?php else: ?>
                  <option value="<?php echo $the_category['category_id']; ?>"><?php echo $the_category['category_name'] ; ?></option>
                <?php endif; ?>
            <?php } ?>
         </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label">
          <?php echo $language->get('label_store'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-7 store-selector">
          <div class="checkbox selector">
            <label>
              <input type="checkbox" onclick="$('input[name*=\'category_store\']').prop('checked', this.checked);"> Select / Deselect
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
                    <input type="checkbox" name="category_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo in_array($the_store['store_id'], $category['stores']) ? 'checked' : null; ?>>
                    <?php echo $the_store['name']; ?>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="category_details" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_details'); ?>
        </label>
        <div class="col-sm-7">
          <textarea class="form-control" id="category_details" name="category_details"><?php echo $category['category_details']; ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label for="status" class="col-sm-3 control-label">
          <?php echo $language->get('label_status'); ?>
        </label>
        <div class="col-sm-7">
          <select id="status" class="form-control" name="status" >
            <option <?php echo isset($category['status']) && $category['status'] == '1' ? 'selected' : null; ?> value="1">
              <?php echo $language->get('text_active'); ?>
            </option>
            <option <?php echo isset($category['status']) && $category['status'] == '0' ? 'selected' : null; ?> value="0">
              <?php echo $language->get('text_inactive'); ?>
            </option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="sort_order" class="col-sm-3 control-label">
          <?php echo $language->get('label_sort_order'); ?>
        </label>
        <div class="col-sm-7">
          <input type="number" class="form-control" id="sort_order" value="<?php echo $category['sort_order']; ?>" name="sort_order">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-7">
          <button id="category-update" data-form="#category-form" data-datatable="#category-category-list" class="btn btn-info" name="btn_edit_category" data-loading-text="Updating...">
            <span class="fa fa-fw fa-pencil"></span>
            <?php echo $language->get('button_update'); ?>
          </button>
        </div>
      </div>

  </div>
</form>