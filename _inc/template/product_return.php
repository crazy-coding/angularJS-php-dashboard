<h4 class="sub-title">
  <?php echo $language->get('text_return_title'); ?>
</h4>

<form id="product-return-form" class="form-horizontal" action="product_return.php" method="post">
  
  <input type="hidden" name="p_id" value="<?php echo $p_id; ?>">
  
  <div class="box-body">
    
    <div class="form-group">
      <label for="invoice_id" class="col-sm-4 control-label">
        <?php echo $language->get('label_invoice_id'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" id="invoice_id" name="invoice_id" required>
      </div>
    </div>

    <div class="form-group">
      <label for="quantity" class="col-sm-4 control-label">
        <?php echo $language->get('label_quantity'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6">
        <input type="number" class="form-control" id="quantity" name="quantity" onKeyUp="if(this.value<0){this.value='1';}" required>
      </div>
    </div>  

    <div class="form-group">
      <label class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="save-product-return" data-form="#product-return-form" data-datatable="#product-product-list" type="submit" class="btn btn-info" name="btn_product_return" data-loading-text="Updating...">
          <span class="fa fa-fw fa-save"></span>
          <?php echo $language->get('button_return'); ?>
        </button>
      </div>
    </div>    
                         
  </div>
</form>