<?php $language->load('product'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="product-del-form" action="product.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="p_id" name="p_id" value="<?php echo $product['p_id']; ?>">
  
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>

  <div class="box-body">
    
    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <input type="radio" id="soft_delete" value="soft_delete" name="delete_action" checked> &nbsp;<label for="soft_delete">
          <?php echo $language->get('label_soft_delete'); ?>
        </label>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
          <input type="radio" id="delete_all" value="delete_all" name="delete_action"> &nbsp;
          <label for="delete_all">
            <?php echo $language->get('label_delete_product'); ?>
         </label>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-8 col-sm-offset-2">
        <button id="product-delete-submit" data-form="#product-del-form" data-datatable="#product-product-list" class="btn btn-danger" name="submit" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>