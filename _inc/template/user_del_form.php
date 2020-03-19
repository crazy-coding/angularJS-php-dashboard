<?php $language->load('user'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="user-del-form" action="user.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="id" name="id" value="<?php echo $the_user['id']; ?>">
  
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>

  <div class="box-body">

    <div class="form-group">
      <label for="insert_to" class="col-sm-4 control-label">
        <?php echo $language->get('label_insert_content_to'); ?>
      </label>
      <div class="col-sm-6">
        <div class="radio">
            <input type="radio" id="insert_to" value="insert_to" name="delete_action" checked>
            <select name="new_user_id" class="form-control">
              <option value="">
                <?php echo $language->get('text_select'); ?>
              </option>
              <?php foreach (get_users() as $get_user) : ?>
                <?php if($get_user['id'] == $the_user['id']) continue; ?>
                <option value="<?php echo $get_user['id']; ?>">
                  <?php echo $get_user['username']; ?>
                </option>
              <?php endforeach; ?>
            </select> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="user-delete" data-form="#user-del-form" data-datatable="#user-user-list" class="btn btn-danger" name="btn_edit_user" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>