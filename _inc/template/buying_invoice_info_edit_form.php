<?php $language->load('buy'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="invoice-form" action="purchase.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="UPDATEINVOICEINFO">
  <input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo $invoice['invoice_id']; ?>">
  <div class="box-body">
      <div class="form-group">
        <label for="invoice_note" class="col-sm-3 control-label">
          <?php echo $language->get('label_invoice_note'); ?>
        </label>
        <div class="col-sm-7">
          <textarea class="form-control" id="invoice_note" name="invoice_note"><?php echo $invoice['invoice_note']; ?></textarea>
        </div>
      </div>
      <div class="form-group">
        <label for="status" class="col-sm-3 control-label">
          <?php echo $language->get('label_status'); ?>
        </label>
        <div class="col-sm-7">
          <select id="status" class="form-control" name="status" >
            <option <?php echo isset($invoice['status']) && $invoice['status'] == '1' ? 'selected' : null; ?> value="1">
              <?php echo $language->get('text_active'); ?>
            </option>
            <option <?php echo isset($invoice['status']) && $invoice['status'] == '0' ? 'selected' : null; ?> value="0">
              <?php echo $language->get('text_inactive'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-7">
          <button id="invoice-update" data-form="#invoice-form" data-datatable="#invoice-invoice-list" class="btn btn-info" name="btn_edit_invoice" data-loading-text="Updating...">
            <span class="fa fa-fw fa-pencil"></span>
            <?php echo $language->get('button_update'); ?>
          </button>
        </div>
      </div>
  </div>
</form>