<?php $language->load('expense'); ?>

<form id="form-expense" class="form-horizontal" action="expense.php" method="post" enctype="multipart/form-data">
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">
  <div class="box-body">
    <div class="form-group">
      <label for="reference_no" class="col-sm-3 control-label">
        <?php echo $language->get('label_reference_no'); ?>
      </label>
      <div class="col-sm-8">
        <?php $reference_no = isset($expense['reference_no']) ? $expense['reference_no'] : null; ?>
        <input type="text" class="form-control" id="reference_no" value="<?php echo $reference_no; ?>" name="reference_no" autofocus <?php echo $reference_no ? 'readonly' : null; ?> autocomplete="off">
      </div>
    </div>
    <div class="form-group">
        <label for="category_id" class="col-sm-3 control-label">
          <?php echo $language->get('label_category'); ?>
        </label>
        <div class="col-sm-8">
          <select class="form-control select2" name="category_id">
            <option value="">
              <?php echo $language->get('text_select'); ?>
            </option>
            <?php foreach (get_expense_categorys() as $the_expense_category) { ?>
              <option value="<?php echo $the_expense_category['category_id']; ?>" <?php echo $expense['category_id'] == $the_expense_category['category_id'] ? 'selected' : null;?>><?php echo $the_expense_category['category_name'] ; ?></option>
            <?php } ?>
         </select>
        </div>
      </div>
    <div class="form-group">
      <label for="title" class="col-sm-3 control-label">
        <?php echo $language->get('label_title'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="text" id="title" class="form-control" name="title" value="<?php echo $expense['title'];?>">
      </div>
    </div>
    <div class="form-group">
      <label for="amount" class="col-sm-3 control-label">
        <?php echo $language->get('label_amount'); ?><i class="required">*</i>
       </label>
      <div class="col-sm-8">
        <input type="number" id="amount" class="form-control" name="amount" value="<?php echo $expense['amount'];?>" onclick="this.select();">
      </div>
    </div>
    <div class="form-group">
      <label for="note" class="col-sm-3 control-label">
        <?php echo $language->get('label_notes'); ?>
      </label>
      <div class="col-sm-8">
        <textarea name="note" id="note" class="form-control"><?php echo $expense['note']; ?></textarea>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-8">            
        <button id="edit-expense-update" class="btn btn-info" data-form="#form-expense" data-datatable="#expense-expense-list" name="submit" data-loading-text="Updating...">
          <i class="fa fa-fw fa-pencil"></i>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>
  </div>
</form>