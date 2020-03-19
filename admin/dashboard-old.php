<?php 
ob_start();
session_start();
include '../_init.php';

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
        <a class="text-green" href="dashboard.php">
          <b>&larr; <?php echo $language->get('text_new_dashboard'); ?></b>
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
    
    <!-- Small Boxes Start -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div id="invoice-count" class="small-box bg-green">
          <div class="inner">
            <h3>
              <?php echo total_invoice(from(), to()); ?>
            </h3>
            <p>
              <?php echo $language->get('text_total_invoice'); ?>
              <?php if (!from()) : ?>
                <?php echo '(Today)';?>
              <?php endif; ?>
            </p>
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
            <h3>
              <?php echo total_customer(from(), to()); ?>
            </h3>
            <p>
              <?php echo $language->get('text_total_customer'); ?>
            </p>
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
            <h3>
              <?php echo total_supplier(); ?>
            </h3>
            <p>
              <?php echo $language->get('text_total_supplier'); ?>
            </p>
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
            <h3>
              <?php echo total_product(); ?>
            </h3>
            <p>
              <?php echo $language->get('text_total_product'); ?>
            </p>
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

    <?php include '../_inc/template/partials/action_buttons.php'; ?><hr>

    <!-- Collection Report Start -->
    <div class="row collection-report-container">
      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_collection_report')) : ?>
          
          <!-- Collection Report Start -->
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