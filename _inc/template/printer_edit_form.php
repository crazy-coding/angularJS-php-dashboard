<?php $language->load('printer'); ?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="printer-form" action="printer.php" method="post">
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="printer_id" name="printer_id" value="<?php echo $printer['printer_id']; ?>">
  <div class="box-body">
    
      <div class="form-group">
        <label for="title" class="col-sm-3 control-label">
          <?php echo $language->get('label_title'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="title" value="<?php echo isset($printer['title']) ? $printer['title'] : null; ?>" name="title" required>
        </div>
      </div>  

      <div class="form-group">
        <label for="type" class="col-sm-3 control-label">
          <?php echo $language->get('label_type'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-8">
          <select class="form-control select2" name="type" id="edit-printer-type">
            <option value="network"<?php echo $printer['type'] == 'network' ? ' selected' : null; ?>>Network</option>
            <option value="windows" <?php echo $printer['type'] == 'windows' ? ' selected' : null; ?>>Windows</option>
            <option value="linux" <?php echo $printer['type'] == 'linux' ? ' selected' : null; ?>>Linux</option>
         </select>
        </div>
      </div>

      <div class="form-group">
        <label for="char_per_line" class="col-sm-3 control-label">
          <?php echo $language->get('label_char_per_line'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="char_per_line" value="<?php echo isset($printer['char_per_line']) ? $printer['char_per_line'] : 200; ?>" name="char_per_line" required>
        </div>
      </div> 

      <div class="form-group">
        <label for="path" class="col-sm-3 control-label">
          <?php echo $language->get('label_path'); ?>
        </label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="path" value="<?php echo isset($printer['path']) ? $printer['path'] : null; ?>" name="path" required>
          <p>
            <small>
              <b>For Windows:</b> (Local USB, Serial or Parallel Printer): Share the printer and enter the share name for your printer here or for Server Message Block (SMB): enter as a smb:// url format such as <span class="text-blue">smb://computername/Receipt Printer</span> <br>
              <b>For Linux:</b> Parallel as <span class="text-blue">/dev/lp0</span>, USB as <span class="text-blue">/dev/usb/lp1</span>, USB-Serial as <span class="text-blue">/dev/ttyUSB0</span>, Serial as <span class="text-blue">/dev/ttyS0</span>
            </small>
          </p>
        </div>
      </div> 

      <div class="form-group">
        <label for="ip_address" class="col-sm-3 control-label">
          <?php echo $language->get('label_ip_address'); ?>
        </label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="ip_address" value="<?php echo isset($printer['ip_address']) ? $printer['ip_address'] : null; ?>" name="ip_address" required>
        </div>
      </div> 

      <div class="form-group">
        <label for="port" class="col-sm-3 control-label">
          <?php echo $language->get('label_port'); ?>
        </label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="port" value="<?php echo isset($printer['port']) ? $printer['port'] : 9100; ?>" name="port" required>
          <p>
            <i>Most printers are open on port 9100</i>
          </p>
        </div>
      </div> 

      <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8 store-selector">
        <div class="checkbox selector">
          <label>
            <input type="checkbox" onclick="$('input[name*=\'printer_store\']').prop('checked', this.checked);"> Select / Deselect
          </label>
        </div>
        <div class="filter-searchbox">
          <input ng-model="search_store" class="form-control" type="text" id="search_store" placeholder="<?php echo $language->get('search'); ?>">
        </div>
        <div class="well well-sm store-well"> 
          <div filter-list="search_store">
          <?php foreach(get_stores() as $the_store) : ?>                    
            <div class="checkbox">
              <label>                         
                <input type="checkbox" name="printer_store[]" value="<?php echo $the_store['store_id']; ?>" <?php echo $the_store['store_id'] == store_id() ? 'checked' : null; ?>>
                <?php echo $the_store['name']; ?>
              </label>
            </div>
          <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="status" class="col-sm-3 control-label">
        <?php echo $language->get('label_status'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <select id="status" class="form-control" name="status" required>
          <option value="1">
            <?php echo $language->get('text_active'); ?>
          </option>
          <option value="0">
            <?php echo $language->get('text_inactive'); ?>
          </option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="sort_order" class="col-sm-3 control-label">
        <?php echo $language->get('label_sort_order'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-8">
        <input type="number" class="form-control" id="sort_order" value="<?php echo isset($printer['sort_order']) ? $printer['sort_order'] : 0; ?>" name="sort_order">
      </div>
    </div>

      <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-8">
          <button id="printer-update" data-form="#printer-form" data-datatable="#printer-printer-list" class="btn btn-info" name="btn_edit_printer" data-loading-text="Updating...">
            <span class="fa fa-fw fa-pencil"></span>
            <?php echo $language->get('button_update'); ?>
          </button>
        </div>
      </div>

  </div>
</form>