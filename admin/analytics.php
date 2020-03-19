<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_analytics')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// Set Document Title
$document->setTitle($language->get('title_analytics'));

// Add body class
$document->setBodyClass('sidebar-collapse');

// Include Header and Footer
include ("header.php"); 
include ("left_sidebar.php");
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper">

  <!-- Content Header Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_analytics_title'); ?>
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
        <?php echo $language->get('text_analytics_title'); ?>
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
      <!-- Todays Report Start -->
      <div class="col-md-6">
        <div class="box box-info">
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
          <div class="box-footer text-center">
             <a href="report_sell.php?p_day=<?php echo day(); ?>&amp;p_month=<?php echo month(); ?>&amp;p_year=<?php echo year(); ?>">
              <?php echo $language->get('text_details'); ?> <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- Todays Report End -->

      <!-- Best Customer Start -->
      <div class="col-md-6">
        <div id="best-customer" class="small-box bg-warning">
          <div class="inner">
            <h3 class="title">
              <?php echo $language->get('text_best_customer'); ?>
            </h3>
            <h2 class="name">
              <?php if (best_customer('customer_name')) : ?>
                <a href="customer_profile.php?customer_id=<?php echo best_customer('customer_id'); ?>">
                  <?php echo best_customer('customer_name'); ?>
                </a>
              <?php else : ?>
                No Customer Yet!
              <?php endif; ?>
            </h2>
            <div class="amount">
              <?php echo $language->get('text_purchase_amount'); ?> 
              <?php 
                $total = best_customer('total');
                echo '<strong>'.get_currency_symbol().currency_format($total).'</strong>';
              ?>
            </div>
            <?php if (best_customer('customer_mobile')) : ?>
              <div class="contact">
                <i><?php echo $language->get('label_mobile'); ?>: <?php echo best_customer('customer_mobile'); ?></i>
              </div>
            <?php endif; ?>
            <?php if (best_customer('customer_email')) : ?>
              <div class="contact">
                <i><?php echo sprintf($language->get('label_email'), null); ?>: <?php echo best_customer('customer_email'); ?></i>
               </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- Best Customer End -->
    </div>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'show_graph')) : ?>
    <div class="row">
      <div class="col-md-12">
          <?php include('../_inc/template/partials/sell_analytics.php'); ?>
      </div>
    </div>
    <?php endif; ?>

    <?php include('../_inc/template/partials/top_products.php'); ?>

</section>
<!-- Content End-->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>