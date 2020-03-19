<?php $language->load('pmethod'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="pmethod-del-form" action="pmethod.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="pmethod_id" name="pmethod_id" value="<?php echo $pmethod['pmethod_id']; ?>">
  
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>

  <div class="box-body">
    
    <div class="form-group">
      <label for="insert_to" class="col-sm-4 control-label">
        <?php echo $language->get('label_invoice_to'); ?>
      </label>
      <div class="col-sm-6">
        <div class="radio">
            <input type="radio" id="insert_to" value="insert_to" name="delete_action" checked="checked">
            <select name="new_pmethod_id" class="form-control">
                <option value="">
                  <?php echo $language->get('text_select'); ?>
                 </option>
              <?php foreach (get_pmethods() as $the_pmethod) : ?>
                <?php if($the_pmethod['pmethod_id'] == $pmethod['pmethod_id']) continue ?>
                <option value="<?php echo $the_pmethod['pmethod_id']; ?>"> 
                  <?php echo $the_pmethod['name']; ?>
                </option>
              <?php endforeach; ?>
            </select> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="pmethod-delete" data-form="#pmethod-del-form" data-datatable="#pmethod-pmethod-list" class="btn btn-danger" name="btn_edit_box" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>