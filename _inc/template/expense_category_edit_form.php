<?php $language->load('expense_category'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="expense-category-form" action="expense_category.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="category_id" name="category_id" value="<?php echo $expense_category['category_id']; ?>">
  <div class="box-body">
    
      <div class="form-group">
        <label for="category_name" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_name'); ?>
        </label>
        <div class="col-sm-7">
          <input type="text" class="form-control" id="category_name" ng-model="expense_categoryName" ng-init="expense_categoryName='<?php echo $expense_category['category_name']; ?>'" value="<?php echo $expense_category['category_name']; ?>" name="category_name" required>
        </div>
      </div>

      <div class="form-group">
        <label for="category_slug" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_slug'); ?>
        </label>
        <div class="col-sm-7">
          <input type="text" class="form-control" id="category_slug" value="{{ expense_categoryName | strReplace:' ':'_' | lowercase }}" name="category_slug" required readonly>
        </div>
      </div>

      <div class="form-group">
        <label for="parent_id" class="col-sm-3 control-label">
          <?php echo $language->get('label_parent'); ?>
        </label>
        <div class="col-sm-7">
          <select class="form-control select2" name="parent_id" required>
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_expense_categorys(array('exclude' => $expense_category['category_id'])) as $the_expense_category) { ?>
                <?php if($expense_category['parent_id'] == $the_expense_category['category_id']) : ?>
                  <option value="<?php echo $the_expense_category['category_id']; ?>" selected><?php echo $the_expense_category['category_name'] ; ?></option>
                <?php else: ?>
                  <option value="<?php echo $the_expense_category['category_id']; ?>"><?php echo $the_expense_category['category_name'] ; ?></option>
                <?php endif; ?>
            <?php } ?>
         </select>
        </div>
      </div>

      <div class="form-group">
        <label for="category_details" class="col-sm-3 control-label">
          <?php echo $language->get('label_category_details'); ?>
        </label>
        <div class="col-sm-7">
          <textarea class="form-control" id="category_details" name="category_details"><?php echo $expense_category['category_details']; ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label for="status" class="col-sm-3 control-label">
          <?php echo $language->get('label_status'); ?>
        </label>
        <div class="col-sm-7">
          <select id="status" class="form-control" name="status" >
            <option <?php echo isset($expense_category['status']) && $expense_category['status'] == '1' ? 'selected' : null; ?> value="1">
              <?php echo $language->get('text_active'); ?>
            </option>
            <option <?php echo isset($expense_category['status']) && $expense_category['status'] == '0' ? 'selected' : null; ?> value="0">
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
          <input type="number" class="form-control" id="sort_order" value="<?php echo $expense_category['sort_order']; ?>" name="sort_order">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-7">
          <button id="expense-category-update" data-form="#expense-category-form" data-datatable="#category-category-list" class="btn btn-info" name="btn_edit_expense_category" data-loading-text="Updating...">
            <span class="fa fa-fw fa-pencil"></span>
            <?php echo $language->get('button_update'); ?>
          </button>
        </div>
      </div>
  </div>
</form>