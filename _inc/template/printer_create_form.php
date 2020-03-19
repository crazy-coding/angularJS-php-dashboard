<?php $language->load('printer'); ?>

<form id="create-printer-form" class="form form-horizontal" action="printer.php" method="post" enctype="multipart/form-data">
  <input type="hidden" id="action_type" name="action_type" value="CREATE">
  <div class="box-body">

      <div class="form-group">
        <label for="title" class="col-sm-3 control-label">
          <?php echo $language->get('label_title'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="title" value="<?php echo isset($request->post['title']) ? $request->post['title'] : null; ?>" name="title" required>
        </div>
      </div>  

      <div class="form-group">
        <label for="type" class="col-sm-3 control-label">
          <?php echo $language->get('label_type'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-6">
          <select ng-model="printerType" class="form-control select2" name="type" id="printer-type">
            <option value="network">Network</option>
            <option value="windows">Windows</option>
            <option value="linux">Linux</option>
         </select>
        </div>
      </div>

      <div class="form-group">
        <label for="char_per_line" class="col-sm-3 control-label">
          <?php echo $language->get('label_char_per_line'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="char_per_line" value="<?php echo isset($request->post['char_per_line']) ? $request->post['char_per_line'] : 200; ?>" name="char_per_line" required>
        </div>
      </div> 

      <div ng-show="!isNetwork" class="form-group">
        <label for="path" class="col-sm-3 control-label">
          <?php echo $language->get('label_path'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="path" value="<?php echo isset($request->post['path']) ? $request->post['path'] : null; ?>" name="path" required>
          <p>
            <small>
              <b>For Windows:</b> (Local USB, Serial or Parallel Printer): Share the printer and enter the share name for your printer here or for Server Message Block (SMB): enter as a smb:// url format such as <span class="text-blue">smb://computername/Receipt Printer</span> <br>
              <b>For Linux:</b> Parallel as <span class="text-blue">/dev/lp0</span>, USB as <span class="text-blue">/dev/usb/lp1</span>, USB-Serial as <span class="text-blue">/dev/ttyUSB0</span>, Serial as <span class="text-blue">/dev/ttyS0</span>
            </small>
          </p>
        </div>
      </div> 

      <div ng-show="isNetwork" class="form-group">
        <label for="ip_address" class="col-sm-3 control-label">
          <?php echo $language->get('label_ip_address'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="ip_address" value="<?php echo isset($request->post['ip_address']) ? $request->post['ip_address'] : null; ?>" name="ip_address" required>
        </div>
      </div> 

      <div ng-show="isNetwork" class="form-group">
        <label for="port" class="col-sm-3 control-label">
          <?php echo $language->get('label_port'); ?><i class="required">*</i>
        </label>
        <div class="col-sm-6">
          <input type="text" class="form-control" id="port" value="<?php echo isset($request->post['port']) ? $request->post['port'] : 9100; ?>" name="port" required>
          <p>
            <i>Most printers are open on port 9100</i>
          </p>
        </div>
      </div> 

      <div class="form-group">
      <label class="col-sm-3 control-label">
        <?php echo $language->get('label_store'); ?><i class="required">*</i>
      </label>
      <div class="col-sm-6 store-selector">
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
      <div class="col-sm-6">
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
      <div class="col-sm-6">
        <input type="number" class="form-control" id="sort_order" value="<?php echo isset($request->post['sort_order']) ? $request->post['sort_order'] : 0; ?>" name="sort_order">
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-6">
        <button id="create-printer-submit" data-form="#create-printer-form" data-datatable="#printer-printer-list" class="btn btn-info" name="btn_edit_printer" data-loading-text="Saving...">
          <span class="fa fa-fw fa-pencil"></span>
          <?php echo $language->get('button_save'); ?>
        </button>
        <button type="reset" class="btn btn-danger" id="reset" name="reset"><span class="fa fa-fw fa-circle-o"></span>
          <?php echo $language->get('button_reset'); ?>
        </button>
      </div>
    </div>

  </div>
  <!-- end .box-body -->
</form>