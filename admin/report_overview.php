<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_overview_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('overview_report');

// Set Document Title
$document->setTitle($language->get('title_overview'));

// ADD BODY CLASS
$document->setBodyClass('sidebar-collapse');

// Include Header and Footer
include ("header.php"); 
include ("left_sidebar.php");
?>

<!-- Content Wrapper Start -->
<div id="overview-report" class="content-wrapper">

  <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_overview_title'); ?>
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
        <?php echo $language->get('text_overview_title'); ?>
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
        <div class="box box-success box-no-border">

          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs store-m15">
              <li class="active">
                  <a href="#sell_overview" data-toggle="tab" aria-expanded="false">
                  <?php echo $language->get('text_sell_overview'); ?>
                </a>
              </li>
              <li class="">
                  <a href="#buy_overview" data-toggle="tab" aria-expanded="false">
                  <?php echo $language->get('text_buy_overview'); ?>
                </a>
              </li>
            </ul>
            <div class="tab-content">

              <div class="tab-pane active" id="sell_overview">
                <?php include '../_inc/template/partials/report_sell_overview.php'; ?>
              </div>
             
              <div class="tab-pane" id="buy_overview">
                <?php include '../_inc/template/partials/report_buy_overview.php'; ?>
              </div>
                
            </div>
          </div>
            
          <!-- </div> -->
        </div>
      </div>
    </div>
  </section>
  <!-- Content End -->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>