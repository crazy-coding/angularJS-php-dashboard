<?php $language->load('category'); ?>

<form id="create-category-form" class="form form-horizontal" action="category.php" method="post" enctype="multipart/form-data">
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  <div class="box-body">

      <div class="form-group">
        <label for="category_name" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_name'); ?>
        </label>
        <div class="col-sm-6">
          <input ng-model="categoryName" type="text" class="form-control" id="category_name" value="<?php echo isset($request->post['category_name']) ? $request->post['category_name'] : null; ?>" name="category_name" required>
        </div>
      </div>

      <div class="form-group">
        <label for="category_slug" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_slug'); ?>
        </label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="category_slug" value="<?php echo isset($request->post['category_slug']) ? $request->post['category_slug'] : "{{ categoryName | strReplace:' ':'_' | lowercase }}"; ?>" name="category_slug" required readonly>
        </div>
      </div>

      <div class="form-group">
        <label for="parent_id" class="col-sm-3 control-label">
          <?php echo $language->get('label_parent'); ?>
        </label>
        <div class="col-sm-6">
          <select class="form-control select2" name="parent_id">
            <option value="0">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_categorys() as $the_category) { ?>
              <option value="<?php echo $the_category['category_id']; ?>"><?php echo $the_category['category_name'] ; ?></option>
            <?php } ?>
         </select>
        </div>
      </div>

      <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6 store-selector">
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
                <input type="checkbox" name="category_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo $the_store['store_id'] == store_id() ? 'checked' : null; ?>>
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
        <div class="col-sm-6">
          <textarea class="form-control" id="category_details" name="category_details"><?php echo isset($request->post['category_details']) ? $request->post['category_details'] : null; ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label for="status" class="col-sm-3 control-label">
          <?php echo $language->get('label_status'); ?>
        </label>
        <div class="col-sm-6">
          <select id="status" class="form-control" name="status" >
            <option value="1">
              <?php echo $language->get('text_active'); ?>
            </option>
            <option value="0">
              <?php echo $language->get('text_inactive'); ?>
            </option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="sort_order" class="col-sm-3 control-label">
          <?php echo $language->get('label_sort_order'); ?>
        </label>
        <div class="col-sm-6">
          <input type="number" class="form-control" id="sort_order" value="<?php echo isset($request->post['sort_order']) ? $request->post['sort_order'] : 0; ?>" name="sort_order">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-6">
          <button id="create-category-submit" data-form="#create-category-form" data-datatable="#category-category-list" class="btn btn-info" name="btn_edit_category" data-loading-text="Saving...">
            <span class="fa fa-fw fa-pencil"></span>
            <?php echo $language->get('button_save'); ?>
          </button>
          <button type="reset" class="btn btn-danger" id="reset" name="reset"><span class="fa fa-fw fa-circle-o"></span>
            <?php echo $language->get('button_reset'); ?>
          </button>
        </div>
      </div>

  </div>
  <!-- end .box-body -->
</form>