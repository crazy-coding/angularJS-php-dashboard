<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_loan_summary')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('loan');

// Set Document Title
$document->setTitle($language->get('title_loan_summary'));

// ADD BODY CLASS
$document->setBodyClass('sidebar-collapse');

$document->addScript('../assets/itsolution24/angular/controllers/LoanPaymentController.js');

// Include Header and Footer
include ("header.php"); 
include ("left_sidebar.php");
?>

<!-- Content Wrapper Start -->
<div id="loan_summary" class="content-wrapper" ng-controller="LoanPaymentController">

  <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_loan_summary_title'); ?>
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
        <?php echo $language->get('text_loan_summary_title'); ?>
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
        <div class="box box-success">
          <div class="box-body">
            <?php include '../_inc/template/partials/loan_summary.php'; ?>

            <h4><b><?php echo $language->get('text_recent_payments'); ?></b></h4>
            <hr class="margin-b20">
            <?php
              $hide_colums = "";
            ?> 
            <div class="table-responsive">                     
              <table id="payment-payment-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_datetime'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_ref_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_created_by'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_note'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_paid'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_datetime'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_ref_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_created_by'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_note'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_paid'); ?>
                    </th>
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