<?php $language->load('customer'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="customer-del-form" action="customer.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="customer_id" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
  
  <h4 class="box-title text-center">
    <?php echo $language->get('text_delete_instruction'); ?>
  </h4>
  <div class="box-body">

    <div class="form-group">
      <label for="insert_to" class="col-sm-6 control-label">
        <?php echo $language->get('label_insert_invoice_to'); ?>
      </label>
      <div class="col-sm-6">
        <div class="radio">
          <input type="radio" id="insert_to" value="insert_to" name="delete_action" checked="checked">
          <select name="new_customer_id" class="form-control">
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_customers() as $the_customer) : ?>
              <?php if($the_customer['customer_id'] == $customer['customer_id']) continue ?>
              <option value="<?php echo $the_customer['customer_id']; ?>">
                <?php echo $the_customer['customer_name']; ?>
              </option>
            <?php endforeach; ?>
          </select> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-12 text-center">
        <button id="customer-delete" data-form="#customer-del-form" data-datatable="#customer-customer-list" class="btn btn-danger" name="btn_edit_customer" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>