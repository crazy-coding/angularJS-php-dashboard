<!-- 
[logo]
[store_name]
[gst_reg]
[vat_reg]
[invoice_id]
[date]
[time]
[data_time]
[store_address]
[store_contact]
[customer_name]
[customer_address]
[customer_phone]
[customer_email]
[customer_contact]
[footer_note]
[invoice_note]
[item_list]
[payment_list]
[tax_summary]
[barcode]
[owner_signature]
[cashier_signateure] 
-->
<style type="text/css">
.editable-buttons {
  display: none;
}
.editable {
  position: relative;
  display: block;
}
.editable .btn-group {
  position: absolute;
  top: -22px;
  left: 50%;
  margin-left: -40px;
}
</style>

<div class="editable-buttons">
<div class="btn-group">
  <button class="btn btn-xs btn-danger delete"><span class="fa fa-trash"></span></button>
  <button class="btn btn-xs btn-info clone"><span class="fa fa-copy"></span></button>
</div>
</div>

<table class="table">
  <tbody>
    <tr class="header">
      <td class="text-center" colspan="2">
        <div class="header-info">
          <div class="logo editable"><span>{logo}</span></div>
          <p class="store-address editable"><span>{store_address}</span></p>
          <h2 class="store-name editable"><span>{store_name}</span></h2>
          <div class="simplte-row editable"><span>GST Reg: {gst_reg}</span></div>
          <div class="simplte-row editable"><span>VAT Reg: {vat_reg}</span></div>
          <h4 class="invoice-id editable"><span>Invoice ID: {invoice_id}</span></h4>
          <h5 class="date editable"><span>Date: {date_time}</span></h5>
          <h4 class="title editable"><span>Tax Invoice</span></h4>
        </div>
      </td>
    </tr>
    <tr class="address">
      <td>  
        <div class="customer-name editable"><span>Customer: {customer_name}</span></div>
        <div class="customer-contact editable"><span>Customer Contact: {customer_contact}</span></div>
      </td>
    </tr>
  </tbody>
</table>
<div class="table-responsive items">  
  <table class="table table-bordered table-striped table-hover mb-0">
    <thead>
      <tr class="active">
				<th class="w-5 text-center" contentEditable>Sl.No.</th>
				<th class="w-50" contentEditable>Product Name</th>
        <th class="w-10" contentEditable>Quantity</th>
				<th class="text-right w-15" contentEditable>Price</th>
				<th class="text-right w-20" contentEditable>Total</th>
			</tr>
    </thead>
    <tbody>
      <tr>
        <td class="text-center" data-title="Sl.">
          #1                            
        </td>
        <td data-title="Product Name">
          ajke 2                                                                                                              
        </td>
        <td data-title="Quantity">
          x 1 box                            
        </td>
        <td class="text-right" data-title="Price">
          120.00                            
        </td>
        <td class="text-right" data-title="Total">
          120.00                            
        </td>
      </tr>              
    </tbody>
  </table>
</div>
<div class="table-responsive calculation">
  <table id="selling_bill" class="table">
    <tbody>
      <tr class="active">
      	<td class="w-80 text-right" contentEditable>Sub Total</td>
      	<td class="w-20 text-right" contentEditable>120.00</td>
      </tr>
      <tr class="active">
      	<td class="w-80 text-right" contentEditable>Discount</td>
      	<td class="w-20 text-right">
          0.00                        
        </td>
      </tr>
      <tr class="active">
        <td class="w-80 text-right" contentEditable>
          Order Tax                        
        </td>
        <td class="w-20 text-right">
          0.00                        
        </td>
      </tr>
      <tr class="active">
      	<td class="w-80 text-right" contentEditable>
          Payable Amount                        
        </td>
      	<td class="w-20 text-right">
          120.00                        
        </td>
      </tr>
      <tr class="active">
        <td class="w-80 text-right" contentEditable>
          Paid                        
        </td>
        <td class="w-20 text-right">
          120.00                        
        </td>
      </tr>
      <tr class="active">
        <td class="w-80 text-right" contentEditable>
          Due                        
        </td>
        <td class="w-20 text-right">
          0.00                        
        </td>
      </tr>
    </tbody>
  </table>
</div>
<div class="table-responsive payments">
  <table class="table table-striped">
    <tbody>
      <tr class="success">
        <td class="w-40 text-right">
          <small><i>Paid on</i></small> 2019-01-30 11:47:18 
                                                (via Cash on Delivery)
                                                by Admin                                                          
        </td>
        <td class="w-30 text-right">
          Amount:&nbsp; 120.00                                                          
        </td>
        <td class="w-30 text-right">
          &nbsp;
        </td>
      </tr>                                     
    </tbody>
  </table>
</div>
<div class="tax-summary">
  <div class="text-center"><h5 class="title editable"><span>Tax Summary</span></h5></div>
  <table class="table table-bordered table-striped print-table order-table table-condensed mb-0">
    <thead>
      <tr class="active">
        <th class="w-35 text-center" contentEditable>Name</th>
        <th class="w-20 text-center" contentEditable>Code</th>
        <th class="w-15 text-center" contentEditable>Qty</th>
        <th class="w-15 text-right" contentEditable>Tax Excl</th>
        <th class="w-15 text-right" contentEditable>Tax Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="text-center">Tax @20%</td>
        <td class="text-center">TTX</td>
        <td class="text-center">1</td>
        <td class="text-right">120.00</td>
        <td class="text-right">0.00</td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="active">
        <th colspan="4" class="text-right">Total Tax Amount</th>
        <th class="text-right">0.00</th>
      </tr>
    </tfoot>
  </table>
</div>
<p class="footer-note editable">
  <span>Thank you for choosing us!</span>
</p>
<div class="qrcode editable">
  {qucode}
</div>

<script type="text/javascript">
$(document).ready(function() {
  var editableButtons = $(".editable-buttons");
  $(".editable span").attr("contentEditable", true);
  $(".editable").hover(function(e) {
    var $this = $(this);
    $(".editable").find(".btn-group").remove();
    $this.append(editableButtons.html());
    $this.find(".editable-buttons").remove();
  }, function() {
    $(".editable").find(".btn-group").remove();
  });

  $(document).delegate(".delete", "click", function(e) {
    e.preventDefault();
    $(this).parent().parent().remove();
  });

  $(document).delegate(".clone", "click", function(e) {
    e.preventDefault();
    var $parent = $(this).parent().parent();
    $parent.clone().insertAfter($parent);
  });
});
</script>