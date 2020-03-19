<?php $language->load('category'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="category-del-form" action="category.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="category_id" name="category_id" value="<?php echo $category['category_id']; ?>">
  
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>
  <div class="box-body">

    <div class="form-group">
      <label for="insert_to" class="col-sm-6 control-label">
        <?php echo $language->get('label_insert_content_to'); ?>
      </label>
      <div class="col-sm-6">
        <div class="radio">
          <input type="radio" id="insert_to" value="insert_to" name="delete_action" checked="checked">
          <select name="new_category_id" class="form-control select2">
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_categorys() as $the_category) : ?>
              <?php if($the_category['category_id'] == $category['category_id']) continue ?>
              <option value="<?php echo $the_category['category_id']; ?>">
                <?php echo $the_category['category_name']; ?>
              </option>
            <?php endforeach; ?>
          </select> 
        </div>
      </div>
    </div>
    
    <br><br>

    <div class="form-group">
      <div class="col-sm-12 text-center">
        <button id="category-delete" data-form="#category-del-form" data-datatable="#category-category-list" class="btn btn-danger" name="btn_edit_category" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>