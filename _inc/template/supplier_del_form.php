<?php $language->load('supplier'); ?>
<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>
<form class="form-horizontal" id="supplier-del-form" action="supplier.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="sup_id" name="sup_id" value="<?php echo $supplier['sup_id']; ?>">
  
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>

  <div class="box-body">
    
    <div class="form-group">
      <label for="insert_to" class="col-sm-4 control-label">
        <?php echo $language->get('label_insert_product_to'); ?>
       </label>
      <div class="col-sm-6">
        <div class="radio">
          <input type="radio" id="insert_to" value="insert_to" name="delete_action" checked="checked">
          <select name="new_sup_id" class="form-control">
              <option value="">
                <?php echo $language->get('text_select'); ?>
               </option>
            <?php foreach (get_suppliers() as $the_supplier) : ?>
              <?php if($the_supplier['sup_id'] == $supplier['sup_id']) continue ?>
              <option value="<?php echo $the_supplier['sup_id']; ?>">
                <?php echo $the_supplier['sup_name']; ?>
               </option>
            <?php endforeach; ?>
          </select> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="supplier-delete" data-form="#supplier-del-form" data-datatable="#supplier-supplier-list" class="btn btn-danger" name="btn_edit_supplier" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>