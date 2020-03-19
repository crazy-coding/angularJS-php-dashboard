<?php $language->load('accounting'); ?>

<form id="form-expense" class="form-horizontal" action="expense.php" method="post" enctype="multipart/form-data">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <?php $invoice_id = isset($invoice['invoice_id']) ? $invoice['invoice_id'] : null; ?>
  <input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo $invoice_id; ?>">
  
  <div class="box-body">
    
    <div class="form-group">
      <label class="col-sm-2">&nbsp;</label>
      <div class="col-sm-5">
        <label for="date" class="control-label">
          <?php echo $language->get('label_date'); ?>
        </label>
        <br>
        <input type="date" class="form-control" id="date" name="date" value="<?php echo isset($invoice['buy_date']) ? $invoice['buy_date'] : date('Y-m-d'); ?>">
      </div>
      <div class="col-sm-5">
        <label for="time" class="control-label">
          <?php echo $language->get('label_time'); ?>
        </label><br>
        <div class="input-group bootstrap-timepicker timepicker">
            <input type="text" class="form-control input-small showtimepicker" id="time" name="time" value="<?php echo isset($invoice['expense_time']) ? to_am_pm($invoice['expense_time']) : to_am_pm(current_time()); ?>">
        </div>
      </div>
    </div>

    <?php foreach ($invoice_items as $item) : ?>

      <div class="form-group">
        <label for="title" class="col-sm-2 control-label">
          <?php echo $language->get('label_title'); ?>
        </label>
        <div class="col-sm-10">
          <input type="text" id="title" class="form-control" name="title" value="<?php echo $item['item_name']; ?>" autocomplete="off">
        </div>
      </div>

      <div class="form-group">
        <label for="price" class="col-sm-2 control-label">
          <?php echo $language->get('label_price'); ?>
        </label>
        <div class="col-sm-10">
          <input type="number" id="price" class="form-control" name="price" value="<?php echo $item['item_buying_price']; ?>" onclick="this.select();">
        </div>
      </div>

      <div class="form-group">
        <label for="expense_note" class="col-sm-2 control-label">
          <?php echo $language->get('label_details'); ?>
        </label>
        <div class="col-sm-10">
          <textarea name="expense_note" id="expense_note" class="form-control"><?php echo $invoice['invoice_note']; ?></textarea>
        </div>
      </div>

    <?php endforeach; ?>

    <div class="form-group">
      <label class="col-sm-2 control-label"></label>
      <div class="col-sm-9">            
        <button id="edit-invoice-update" class="btn btn-info" data-form="#form-expense" data-datatable="#invoice-invoice-list" name="submit" data-loading-text="Updating...">
          <i class="fa fa-fw fa-pencil"></i>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>
    
  </div>
</form>