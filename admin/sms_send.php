<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'send_sms')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('sms');

// Set Document Title
$document->setTitle($language->get('title_send_sms'));

// Add Script
$document->addScript('../assets/underscore/underscore.min.js');
$document->addScript('../assets/itsolution24/angular/controllers/SMSController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="SMSController">

  <!-- Content Header Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_sms_title'); ?>
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
        <?php echo $language->get('text_send_sms'); ?>
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
    
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">
          <span class="fa fa-fw fa-comment-o"></span> <?php echo $language->get('text_send_sms_title'); ?>
        </h3>
      </div>
      <div class="box-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs store-m15">
            <li class="active">
                <a href="#single" data-toggle="tab" aria-expanded="false">
                <?php echo $language->get('text_single'); ?>
              </a>
            </li>
            <li>
                <a href="#group" data-toggle="tab" aria-expanded="false">
                <?php echo $language->get('text_group'); ?>
              </a>
            </li>
          </ul>
          <div class="tab-content">

            <!-- single Setting Start -->
            <div class="tab-pane active" id="single">
              <?php include('../_inc/template/sms_send_form.php'); ?>
            </div> 
            <!-- single Setting End -->

            <!-- group Setting Start -->
            <div class="tab-pane" id="group">
              <?php include('../_inc/template/sms_send_group_form.php'); ?>
            </div> 
            <!-- group Setting End -->

          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- Content End -->
  
</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>