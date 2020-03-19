<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_expired_product')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('expired');

// Set Document Title
$document->setTitle($language->get('title_expired'));

// Add Script
$document->addScript('../assets/itsolution24/angular/modals/ProductEditModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/ExpiredProductController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="ExpiredProductController">

  <!-- Header Content Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_expired_title'); ?>
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
        <?php echo $language->get('text_expired_title'); ?>
      </li>
    </ol>
  </section>
  <!--Header Content End -->

  <!-- Start Content -->
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
      <div class="col-md-12">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">
                <?php echo $language->get('text_expired_box_title'); ?>
              </h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">

                <?php
                  $print_columns = '0,1,2,3,4,5,6,7';
                  if ($user->getGroupId() != 1) {
                    if (! $user->hasPermission('access', 'show_buy_price')) {
                      $print_columns = str_replace('5,', '', $print_columns);
                    }
                  }
                  $hide_colums = "";
                  if ($user->getGroupId() != 1) {
                    if (! $user->hasPermission('access', 'view_buy_price')) {
                      $hide_colums .= "5,";
                    }
                  }
                ?>
                
                <!-- Product List End -->
                <table id="product-product-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>" data-print-columns="<?php echo $print_columns;?>">
                  <thead>
                    <tr class="bg-gray">
                      <th class="w-5">
                        <?php echo sprintf($language->get('label_serial_no'),null); ?>
                      </th>
                      <th class="w-25">
                        <?php echo sprintf($language->get('label_name'), $language->get('label_product')); ?>
                      </th>
                      <th class="w-10">
                        <?php echo $language->get('label_supplier'); ?>
                      </th>
                      <th class="w-10">
                        <?php echo $language->get('label_mobile'); ?>
                      </th>
                      <th class="w-10">
                        <?php echo $language->get('label_box'); ?>
                      </th>
                      <th class="w-5">
                        <?php echo $language->get('label_buy_price'); ?>
                      </th>
                      <th class="w-5">
                        <?php echo $language->get('label_quantity'); ?>
                      </th>
                      <th class="w-25">
                        <?php echo $language->get('label_expired_date'); ?>
                      </th>
                      <th class="w-5">
                        <?php echo $language->get('label_edit'); ?>
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
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
                <!-- Product Product List End-->
              </div>
            </div>
          </div>
        </div>
      </div>
  </section>
  <!-- Content Header End -->
</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>