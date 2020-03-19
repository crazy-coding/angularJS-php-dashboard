<?php $language->load('user'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="user-form" action="user.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="id" name="id" value="<?php echo $the_user['id']; ?>">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="username" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="username" value="<?php echo $the_user['username']; ?>" name="username" required>
      </div>
    </div>

    <div class="form-group">
      <label for="email" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_email'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="email" class="form-control" id="email" value="<?php echo $the_user['email']; ?>" name="email">
      </div>
    </div>

    <div class="form-group">
      <label for="mobile" class="col-sm-4 control-label">
        <?php echo $language->get('label_mobile'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="number" class="form-control" id="mobile" value="<?php echo $the_user['mobile']; ?>" name="mobile">
      </div>
    </div>

    <div class="form-group">
      <label for="group_id" class="col-sm-4 control-label">
        <?php echo $language->get('label_group'); ?><i class="required">*</i>
        <span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_group'); ?>"></span>    
      </label>
      <div class="col-sm-6">
        <select class="form-control" name="group_id" required>
          <option value="">
            <?php echo $language->get('text_select'); ?>
          </option>
          <?php foreach (get_usergroups() as $group) { ?>
              <?php if($group['group_id'] == $the_user['group_id']) : ?>
                <option value="<?php echo $group['group_id']; ?>" selected><?php echo $group['name'] ; ?></option>
              <?php else: ?>
                <option value="<?php echo $group['group_id']; ?>"><?php echo $group['name'] ; ?></option>
              <?php endif; ?>
          <?php } ?>
       </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-4 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6 store-selector">
        <div class="checkbox selector">
          <label>
            <input type="checkbox" onclick="$('input[name*=\'user_store\']').prop('checked', this.checked);"> Select / Deselect
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
                  <input type="checkbox" name="user_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo in_array($the_store['store_id'], $the_user['stores']) ? 'checked' : null; ?>>
                  <?php echo $the_store['name']; ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="status" class="col-sm-4 control-label">
        <?php echo $language->get('label_status'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <select id="status" class="form-control" name="status" >
          <option <?php echo isset($the_user['status']) && $the_user['status'] == '1' ? 'selected' : null; ?> value="1">
            <?php echo $language->get('text_active'); ?>
          </option>
          <option <?php echo isset($the_user['status']) && $the_user['status'] == '0' ? 'selected' : null; ?> value="0">
            <?php echo $language->get('text_inactive'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-4 control-label">
        <?php echo sprintf($language->get('label_sort_order'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="number" class="form-control" id="sort_order" value="<?php echo $the_user['sort_order']; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label for="user_address" class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="user-update" data-form="#user-form" data-datatable="#user-user-list" class="btn btn-info" name="btn_edit_user" data-loading-text="Updating...">
          <span class="fa fa-fw fa-pencil"></span> 
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>