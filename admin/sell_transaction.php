<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_sell_transaction')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('sell_transaction');

// Set Document Title
$document->setTitle($language->get('title_sell_transaction'));

// Add Script
$document->addScript('../assets/itsolution24/angular/modals/SellTransactionViewModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/SellTransactionController.js');

// ADD BODY CLASS
$document->setBodyClass('sidebar-collapse');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="SellTransactionController">

  <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_sell_transaction_title'); ?>
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
        <?php echo $language->get('text_sell_transaction_title'); ?>
      </li>
    </ol>
  </section>
  <!-- Content Header end -->

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

      <!-- SellTransaction List Section Start-->
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_sell_transaction_list_title'); ?>
            </h3>
            <div class="box-tools pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-info">
                    <span class="fa fa-fw fa-filter"></span> 
                    <?php if(isset($request->get['customer_id'])) : ?>
                      <?php echo get_the_customer($request->get['customer_id'],'customer_name'); ?>
                  <?php else : ?>
                    <?php echo $language->get('button_filter'); ?>
                  <?php endif; ?>
                </button>
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu" style="max-height: 350px;overflow-y:scroll;">
                  <li>
                    <a href="sell_transaction.php">
                      <?php echo sprintf($language->get('text_view_all'),''); ?>
                    </a>
                  </li>
                  <?php foreach(get_customers() as $the_customer) : ?>
                    <li>
                      <a href="sell_transaction.php?customer_id=<?php echo $the_customer['customer_id'];?>">
                        <?php echo $the_customer['customer_name']; ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                 </ul>
              </div>
          </div>
          </div>
          <div class='box-body'>     
            <?php
              $hide_colums = "";
              if ($user->getGroupId() != 1) {
                if (! $user->hasPermission('access', 'read_sell_transaction')) {
                  $hide_colums .= "7,";
                }
              }
            ?> 
            <div class="table-responsive">                     
              <table id="transaction-transaction-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_created_at'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_type'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_customer'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_pmethod'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_created_by'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_amount'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_view'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_created_at'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_type'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_customer'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_pmethod'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_created_by'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_amount'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_view'); ?>
                    </th>
                  </tr>
                </tfoot>
              </table>    
            </div>
          </div>
        </div>
      </div>
       <!-- SellTransaction List Section End-->
    </div>
  </section>
  <!-- Content End -->
</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>