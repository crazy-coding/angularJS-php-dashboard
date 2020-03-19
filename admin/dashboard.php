<?php 
ob_start();
session_start();
include realpath(__DIR__.'/../').'/_init.php';

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

//  Load Language Files
$language->load('dashboard');
$language->load('menu');

// Set Document Title
$document->setTitle($language->get('title_dashboard'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/DashboardController.js');
$document->addScript('../assets/itsolution24/angular/controllers/ReportCollectionController.js');

// ADD BODY CLASS
$document->setBodyClass('dashboard'); 

$banking_model = $registry->get('loader')->model('banking');
$invoice_model = $registry->get('loader')->model('invoice');

// Include Header and Footer
include ("header.php");
include ("left_sidebar.php");
?>
<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="DashboardController">

    <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_dashboard'); ?>
      <small>
        <?php echo store('name'); ?>
      </small>
    </h1>
    <ol class="breadcrumb">
      <li>
        <a class="text-red" href="dashboard-old.php">
          <?php echo $language->get('text_old_dashboard'); ?> &rarr;
        </a>
      </li>
    </ol>
  </section>
  <!-- ContentH eader End -->

  <!-- Content Start -->
  <section class="content">

    <?php if(DEMO || settings('is_update_available')) : ?>
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-body">
            <?php if (settings('is_update_available')) : ?>
            <div class="alert alert-warning mb-0">
              <p><span class="fa fa-fw fa-info-circle"></span> Version <span class="label label-info"><?php echo settings('update_version');?></span> is available now. <a href="<?php echo settings('update_link');?>" target="_blink">Read changelog & update instructions here</a></p>
            </div>
            <?php endif; ?>
            <?php if (DEMO) : ?>
            <div class="alert alert-info mb-0">
              <p><span class="fa fa-fw fa-info-circle"></span> <?php echo $language->get('text_demo'); ?></p>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div class="hidden-xs action-button-sm">
    <?php include '../_inc/template/partials/action_buttons.php'; ?>
    </div>

    <hr>
    
    <!-- Small Boxes Start -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div id="invoice-count" class="small-box bg-green">
          <div class="inner">
            <h4>
              <i><?php echo $language->get('text_total_invoice'); ?>:</i> <span class="total-invoice"><?php echo total_invoice(from(), to()); ?></span>
            </h4>
            <h4>
              <i><?php echo $language->get('text_total_invoice_today'); ?>:</i> <span class="total-invoice"><?php echo total_invoice_today(); ?></span>
            </h4>
          </div>
          <div class="icon">
            <i class="fa fa-pencil"></i>
          </div>
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_customer')) : ?>
            <a href="invoice.php" class="small-box-footer">
              <?php echo $language->get('text_details'); ?> 
              <i class="fa fa-arrow-circle-right"></i>
            </a>
          <?php else:?>
            <a href="#" class="small-box-footer">
              &nbsp;
            </a>
          <?php endif;?>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6">
        <div id="customer-count" class="small-box bg-red">
          <div class="inner">
            <h4>
              <i><?php echo $language->get('text_total_customer'); ?>:</i> <span class="total-invoice"><?php echo total_customer(from(), to()); ?></span>
            </h4>
            <h4>
              <i><?php echo $language->get('text_total_customer_today'); ?>:</i> <span class="total-invoice"><?php echo total_customer_today(); ?></span>
            </h4>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_customer')) : ?>
            <a href="customer.php" class="small-box-footer">
              <?php echo $language->get('text_details'); ?> 
              <i class="fa fa-arrow-circle-right"></i>
            </a>
          <?php else:?>
            <a href="#" class="small-box-footer">
              &nbsp;
            </a>
          <?php endif;?>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6">
        <div id="supplier-count" class="small-box bg-purple">
          <div class="inner">
            <h4>
              <i><?php echo $language->get('text_total_supplier'); ?>:</i> <span class="total-invoice"><?php echo total_supplier(from(), to()); ?></span>
            </h4>
            <h4>
              <i><?php echo $language->get('text_total_supplier_today'); ?>:</i> <span class="total-invoice"><?php echo total_supplier_today(); ?></span>
            </h4>
          </div>
          <div class="icon">
            <i class="fa fa-fw fa-shopping-cart"></i>
          </div>
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_supplier')) : ?>
            <a href="supplier.php" class="small-box-footer">
              <?php echo $language->get('text_details'); ?> 
              <i class="fa fa-arrow-circle-right"></i>
            </a>
          <?php else:?>
            <a href="#" class="small-box-footer">
              &nbsp;
            </a>
          <?php endif;?>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6">
        <div id="product-count" class="small-box bg-yellow">
          <div class="inner">
            <h4>
              <i><?php echo $language->get('text_total_product'); ?>:</i> <span class="total-invoice"><?php echo total_product(from(), to()); ?></span>
            </h4>
            <h4>
              <i><?php echo $language->get('text_total_product_today'); ?>:</i> <span class="total-invoice"><?php echo total_product_today(); ?></span>
            </h4>
          </div>
          <div class="icon">
            <i class="fa fa-star"></i>
          </div>
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_product')) : ?>
            <a href="product.php" class="small-box-footer">
              <?php echo $language->get('text_details'); ?> 
              <i class="fa fa-arrow-circle-right"></i>
            </a>
          <?php else:?>
            <a href="#" class="small-box-footer">
              &nbsp;
            </a>
          <?php endif;?>
        </div>
      </div>
    </div>
    <!--Small Box End -->

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_report')) : ?>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-footer">
            <div class="row">
              <div class="col-lg-8 col-xs-12">
                <div class="box box-default banking-box">
                  <div class="box-header with-border">
                    <h3 class="box-title"><?php echo $language->get('text_recent_invoices'); ?></h3>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table no-margin">
                        <thead>
                          <tr>
                            <th><?php echo $language->get('label_invoice_id'); ?></th>
                            <th><?php echo $language->get('label_created_at'); ?></th>
                            <th><?php echo $language->get('label_customer_name'); ?></th>
                            <th><?php echo $language->get('label_amount'); ?></th>
                            <th><?php echo $language->get('label_status'); ?></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if ($invoices = $invoice_model->getInvoices('sell', store_id(), 3)) : ?>
                            <?php foreach ($invoices as $row) : ?>
                              <tr>
                                <td><a href="<?php echo root_url();?>/admin/view_invoice.php?invoice_id=<?php echo $row['invoice_id'];?>"><?php echo $row['invoice_id'];?></a></td>
                                <td><?php echo $row['created_at'];?></td>
                                <td><?php echo get_the_customer($row['customer_id'], 'customer_name');?></td>
                                <td><?php echo currency_format($row['payable_amount']);?></td>
                                <td><?php echo $row['payment_status'] = 'paid' ? '<span class="label label-success">Paid</span>' : '<span class="label label-danger">Due</span>';?></span></td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="box-footer clearfix">
                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_invoice')) : ?>
                      <a href="<?php echo root_url();?>/admin/pos.php" class="btn btn-xs btn-info btn-flat"><span class="fa fa-fw fa-plus"></span> <?php echo $language->get('button_create_new'); ?></a>
                    <?php endif; ?>
                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_invoice_list')) : ?>
                      <a href="<?php echo root_url();?>/admin/invoice.php" class="btn btn-xs btn-success btn-flat"><span class="fa fa-fw fa-list"></span> <?php echo $language->get('button_view_all'); ?></a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-xs-12" style="padding-right: 15px">
                <div class="progress-group">
                  <span class="progress-text"><?php echo $language->get('text_invoice_amount'); ?></span>
                  <span class="progress-number">
                    <?php 
                    $invoice_amount = selling_price(from(), to());
                    $discount_amount = discount_amount(from(), to());
                    $due_amount = due_amount(from(), to());
                    $received_amount = received_amount(from(), to());
                    echo currency_format($invoice_amount);?></span>
                  <div class="progress sm">
                    <div class="progress-bar progress-bar-aqua" style="width: <?php echo get_progress_percentage($invoice_amount, $discount_amount+$due_amount);?>%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  <span class="progress-text"><?php echo $language->get('text_discount_given'); ?></span>
                  <span class="progress-number"><?php echo currency_format($discount_amount); ?></span>
                  <div class="progress sm">
                    <div class="progress-bar progress-bar-warning" style="width:<?php echo ($discount_amount/$invoice_amount)*100;?>%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  <span class="progress-text"><?php echo $language->get('text_due_given'); ?></span>
                  <span class="progress-number"><?php echo currency_format($due_amount); ?></span>
                  <div class="progress sm">
                    <div class="progress-bar progress-bar-red" style="width: <?php echo ($due_amount/$invoice_amount)*100;?>%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  <span class="progress-text"><?php echo $language->get('text_received_amount'); ?></span>
                  <span class="progress-number"><?php echo currency_format($received_amount); ?></span>
                  <div class="progress sm">
                    <div class="progress-bar progress-bar-success" style="width: <?php echo ($received_amount/$invoice_amount)*100;?>%"></div>
                  </div>
                </div>
                <a href="<?php root_url();?>/admin/report_overview.php" class="btn btn-sm btn-block btn-warning btn-flat"><?php echo $language->get('button_overview_report'); ?> &rarr;</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> 
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_accounting_report')) : ?>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-body deposit-today">
            <div class="row">
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <h5 class="description-header"><?php echo currency_format(get_bank_deposit_amount(date('Y-m-d'), date('Y-m-d')));?></h5>
                  <h6 class="description-text"><?php echo $language->get('text_deposit_today'); ?></h6>
                </div>
              </div>
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <h5 class="description-header"><?php echo currency_format(get_bank_withdraw_amount(date('Y-m-d'), date('Y-m-d')));?></h5>
                  <h6 class="description-text"><?php echo $language->get('text_withdraw_today'); ?></h6>
                </div>
              </div>
              <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                  <h5 class="description-header"><?php echo currency_format(get_bank_deposit_amount(from(), to()));?></h5>
                  <h6 class="description-text"><?php echo $language->get('text_total_deposit'); ?></h6>
                </div>
              </div>
              <div class="col-sm-3 col-xs-6">
                <div class="description-block">
                  <h5 class="description-header"><?php echo currency_format(get_bank_withdraw_amount(from(), to()));?></h5>
                  <h6 class="description-text"><?php echo $language->get('text_total_withdraw'); ?></h6>
                </div>
              </div>
            </div>
          </div>
          <div class="box-footer">
            <div class="row">
              <div class="col-lg-6 col-xs-12">
                <div class="box box-default banking-box">
                  <div class="box-header with-border">
                    <h3 class="box-title"><?php echo $language->get('text_recent_deposit'); ?></h3>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table no-margin">
                        <thead>
                          <tr>
                            <th class="w-30"><?php echo $language->get('label_date'); ?></th>
                            <th class="w-40"><?php echo $language->get('label_description'); ?></th>
                            <th class="w-20 text-right"><?php echo $language->get('label_amount'); ?></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if ($transactions = $banking_model->getTransactions('deposit', store_id(), 3)) : ?>
                            <?php foreach ($transactions as $row) : ?>
                              <tr>
                                <td><?php echo $row['created_at'];?></td>
                                <td><a class="view-deposit" data-refno="<?php echo $row['ref_no'];?>" href="#"><?php echo $row['title'];?></a></td>
                                <td class="text-right"><?php echo currency_format($row['amount']);?></td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="box-footer clearfix">
                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'deposit')) : ?>
                      <a ng-click="BankingDepositModal()" onClick="return false;" href="javascript:void(0)" class="btn btn-xs btn-info btn-flat"><span class="fa fa-fw fa-plus"></span> <?php echo $language->get('button_deposit_now'); ?></a>
                    <?php endif; ?>
                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_bank_transactions')) : ?>
                      <a href="<?php echo root_url();?>/admin/bank_transactions.php" class="btn btn-xs btn-success btn-flat"><span class="fa fa-fw fa-list"></span> <?php echo $language->get('button_view_all'); ?></a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-xs-12" style="padding-right: 15px">
                <div class="box box-default banking-box">
                  <div class="box-header with-border">
                    <h3 class="box-title"><?php echo $language->get('text_recent_withdraw'); ?></h3>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table no-margin">
                        <thead>
                          <tr>
                            <th class="w-30"><?php echo $language->get('label_date'); ?></th>
                            <th class="w-40"><?php echo $language->get('label_description'); ?></th>
                            <th class="w-20 text-right"><?php echo $language->get('label_amount'); ?></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if ($transactions = $banking_model->getTransactions('withdraw', store_id(), 3)) : ?>
                            <?php foreach ($transactions as $row) : ?>
                              <tr>
                                <td><?php echo $row['created_at'];?></td>
                                <td><a class="view-deposit" data-refno="<?php echo $row['ref_no'];?>" href="#"><?php echo $row['title'];?></a></td>
                                <td class="text-right"><?php echo currency_format($row['amount']);?></td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="box-footer clearfix">
                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'withdraw')) : ?>
                      <a ng-click="BankingWithdrawModal()" onClick="return false;" href="javascript:void(0)" class="btn btn-xs btn-danger btn-flat"><span class="fa fa-fw fa-minus"></span> <?php echo $language->get('button_withdraw_now'); ?></a>
                    <?php endif; ?>
                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_bank_transactions')) : ?>
                      <a href="<?php echo root_url();?>/admin/bank_transactions.php" class="btn btn-xs btn-success btn-flat"><span class="fa fa-fw fa-list"></span> <?php echo $language->get('button_view_all'); ?></a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>   

    <!-- Collection Report Start -->
    <div class="row collection-report-container">
      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_collection_report')) : ?>
          <div id="collection-report" class="col-md-12">
            <div class="box box-info mb-0">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <?php echo $language->get('text_collection_report'); ?>
                </h3>
              </div>
              
              <div class="dashboard-widget box-body">
                <?php include('../_inc/template/partials/report_collection.php'); ?>
              </div>
            </div>
          </div>
        <?php endif; ?>  
    </div>
    <!-- Collection Report End -->

    <hr>

    <div class="row">
      <div class="col-md-6">
        <div class="box box-info tour-item" id="profit-calculation">
          <div class="box-header with-border">
            <h3 class="box-title">
              <?php echo $language->get('text_report_title'); ?> 
              <small>of</small> 
              <?php echo date("F j, Y", strtotime(date('Y-m-d'))); ?>
            </h3>
          </div>
          <div class="box-body">
            <?php include '../_inc/template/partials/profit_calc.php'; ?>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box box-info tour-item" id="recent_customer">
          <div class="box-header with-border">
            <h3 class="box-title">
              <?php echo $language->get('text_recent_customer_box_title'); ?>
            </h3>
          </div>
          <div class="box-body w-recent-customer">
            <?php if (count(recent_customers(1)) > 0) : ?>
              <ul class="unstyled list-group recent-cusomers">
                <?php foreach (recent_customers(5) as $customer) :
                  if ($customer['customer_mobile'] && !empty($customer['customer_mobile'])) {
                    $customer_contact = '('.$customer['customer_mobile'].')';
                  } else if ($customer['customer_email'] && !empty($customer['customer_email'])) {
                    $customer_contact = '('.$customer['customer_email'].')';
                  } ?>
                  <li class="list-group-item bg-blue">
                    <a class="recent-customer-name" href="customer_profile.php?customer_id=<?php echo $customer['customer_id'];?>">
                      <i class="fa fa-fw fa-user"></i>
                      <?php echo limit_char($customer['customer_name'], 30); ?> 
                      <?php echo $customer_contact; ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="not-found">
                <?php echo sprintf($language->get('text_not_found'), $language->get('text_customer')); ?>
              </p>
            <?php endif; ?>
          </div>
          <div class="box-footer text-center">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_customer')) : ?>
              <a href="customer.php">
                <?php echo $language->get('text_details'); ?> 
                <span class="fa fa-arrow-circle-right"></span>
              </a>
            <?php else:?>
                &nbsp;
            <?php endif;?>
          </div>
        </div>
      </div>
    </div>
    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'show_graph')) : ?>
      <div class="row">
        <div class="col-md-12 tour-item" id="buy_sell_comparison">
          <?php include '../_inc/template/partials/sell_analytics.php'; ?>
        </div>
      </div>
    <?php endif; ?>
  </section>
  <!-- Content End -->
</div>
<!-- Content Wrapper End -->
    
<?php include ("footer.php"); ?>