<?php $language->load('usergroup'); ?>
<form id="create-usergroup-form" class="form-horizontal" action="user_group.php" method="post" enctype="multipart/form-data">
  
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="name" ng-model="usergroupName" value="<?php echo isset($request->post['name']) ? $request->post['name'] : null; ?>" name="name" required>
      </div>
    </div>

    <div class="form-group">
      <label for="slug" class="col-sm-3 control-label">
        <?php echo $language->get('label_slug'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="slug" value="<?php echo isset($request->post['slug']) ? $request->post['slug'] : "{{ usergroupName | strReplace:' ':'_' | lowercase }}"; ?>" name="slug" required readonly>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-7">
        <button class="btn btn-info" id="create-usergroup-submit" type="submit" name="create-usergroup-submit" data-form="#create-usergroup-form" data-loading-text="Saving...">
          <span class="fa fa-fw fa-save"></span>
          <?php echo $language->get('button_save'); ?>
        </button>
        <button type="reset" class="btn btn-danger" id="reset" name="reset">
          <span class="fa fa-fw fa-circle-o"></span> 
          <?php echo $language->get('button_reset'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>