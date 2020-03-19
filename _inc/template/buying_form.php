<style type="text/css">
  .modal-lg {
    width: 80%;
  }
</style>
<?php $language->load('buy'); ?>
<?php $invoice_id = isset($invoice['invoice_id']) ? $invoice['invoice_id'] : null; ?>
<form id="form-buying" class="form-horizontal" action="buying.php" method="post" enctype="multipart/form-data">
  <?php if ($invoice_id) : ?>
    <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <?php else: ?>
    <input type="hidden" id="action_type" name="action_type" value="CREATE">
  <?php endif; ?>
  <!-- <input type="hidden" id="sup_id" name="sup_id" value="<?php echo $supplier['sup_id']; ?>"> -->
  <div class="box-body">
    <div class="form-group">
      <label for="add_item" class="col-sm-3 control-label">
        <?php echo $language->get('label_supplier'); ?>
      </label>
      <div class="col-sm-6">
        <select id="supplier_selector" class="form-control select2" name="sup_id">
          <option value=""><?php echo $language->get('text_select'); ?></option>
          <?php foreach (get_suppliers() as $sup) : ?>
            <option value="<?php echo $sup['sup_id'];?>" <?php echo isset( $supplier['sup_id']) && ($sup['sup_id'] == $supplier['sup_id']) ? 'selected' : null;?>>
              <?php echo $sup['sup_name'];?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="well paddingbt-50">
      <div class="form-group">
        <div class="col-sm-5">
          <label for="invoice_id" class="control-label">
            <?php echo $language->get('label_invoice_id'); ?>
            <span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_invoice_id'); ?>"></span>  
          </label>
          <br>
          <input type="text" class="form-control" id="invoice_id" value="<?php echo $invoice_id; ?>" name="invoice_id" autofocus <?php echo $invoice_id ? 'readonly' : null; ?> autocomplete="off">
        </div>
        <div class="col-sm-4">
          <label for="date" class="control-label">
            <?php echo $language->get('label_date'); ?>
          </label><br>
          <input type="date" class="form-control" id="date" name="date" value="<?php echo isset($invoice['buy_date']) ? $invoice['buy_date'] : date('Y-m-d'); ?>">
        </div>
        <div class="col-sm-3">
          <label for="time" class="control-label">
            <?php echo $language->get('label_time'); ?>
          </label><br>
          <div class="input-group bootstrap-timepicker timepicker">
              <input type="text" class="form-control input-small showtimepicker" id="time" name="time" value="<?php echo isset($invoice['buy_time']) ? to_am_pm($invoice['buy_time']) : to_am_pm(current_time()); ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="add_item" class="col-sm-3 control-label">
        <?php echo $language->get('label_search_product'); ?>
        <span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_search_product'); ?>"></span>
      </label>
      <div class="col-sm-9">
        <input type="text" onkeypress="return event.keyCode != 13;" data-type="p_name" placeholder="<?php echo $language->get('placeholder_search_product'); ?>" id="add_item" class="form-control autocomplete-product" autocomplete="off" onclick="this.select();">
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">

          <table id="poTable" class="table table-striped table-bordered">
            <thead>
              <tr class="active">
                <th class="w-25">
                  <?php echo $language->get('label_product'); ?>
                </th>
                <th class="w-10 text-center">
                  <?php echo $language->get('label_available'); ?>
                </th>
                <th class="w-10 text-center">
                  <?php echo $language->get('label_quantity'); ?>
                </th>
                <th class="w-15 text-center">
                  <?php echo $language->get('label_buying_price'); ?>
                </th>
                <th class="w-15 text-center">
                  <?php echo $language->get('label_selling_price'); ?>
                </th>
                <th class="w-10 text-right">
                  <?php echo $language->get('label_tax_amount'); ?>
                </th>
                <th class="w-15 text-right">
                  <?php echo $language->get('label_subtotal'); ?>
                </th>
                <th class="w-10 text-center">
                  <i class="fa fa-trash-o"></i>
                </th>
              </tr>
            </thead>

            <tbody>   
              <?php if (isset($invoice_items)) : ?>
                <?php foreach ($invoice_items as $product) : $product_info = get_the_product($product['p_id']); ?>
                  <tr id="<?php echo $product['p_id']; ?>" class="<?php echo $product['p_id']; ?>" data-item-id="<?php echo $product['p_id']; ?>">
                    <td data-title="<?php echo $language->get('label_product'); ?>">
                      <input name="product[<?php echo $product['p_id']; ?>][id]" type="hidden" class="rid" value="<?php echo $product['p_id']; ?>">
                      <input name="product[<?php echo $product['p_id']; ?>][name]" type="hidden" class="rname" value="<?php echo $product['item_name']; ?>">
                      <input name="product[<?php echo $product['p_id']; ?>][category_id]" type="hidden" class="rcategoryid" value="<?php echo $product['category_id']; ?>">
                      <span class="sname" id="name_<?php echo $product['p_id']; ?>">  
                        <?php echo $product['item_name']; ?>
                      </span>
                    </td>
                    <td class="text-center" data-title="<?php echo $language->get('label_available'); ?>">
                      <span class="savailable" id="available_<?php echo $product['p_id']; ?>">
                        <?php echo $product['quantity_in_stock']; ?>
                      </span>
                    </td>
                    <td data-title="<?php echo $language->get('label_quantity'); ?>">
                      <input class="form-control text-center rquantity" name="product[<?php echo $product['p_id']; ?>][quantity]" type="text" value="<?php echo $product['item_quantity']; ?>" data-id="<?php echo $product['p_id']; ?>" id="quantity_<?php echo $product['p_id']; ?>" onclick="this.select();" onKeyUp="if(this.value<0){this.value='0';}">
                    </td>
                    <td data-title="<?php echo $language->get('label_buying_price'); ?>">
                      <input class="form-control text-center rcost" name="product[<?php echo $product['p_id']; ?>][cost]" type="text" value="<?php echo currency_format($product['item_buying_price']); ?>" data-id="<?php echo $product['p_id']; ?>" data-item="<?php echo $product['p_id']; ?>" id="cost_<?php echo $product['p_id']; ?>" onclick="this.select();">
                    </td>
                    <td data-title="<?php echo $language->get('label_selling_price'); ?>">
                      <input class="form-control text-center rsell" name="product[<?php echo $product['p_id']; ?>][sell]" type="text" value="<?php echo currency_format($product['item_selling_price']); ?>" data-id="<?php echo $product['p_id']; ?>" data-item="<?php echo $product['p_id']; ?>" id="cost_<?php echo $product['p_id']; ?>" onclick="this.select();">
                    </td>
                    <td class="text-right" data-title="<?php echo $language->get('label_tax_amount'); ?>">
                      <input id="itemTaxMethod_<?php echo $product['p_id']; ?>" name="product[<?php echo $product['p_id']; ?>][item_tax_method]" type="hidden" value="<?php echo $product_info['tax_method']; ?>">
                      <?php if (isset($product_info['taxrate'])) : ?>
                      <input id="itemTaxrate_<?php echo $product['p_id']; ?>" name="product[<?php echo $product['p_id']; ?>][item_taxrate]" type="hidden" value="<?php echo isset($product_info['taxrate']['taxrate']) ? $product_info['taxrate']['taxrate'] : 0; ?>">
                      <?php endif; ?>
                      <input id="itemTaxAmount_<?php echo $product['p_id']; ?>" name="product[<?php echo $product['p_id']; ?>][item_tax_amount]" type="hidden" value="<?php echo $product['item_tax']; ?>">
                      <span class="stax" id="taxAmount_<?php echo $product['p_id']; ?>"><?php echo currency_format($product['item_tax']); ?></span>
                    </td>
                    <td class="text-right" data-title="<?php echo $language->get('label_subtotal'); ?>">
                      <span class="text-right ssubTotal" id="subTotal_<?php echo $product['p_id']; ?>">
                        <?php echo currency_format($product['item_total']); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <i class="fa fa-close text-red pointer spodel" id="delete-item" data-id="<?php echo $product['p_id']; ?>" title="<?php echo $language->get('button_remove'); ?>"></i>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>

            <tfoot>
              <tr class="bg-gray">
                <th class="text-right" colspan="6">
                  <?php echo $language->get('label_payable_amount'); ?>
                </th>
                <th class="col-xs-2 text-right">
                  <input type="hidden" name="total_tax" ng-init="totalTax=<?php echo isset($invoice['order_tax']) && !empty($invoice_items) ? currency_format($invoice['order_tax']) : '0.00'; ?>" value="{{ totalTax }}">
                  <input type="hidden" name="total" value="{{ total }}">
                  <span id="gtotal" ng-init="total=<?php echo isset($invoice['payable_amount']) && !empty($invoice_items) ? currency_format($invoice['payable_amount']) : '0.00'; ?>">{{ total | formatDecimal:2 }}</span>
                </th>
                <th class="w-25p"></th>
              </tr>
              <tr class="bg-gray">
                <th class="text-right" colspan="6">
                  <?php echo $language->get('label_paid_amount'); ?>
                </th>
                <th class="text-right">
                  <input class="form-control text-right buy-total-amount" type="text" name="paid_amount" value="{{ total | formatDecimal:2 }}" >
                </th>
                <th>&nbsp;</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
     </div>

    <div class="form-group">
      <label for="attachment" class="col-sm-3 control-label">
        <?php echo $language->get('label_attachment'); ?>
        <span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_attachment'); ?>"></span>
      </label>
      <div class="col-sm-9">
        <input type="file" name="attachment" class="form-control tip buying-attachement" id="attachment" tabindex="-1">
        <div class="bootstrap-filestyle input-group">
          <input type="text" class="form-control " disabled> 
          <span class="group-span-filestyle input-group-btn" tabindex="0">
            <label for="attachment" class="btn btn-default ">
              <span class="fa fa-folder-open"></span> Choose file
            </label>
          </span>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="col-sm-6 col-sm-offset-3">
        <div>
          <input type="checkbox" id="force_upload" name="force_upload" value="1"> 
          <label for="force_upload">
            <?php echo $language->get('label_force_upload'); ?>
          </label>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="buying_note" class="col-sm-3 control-label">
        <?php echo $language->get('label_note'); ?>
      </label>
      <div class="col-sm-9">
        <textarea name="buying_note" id="buying_note" class="form-control"><?php echo isset($invoice) ? $invoice['invoice_note'] : null; ?></textarea>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label"></label>
      <div class="col-sm-6">            
        <button id="buying-confirm-btn" class="btn btn-block btn-info" data-form="#form-buying" data-datatable="#invoice-invoice-list" name="submit" data-loading-text="Processing...">
          <?php if ($invoice_id) : ?>
            <i class="fa fa-fw fa-pencil"></i> 
            <?php echo $language->get('button_update'); ?>
          <?php else : ?>
            <i class="fa fa-fw fa-money"></i> 
            <?php echo $language->get('button_buy_now'); ?>
          <?php endif; ?>
        </button>
      </div>
    </div>
  </div>
</form>