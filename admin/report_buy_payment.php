<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_buy_payment_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('report');

// Set Document Title
$document->setTitle($language->get('title_buy_payment_report'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/ReportBuyPaymentController.js');

// ADD BODY CLASS
$document->setBodyClass('sidebar-collapse');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="ReportBuyPaymentController">

  <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_buy_payment_report_title'); ?>
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
        <?php echo $language->get('text_buy_payment_report_title'); ?>
      </li>
    </ol>
  </section>
  <!-- Content Header End -->

  <!-- Content Start Start -->
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
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_buy_payment_report_sub_title'); ?>
            </h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <table id="report-report-list" class="table table-bordered table-striped table-hover">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-10">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_date'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_type'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_invoice_id'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo $language->get('label_pmethod_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_note'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_amount'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
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