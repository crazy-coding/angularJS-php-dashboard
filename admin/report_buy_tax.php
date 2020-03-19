<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_buy_tax_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

$invoice_model = $registry->get('loader')->model('invoice');

//  Load Language File
$language->load('report');

// Set Document Title
$document->setTitle($language->get('title_buy_tax_report'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/ReportBuyTaxController.js');

// ADD BODY CLASS
$document->setBodyClass('sidebar-collapse');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="ReportBuyTaxController">

  <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_buy_tax_report_title'); ?>
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
      <li class="active">
        <?php echo $language->get('text_buy_tax_report_title'); ?>
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
      <div class="col-sm-12 tax-values">
        <div class="row">
          <div class="<?php echo get_preference('invoice_view') == 'indian_gst' ? 'col-sm-6' : 'col-sm-12';?>">
            <div class="row">
              <div class="col-sm-6">
                <div class="small-box bred">
                <h4><?php echo currency_format(buying_price(from(), to()));?></h4>
                <p><?php echo $language->get('text_buy_amount'); ?></p>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="small-box bpurple">
                  <h4><?php echo currency_format(get_buy_tax('item_tax', from(), to()));?></h4>
                  <p><?php echo $language->get('text_total_tax_amount'); ?></p>
                </div>
              </div>
            </div>
          </div>
          <?php if (get_preference('invoice_view') == 'indian_gst') : ?>
          <div class="col-sm-6">
            <div class="small-box col-sm-4 borange">
              <h4><?php echo currency_format(get_buy_tax('igst', from(), to()));?></h4>
              <p><?php echo $language->get('text_igst'); ?></p>
            </div>
            <div class="small-box col-sm-4 bolive">
              <h4><?php echo currency_format(get_buy_tax('cgst', from(), to()));?></h4>
              <p><?php echo $language->get('text_cgst'); ?></p>
            </div>
            <div class="small-box col-sm-4 borange">
              <h4><?php echo currency_format(get_buy_tax('sgst', from(), to()));?></h4>
              <p><?php echo $language->get('text_sgst'); ?></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <div class="clearfix"></div>
      </div>

      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_buy_tax_report_sub_title'); ?>
            </h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                  $print_columns = '0,1,2';
                  $hide_colums = "";
                ?>
              <table id="report-report-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>" data-print-columns="<?php echo $print_columns;?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-30">
                      <?php echo $language->get('label_created_at'); ?>
                    </th>
                    <th class="w-40">
                      <?php echo $language->get('label_invoice_id'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_tax_amount'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Content End -->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>