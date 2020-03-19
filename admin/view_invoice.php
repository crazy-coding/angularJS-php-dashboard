<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'view_invoice')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('invoice');

// Validate INVOICE ID
if (!isset($request->get['invoice_id'])) { 
  redirect('invoice.php');
}

// INVOICE MODEL
$invoice_model = $registry->get('loader')->model('invoice');
$invoice_id = $request->get['invoice_id'];
$invoice_info = $invoice_model->getInvoiceInfo($invoice_id);
if (!$invoice_info) {
  redirect('invoice.php');
}

// FETCH INVOICE INFO
$inv_type         = $invoice_info['inv_type'];
$created_at       = format_date($invoice_info['created_at']);
$customer_id      = $invoice_info['customer_id'];
$customer_name    = $invoice_info['customer_name'];
if ($invoice_info['customer_mobile']) {
  $customer_contact = $invoice_info['customer_mobile'];
} else  {
  $customer_contact = $invoice_info['mobile_number'] ? $invoice_info['mobile_number'] : $invoice_info['customer_email'];
}
$invoice_note     = $invoice_info['invoice_note'];
$invoice_items = $invoice_model->getInvoiceItems($invoice_id);
$selling_price = $invoice_model->getSellingPrice($invoice_id);
$taxes = $invoice_model->getInvoiceItemTaxes($invoice_id);
$document->setTitle($language->get('text_invoice') . ' - ' . $invoice_id);

// PAYMENT MODEL
$payment_model = $registry->get('loader')->model('payment');

// Qrcode
$qrcode_text = 'InvoiceID: ' . $invoice_id . ', Name: ' . $customer_name;
include(DIR_VENDOR.'/phpqrcode/qrlib.php');
QRcode::png($qrcode_text, ROOT.'/storage/qrcode.png', 'L', 3, 1);

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/InvoiceViewController.js');  

// SIDEBAR COLLAPSE
$document->setBodyClass('sidebar-collapse');
$document->setBodyClass('invoice-page');

// ADD BODY CLASS
$document->setBodyClass('sidebar-collapse');


// $parser = new Parser();
// $data = array(
//   'logo' => '<img src="http://pos-bd/assets/itsolution24/img/logo-favicons/1_logo.png">',
//   'store_name' => store('name'),
//   'gst_reg' => get_preference('gst_reg_no'),
//   'vat_reg' => get_preference('vat_reg_no'),
//   'invoice_id' => $invoice_id,
//   'date' => date('Y-m-d', strtotime($created_at)),
//   'time' => date('H:i:s', strtotime($created_at)),
//   'data_time' => $created_at,
//   'store_address' => store('address'),
//   'store_contact' => store('mobile'),
//   'customer_name' => $customer_name,
//   'customer_address' => '',
//   'customer_mobile' => $invoice_info['customer_mobile'] ? $invoice_info['customer_mobile'] : $invoice_info['mobile_number'],
//   'customer_email' => $invoice_info['customer_email'],
//   'customer_contact' => $customer_contact,
//   'footer_note' => get_preference('invoice_footer_text'),
//   'invoice_note' => get_preference('invoice_footer_text'),
//   'item_list' => '',
//   'payment_list' => '',
//   'tax_summary' => '',
//   'qucode' => '<img src="../storage/qrcode.png">',
//   'owner_signature' => '',
//   'cashier_signateure]' => '',
// );
// $statement = $db->prepare("SELECT `content` FROM `pos_receipt_template` WHERE `store_id` = ? AND `is_active` = ?");
// $statement->execute(array(store_id(), 1));
// $template = $statement->fetch(PDO::FETCH_ASSOC);
// echo $parser->parse($template['content'], $data);
// exit();


// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php"); 
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="InvoiceViewController">

  <!-- Content Header Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_view_invoice_title'); ?> &larr; <?php echo $invoice_id ; ?>
      <small>
        <?php echo store('name'); ?>
      </small>
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="dashboard.php">
          <i class="fa fa-dashboard"></i> 
          <?php echo $language->get('text_dashboard'); ?>
        </a>
      </li>
      <li>
        <a href="invoice.php">
          <?php echo $language->get('text_invoice'); ?>
        </a>
      </li>
      <li class="active">
        <?php echo $invoice_id ; ?>
      </li>
    </ol>
  </section>
  <!-- Content Header End -->

  <!-- Content Start -->
  <section class="content">

    <?php if(DEMO) : ?>
    <div class="box">
      <div class="box-body">
        <div class="alert alert-info mb-0">
          <p><span class="fa fa-fw fa-info-circle"></span> <?php echo $language->get('text_demo'); ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-info">
        	<div class='box-body'>    
            <div id="invoice" class="row">
              <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
                
                <table class="table">
                  <tbody>
                    <tr class="invoice-header">
                      <td class="text-center" colspan="2">
                        <div class="invoice-header-info">
                          <div class="logo">
                            <?php if ($store->get('logo')): ?>
                              <img src="<?php echo root_url(); ?>/assets/itsolution24/img/logo-favicons/<?php echo $store->get('logo'); ?>">
                            <?php else: ?>
                              <img src="<?php echo root_url(); ?>/assets/itsolution24/img/logo-favicons/nologo.png">
                            <?php endif; ?>
                          </div>
                          <h2 class="invoice-title"><?php echo store('name'); ?></h2>
                          <?php if (get_preference('invoice_view') == 'indian_gst'):?>
                            <?php if (get_preference('gst_reg_no')):?>
                              <div><?php echo $language->get('label_gst_reg_no'); ?>: <?php echo get_preference('gst_reg_no'); ?></div>
                            <?php endif;?>
                          <?php endif;?>
                          <?php if (store('vat_reg_no')):?>
                            <div><?php echo $language->get('label_vat_reg_no'); ?>: <?php echo store('vat_reg_no'); ?></div>
                          <?php endif;?>
                          <h6 class="font-size18">
                            <?php echo $language->get('label_invoice_id'); ?>: <?php echo $invoice_id; ?>
                            <?php if (invoice_edit_lifespan($invoice_info['created_at'])) : ?>
                                <a href="pos.php?customer_id=<?php echo $customer_id; ?>&invoice_id=<?php echo $invoice_id; ?>">
                                  &nbsp;<span class="fa fa-edit"></span>
                                </a>
                            <?php endif; ?>    
                          </h6>
                          <h5>
                            <?php echo $language->get('label_date'); ?>: <?php echo $created_at; ?>
                          </h5>
                          <?php if (get_preference('invoice_view') != 'standard'):?>
                            <h4><b><?php echo $language->get('text_tax_invoice'); ?></b></h4>
                          <?php endif;?>
                        </div>
                      </td>
                    </tr>
                    <tr class="invoice-address">
                      <td class="w-50 invoice-from">
                        <div>
                          <div class="com-details">
                            <div>
                              <strong>
                                <?php echo $language->get('text_from'); ?>
                              </strong>
                            </div>
                            <span class="invoice-address">
                              <?php echo store('address'); ?>
                            </span><br>
                            <span>
                              <?php echo $language->get('label_mobile'); ?>: <?php echo store('mobile'); ?>
                            </span>
                          </div>
                        </div>
                      </td>
                      <td class="text-right invoice-to w-50">  
                        <div>
                          <strong>
                            <?php echo $language->get('text_to'); ?>
                          </strong>
                        </div>
                        <div>
                          <?php echo $language->get('label_customer_name'); ?>: <?php echo $customer_name ; ?> 
                          <a class="view-link" href="customer_profile.php?customer_id=<?php echo $customer_id; ?>">
                            <span class="fa fa-eye"></span>
                          </a>
                        </div>
                        <div>
                          <?php echo $language->get('label_customer_contact'); ?>: <?php echo $customer_contact ; ?>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div class="table-responsive invoice-items">  
                  <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                      <tr class="active">
                				<th class="w-5 text-center">
                          <?php echo $language->get('label_serial_no'); ?>
                        </th>
                				<th class="w-50">
                          <?php echo $language->get('label_product_name'); ?>
                        </th>
                        <th class="w-10">
                          <?php echo $language->get('label_quantity'); ?>
                        </th>
                				<th class="text-right w-15">
                          <?php echo $language->get('label_price'); ?>
                        </th>
                				<th class="text-right w-20">
                          <?php echo $language->get('label_total'); ?>
                        </th>
                			</tr>
                    </thead>
                    <tbody>
                      <?php
                        $i=0;
                        foreach($invoice_items as $item) : $item_info = get_the_product($item['item_id']);
                          $i++; ?>
                          <tr>
                            <td class="text-center" data-title="Sl.">
                              #<?php echo $i ; ?>
                            </td>
                            <td data-title="<?php echo $language->get('label_product_name'); ?>">
                              <?php echo $item['item_name'] ; ?> 
                              <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
                                <?php if ($item['gst']) : ?>
                                  &nbsp;- <?php echo sprintf($language->get('label_tax'), currency_format($item['gst']).'%'); ?>
                                <?php endif; ?>
                              <?php else : ?>
                                <?php if ($item_info['taxrate']) : ?>
                                  &nbsp;- <?php echo sprintf($language->get('label_tax'), $item_info['taxrate']['taxrate_code']); ?>
                                <?php endif; ?>
                              <?php endif; ?>
                              <?php if ($item_info['hsn_code']) : ?>
                                (HSN Code: <?php echo $item_info['hsn_code'];?>)
                              <?php endif; ?>
                            </td>
                            <td data-title="<?php echo $language->get('label_quantity'); ?>">
                              x <?php echo $item['item_quantity']; ?> <?php echo get_the_unit($item_info['unit_id'], 'unit_name');?>
                            </td>
                            <td class="text-right" data-title="<?php echo $language->get('label_price'); ?>">
                              <?php echo currency_format($item['item_price']); ?>
                            </td>
                            <td class="text-right" data-title='<?php echo $language->get('label_total'); ?>'>
                              <?php echo currency_format($item['item_total']); ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="table-responsive bt-1">
                  <table id="selling_bill" class="table">
                    <tbody>
                      <tr class="active">
                      	<td class="w-80 text-right">
                          <?php echo $language->get('label_sub_total'); ?>
                        </td>
                      	<td class="w-20 text-right">
                          <?php echo currency_format($selling_price['subtotal']); ?>
                        </td>
                      </tr>
                      <tr class="active">
                      	<td class="w-80 text-right">
                          <?php echo $language->get('label_discount_amount'); ?>
                        </td>
                      	<td class="w-20 text-right">
                          <?php echo $selling_price['discount_amount'] ? currency_format($selling_price['discount_amount']) : '0.00'; ?>
                        </td>
                      </tr>
                      <tr class="active">
                        <td class="w-80 text-right">
                          <?php echo $language->get('label_order_tax'); ?>
                        </td>
                        <td class="w-20 text-right">
                          <?php echo $selling_price['order_tax'] ? currency_format($selling_price['order_tax']) : '0.00'; ?>
                        </td>
                      </tr>
                      <?php if (get_preference('invoice_view') == 'indian_gst') : 
                      $igst = $selling_price['igst'];
                      $cgst = $selling_price['cgst'];
                      $sgst = $selling_price['sgst'];
                      ?>
                        <?php if ($igst > 0) : ?>
                          <tr class="active">
                            <td class="w-80 text-right td-thick-border">
                              <?php 
                                echo $language->get('label_igst'); 
                              ?>
                            </td>
                            <td class="w-20 text-right td-thick-border">
                              <?php echo currency_format($igst); ?>
                            </td>
                          </tr>
                        <?php endif; ?>

                        <?php if ($cgst > 0) : ?>
                          <tr class="active">
                            <td class="w-80 text-right td-thick-border">
                              <?php 
                                echo $language->get('label_cgst'); 
                              ?>
                            </td>
                            <td class="w-20 text-right td-thick-border">
                              <?php echo currency_format($cgst); ?>
                            </td>
                          </tr>
                        <?php endif; ?>

                        <?php if ($sgst > 0) : ?>
                          <tr class="active">
                            <td class="w-80 text-right td-thick-border">
                              <?php 
                                echo $language->get('label_sgst'); 
                              ?>
                            </td>
                            <td class="w-20 text-right td-thick-border">
                              <?php echo currency_format($sgst); ?>
                            </td>
                          </tr>
                        <?php endif; ?>
                      <?php endif; ?>
                      <tr class="active">
                      	<td class="w-80 text-right">
                          <?php echo $language->get('label_payable_amount'); ?>
                        </td>
                      	<td class="w-20 text-right">
                          <?php echo currency_format($selling_price['payable_amount']); ?>
                        </td>
                      </tr>
                      <tr class="active">
                        <td class="w-80 text-right">
                          <?php echo $language->get('label_paid_amount'); ?>
                        </td>
                        <td class="w-20 text-right">
                          <?php echo currency_format($selling_price['paid_amount']); ?>
                        </td>
                      </tr>
                      <tr class="<?php echo $selling_price['due'] > 0 ? 'danger' : 'active';?>">
                        <td class="w-80 text-right">
                          <?php echo $language->get('label_due'); ?>
                        </td>
                        <td class="w-20 text-right">
                          <?php echo currency_format($selling_price['due']); ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody>
                      <?php if (!empty($payment_model->getPayments($invoice_id))) : ?>
                        <?php 
                        foreach ($payment_model->getPayments($invoice_id) as $row) : 
                          if ($row['type'] == 'return') {
                            $color = 'danger';
                          } elseif ($row['type'] == 'change') {
                            $color = 'info';
                          } elseif ($row['type'] == 'discount') {
                            $color = 'warning';
                          } else {
                            $color = 'success';
                          }
                          ?>
                          <tr class="bt-1 <?php echo $color;?>">
                            <td class="w-40 text-right">
                              <?php if ($row['type'] == 'return') : ?>
                                <small><i>Return on</i></small> <?php echo $row['created_at'];?> <small><i>by</i></small> <?php echo get_the_user($row['created_by'], 'username');?>
                              <?php elseif ($row['type'] == 'change') : ?>
                                <small><i>Change on</i></small> <?php echo $row['created_at'];?> <small><i>by</i></small> <?php echo get_the_user($row['created_by'], 'username');?>
                              <?php elseif ($row['type'] == 'discount') : ?>
                                <small><i>Discount on</i></small> <?php echo $row['created_at'];?> <small><i>by</i></small> <?php echo get_the_user($row['created_by'], 'username');?>
                              <?php elseif ($row['type'] == 'due_paid') : ?>
                                <small><i>Duepaid on</i></small> <?php echo $row['created_at'];?> 
                                <?php if ($row['pmethod_id']) : ?>
                                (via <?php echo get_the_pmethod($row['pmethod_id'], 'name');?>)
                                <?php endif; ?>
                                by <?php echo get_the_user($row['created_by'], 'username');?>
                              <?php else : ?>
                                <small><i>Paid on</i></small> <?php echo $row['created_at'];?> 
                                <?php if ($row['pmethod_id']) : ?>
                                (via <?php echo get_the_pmethod($row['pmethod_id'], 'name');?>)
                                <?php endif; ?>
                                by <?php echo get_the_user($row['created_by'], 'username');?>
                              <?php endif; ?>
                            </td>
                            <td class="w-30 text-right">
                              <?php if ($row['type'] == 'return') : ?>
                                <?php echo $language->get('label_amount'); ?>:&nbsp; <?php echo currency_format($row['amount']); ?>
                              <?php elseif ($row['type'] == 'change') : ?>
                                &nbsp;
                              <?php else : ?>
                                <?php echo $language->get('label_amount'); ?>:&nbsp; <?php echo currency_format($row['total_paid']); ?>
                              <?php endif; ?>
                            </td>
                            <td class="w-30 text-right">
                              <?php if ($row['type'] != 'return' && $row['pos_balance'] > 0) : ?>
                                <?php echo $language->get('label_change'); ?>:&nbsp; <?php echo currency_format($row['pos_balance']); ?>
                              <?php else: ?>
                                &nbsp;
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

                <?php if (get_preference('invoice_view') != 'standard'):?>
                  <div class="text-center"><h5><b><?php echo $language->get('text_tax_summary');?></b></h5></div>
                  <table class="table table-bordered table-striped print-table order-table table-condensed mb-0">
                    <thead>
                      <tr class="active">
                        <th class="w-35 text-center"><?php echo sprintf($language->get('label_name'),null); ?></th>
                        <th class="w-20 text-center"><?php echo sprintf($language->get('label_code'),null); ?></th>
                        <th class="w-15 text-center"><?php echo $language->get('label_qty');?></th>
                        <th class="w-15 text-right"><?php echo $language->get('label_tax_excl');?></th>
                        <th class="w-15 text-right"><?php echo $language->get('label_tax_amt');?></th>
                      </tr>
                    </thead>
                    <tbody>

                      <?php $gtotal = 0; foreach($taxes as $tax) : ?>
                      <tr>
                        <td class="text-center"><?php echo $tax['taxrate_name'];?></td>
                        <td class="text-center"><?php echo $tax['taxrate_code'];?></td>
                        <td class="text-center"><?php echo $tax['qty'];?></td>
                        <td class="text-right"><?php echo currency_format($tax['total'] - $tax['item_tax']);?></td>
                        <td class="text-right"><?php echo currency_format($tax['item_tax']);?></td>
                      </tr>
                      <?php $gtotal += $tax['tax']; endforeach; ?>

                      </tbody>
                      <tfoot>
                        <tr class="active">
                          <th colspan="4" class="text-right"><?php echo $language->get('label_total_tax_amount');?></th>
                          <th class="text-right"><?php echo currency_format($gtotal);?></th>
                        </tr>
                    </tfoot>
                  </table>
                <?php endif; ?>

                <?php if ($invoice_note) : ?>
                  <p class="text-center mb-0">
                    <i><?php echo $invoice_note; ?></i>
                  </p>
                <?php endif; ?>

                <?php if (get_preference('invoice_footer_text')) : ?>
                  <p class="text-center">
                    <i><?php echo get_preference('invoice_footer_text'); ?></i>
                  </p>
                <?php endif; ?>

                <div class="invoice-header-info barcodes">
                  <img src="../storage/qrcode.png">
                </div>

                <div class="table-responsive">
                  <table class="table">
                    <tbody>
                      <tr class="invoice-authority-cotainer">
                        <td class="w-50">
                          <div class="invoice_authority invoice_created_by">
                            <div class="name">
                              <?php echo $user->getUserName($invoice_info['created_by']); ?>
                            </div>
                            <div>
                              <?php echo $language->get('text_created_by'); ?>
                            </div>
                          </div>
                        </td>
                        <td class="w-50">
                          <div class="invoice_authority invoice_created_by">
                            <div class="name">
                              <?php echo $user->getUsername(store('cashier_id')); ?>
                            </div>
                            <div>
                              <?php echo $language->get('text_cashier'); ?>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="table-responsive footer-actions">
                  <table class="table">
                    <tbody>
                      <tr class="no-print">
                        <td colspan="2">
                          <button onClick="window.print();" class="btn btn-info btn-block">
                            <span class="fa fa-fw fa-print"></span> 
                            <?php echo $language->get('button_print'); ?>
                          </button>
                        </td>
                      </tr>
                      <?php if (($user->getGroupId() == 1 || $user->hasPermission('access', 'send_sms')) && get_preference('sms_alert')) : ?>
                        <tr class="no-print">
                          <td colspan="2">
                            <button id="sms-btn" data-invoiceid="<?php echo $invoice_id; ?>" class="btn btn-danger btn-block">
                              <span class="fa fa-fw fa-comment-o"></span> 
                              <?php echo $language->get('button_send_sms'); ?>
                            </button>
                          </td>
                        </tr>
                      <?php endif; ?>
                      <tr class="no-print">
                        <td colspan="2">
                          <button id="email-btn" data-customerName="<?php echo $customer_name; ?>" class="btn btn-success btn-block">
                            <span class="fa fa-fw fa-envelope-o"></span> 
                            <?php echo $language->get('button_email'); ?>
                          </button>
                        </td>
                      </tr>
                      <tr class="no-print">
                        <td colspan="2">
                          <a class="btn btn-default btn-block" href="pos.php">
                            &larr; <?php echo $language->get('button_back_to_pos'); ?>
                          </a>
                        </td>
                      </tr>
                      <tr class="text-center">
                        <td colspan="2">
                          <br>
                          <p class="powered-by">
                            <small>&copy; <a href="http://itsolution24.com">ITsolution24.com</a></small>
                          </p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

              </div>
            </div> 
    		  </div> 
        </div>
      </div>
    </div>
  </section>
  <!-- Content End-->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>