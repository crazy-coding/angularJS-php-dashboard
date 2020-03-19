<?php $language->load('unit'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_delete_title'); ?>
</h4>

<form class="form-horizontal" id="unit-del-form" action="unit.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="DELETE">
  <input type="hidden" id="unit_id" name="unit_id" value="<?php echo $unit['unit_id']; ?>">
  <h4 class="unit-title text-center">
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
            <select name="new_unit_id" class="form-control">
                <option value="">
                  <?php echo $language->get('text_select'); ?>
                </option>
              <?php foreach (get_units() as $the_unit) : ?>
                <?php if($the_unit['unit_id'] == $unit['unit_id']) continue; ?>
                <option value="<?php echo $the_unit['unit_id']; ?>">
                  <?php echo $the_unit['unit_name']; ?>
                </option>
              <?php endforeach; ?>
            </select> 
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-4 control-label"></label>
      <div class="col-sm-6">
        <button id="unit-delete" data-form="#unit-del-form" data-datatable="#unit-unit-list" class="btn btn-danger" name="btn_edit_box" data-loading-text="Deleting...">
          <span class="fa fa-fw fa-trash"></span>
          <?php echo $language->get('button_delete'); ?>
        </button>
      </div>
    </div>
  </div>
</form>