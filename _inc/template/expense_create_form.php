<?php $language->load('expense'); ?>

<form id="form-expense" class="form-horizontal" action="expense.php" method="post" enctype="multipart/form-data">
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  <input type="hidden" id="sup_id" name="sup_id" value="1">
  <div class="box-body">
    <div class="form-group">
      <div class="col-sm-3 col-sm-offset-2">
        <label for="reference_no" class="control-label">
            <?php echo $language->get('label_reference_no'); ?>
        </label><br>
        <input type="text" class="form-control" id="reference_no" value="" name="reference_no" autofocus autocomplete="off">
      </div>
      <div class="col-sm-3">
        <label for="date" class="control-label">
          <?php echo $language->get('label_date'); ?><i class="required">*</i>
        </label><br>
        <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
      </div>
      <div class="col-sm-3">
        <label for="time" class="control-label">
          <?php echo $language->get('label_time'); ?><i class="required">*</i>
        </label><br>
        <div class="input-group bootstrap-timepicker timepicker">
            <input type="text" class="form-control input-small showtimepicker" id="time" name="time" value="<?php echo to_am_pm(current_time()); ?>">
        </div>
      </div>
    </div>

    <div class="form-group">
        <label for="category_id" class="col-sm-2 control-label">
          <?php echo $language->get('label_category'); ?>
        </label>
        <div class="col-sm-9">
          <select class="form-control select2" name="category_id">
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_expense_categorys() as $the_expense_category) { ?>
              <option value="<?php echo $the_expense_category['category_id']; ?>"><?php echo $the_expense_category['category_name'] ; ?></option>
            <?php } ?>
         </select>
        </div>
      </div>

    <div class="form-group">
      <label for="title" class="col-sm-2 control-label">
        <?php echo $language->get('label_title'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-9">
        <input type="text" id="title" class="form-control" name="title">
      </div>
    </div>

    <div class="form-group">
      <label for="amount" class="col-sm-2 control-label">
        <?php echo $language->get('label_amount'); ?><i class="required">*</i>
       </label>
      <div class="col-sm-9">
        <input type="number" id="amount" class="form-control" name="amount" onclick="this.select();">
      </div>
    </div>

    <div class="form-group">
      <label for="note" class="col-sm-2 control-label">
        <?php echo $language->get('label_notes'); ?>
      </label>
      <div class="col-sm-9">
        <textarea name="note" id="note" class="form-control"></textarea>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-2 control-label"></label>
      <div class="col-sm-9">            
        <button id="create-expense-submit" class="btn btn-info" data-form="#form-expense" data-datatable="#expense-expense-list" name="submit" data-loading-text="Saving...">
          <i class="fa fa-fw fa-pencil"></i>
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