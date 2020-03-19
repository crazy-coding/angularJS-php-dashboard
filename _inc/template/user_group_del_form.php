<?php $language->load('usergroup'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="user-group-del-form" action="user_group.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="group_id" name="group_id" value="<?php echo $usergroup['group_id']; ?>">
 
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>

  <div class="box-body">

    <div class="form-group">
      <label for="insert_to" class="col-sm-6 control-label">
        <?php echo $language->get('label_insert_all_user_to'); ?>
      </label>
      <div class="col-sm-6">
        <div class="radio">
          <input type="radio" id="insert_to" value="insert_to" name="delete_action" checked>
          <select name="new_group_id" class="form-control">
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_usergroups() as $the_group) : ?>
                <?php if($the_group['group_id'] == $usergroup['group_id']) continue; ?>
                <option value="<?php echo $the_group['group_id']; ?>">
                  <?php echo $the_group['name']; ?>
                </option>
            <?php endforeach; ?>
          </select> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-12 text-center">
        <button id="user-group-delete" data-form="#user-group-del-form" data-datatable="#user-group-list" class="btn btn-danger" name="btn_edit_user-group" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span> DELETE
        </button>
      </div>
    </div>

  </div>
</form>