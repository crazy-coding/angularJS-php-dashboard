<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_sms_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('sms');

// Set Document Title
$document->setTitle($language->get('title_sms_report'));

// Add Script
$document->addScript('../assets/itsolution24/angular/modals/SMSResendModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/SMSReportController.js');

// ADD BODY CLASS
$document->setBodyClass('sidebar-collapse');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="SMSReportController">

  <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_sms_report_title'); ?>
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
        <?php echo $language->get('text_sms_report_title'); ?>
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
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_sms_list_title'); ?>
            </h3>
            <div class="box-tools pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-info">
                  <span class="fa fa-filter"></span> 
                  <?php if (isset($request->get['type']) && $request->get['type'] == 'pending') : ?>
                    <?php echo $language->get('text_pending'); ?>
                  <?php elseif (isset($request->get['type']) && $request->get['type'] == 'delivered') : ?>
                    <?php echo $language->get('text_delivered'); ?>
                  <?php elseif (isset($request->get['type']) && $request->get['type'] == 'failed') : ?>
                    <?php echo $language->get('text_failed'); ?>
                  <?php else : ?>
                    <?php echo $language->get('text_all'); ?>
                  <?php endif; ?>
                </button>
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="sms_report.php">
                        <?php echo $language->get('button_all'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="sms_report.php?type=pending">
                        <?php echo $language->get('button_pending'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="sms_report.php?type=delivered">
                        <?php echo $language->get('button_delivered'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="sms_report.php?type=failed">
                        <?php echo $language->get('button_failed'); ?>
                      </a>
                    </li>
                 </ul>
              </div>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                  $hide_colums = "";
                ?>
              <table id="sms-sms-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_schedule_at'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_campaign_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_people_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_mobile_number'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_process_status'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_response_text'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_delivered'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_resend'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_schedule_at'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_campaign_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_people_name'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_mobile_number'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_process_status'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_response_text'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_delivered'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_resend'); ?>
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