<?php $language->load('printer'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete'); ?>
</h4>

<form class="form-horizontal" id="printer-del-form" action="printer.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="printer_id" name="printer_id" value="<?php echo $printer['printer_id']; ?>">
    <div class="box-body">
    <div class="form-group">
      <div class="col-sm-12 text-center">
        <button id="printer-delete" data-form="#printer-del-form" data-datatable="#printer-printer-list" class="btn btn-danger" name="btn_edit_printer" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
  </div>
</form>