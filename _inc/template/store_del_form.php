<?php $language->load('store'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="store-del-form" action="store.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="store_id" name="store_id" value="<?php echo $store_info['store_id']; ?>">
  
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>

  <div class="box-body">

    <div class="form-group">
      <label for="insert_to" class="col-sm-4 control-label">
        <?php echo $language->get('label_delete_all'); ?>
      </label>
      <div class="col-sm-6">
        <div class="radio">
          <input type="radio" id="delete" value="delete" name="delete_action">&nbsp;  
        </div>
      </div>
    </div>
    
    <div class="form-group">
      <label for="insert_to" class="col-sm-4 control-label">
        <?php echo $language->get('label_insert_store_to'); ?>
      </label>
      <div class="col-sm-6">
        <div class="radio">
          <input type="radio" id="insert_to" value="insert_to" name="delete_action" checked="checked">
          <select name="new_store_id" class="form-control">
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_stores() as $the_store) : ?>
              <?php if($the_store['store_id'] == $store_info['store_id']) continue ?>
              <option value="<?php echo $the_store['store_id']; ?>">
                <?php echo $the_store['name']; ?>
              </option>
            <?php endforeach; ?>
          </select> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="store-delete" data-form="#store-del-form" data-datatable="#store-store-list" class="btn btn-danger" name="btn_edit_box" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>