<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_customer_profile')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language Files
$language->load('customer');
$language->load('customer_profile');

// LOAD CUSTOMER MODEL
$registry->get('loader')->model('customer');
$customer_model = $registry->get('model_customer');

// FETCH CUSTOMER INFO
$customer = $customer_model->getCustomer($request->get['customer_id']);
if (count($customer) <= 1) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/customer.php');
}

// Set Document Title
$document->setTitle($language->get('title_customer_profile'));

// Add ScriptS
$document->addScript('../assets/itsolution24/angular/controllers/CustomerController.js');
$document->addScript('../assets/itsolution24/angular/controllers/CustomerProfileController.js');

// ADD BODY CLASS 
$document->setBodyClass('sidebar-collapse customer-profile'); 

// Include Header and Footer
include 'header.php'; 
include 'left_sidebar.php';  
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="CustomerProfileController">

  <!-- Header Content Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo sprintf($language->get('text_profile_title'), $customer['customer_name']); ?>
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="dashboard.php">
          <i class="fa fa-dashboard"></i> 
          <?php echo $language->get('text_dashboard'); ?>
        </a>
      </li>
      <li>
        <a href="customer.php">
          <?php echo $language->get('text_customers'); ?>
        </a>
      </li>
      <li class="active">
        <?php echo $customer['customer_name']; ?>
      </li>
    </ol>
  </section>
  <!-- Header Content End -->

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
    
    <div class="row profile-heading">
      <!-- Profile Part Start -->
      <div class="col-sm-4 col-xs-12">
        <div class="box box-widget widget-user">
          <div class="widget-user-header bg-<?php echo $user->getPreference('base_color', 'black'); ?>">
            <h3 class="widget-user-username">
              <?php echo $customer['customer_name']; ?>
            </h3>
            <h5 class="widget-user-desc">
              <?php echo $language->get('text_since'); ?> 
              <?php echo format_date($customer['created_at']); ?>
            </h5>
          </div>
          <div class="widget-user-image">
            <!-- <img class="img-circle" src="../assets/itsolution24/img/<?php echo customer_avatar($customer['customer_sex']); ?>" alt="<?php echo $customer['customer_name']; ?>"> -->
            <svg class="svg-icon"><use href="#icon-<?php echo customer_avatar($customer['customer_sex']); ?>"></svg>
          </div>
          <div class="box-footer">
            <div class="row">
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">
                    <?php echo customer_total_invoice($customer['customer_id']); ?>
                  </h5>
                  <span class="description-text">
                    <?php echo $language->get('text_total_invoice'); ?>
                  </span>
                </div>
              </div>
              <div class="col-sm-4 border-right">
                <div class="description-block">
                  <h5 class="description-header">
                    <?php echo currency_format(customer_total_buying_amount($customer['customer_id'])); ?>
                  </h5>
                  <span class="description-text">
                    <?php echo $language->get('text_total_buying'); ?>
                  </span>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="description-block">

                  <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'update_customer')) : ?>
                    <button ng-click="customerEdit(<?php echo $customer['customer_id']; ?>, '<?php echo $customer['customer_name']; ?>')" title="<?php echo $language->get('button_edit'); ?>" class="btn btn-bg btn-info"><i class="fa fa-pencil"></i></button>
                  <?php endif; ?>

                  <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_invoice')) : ?>
                  <a id="sell-product" class="btn btn-bg btn-success" target="_blink" href="pos.php?customer_id=<?php echo $customer['customer_id']; ?>" title="<?php echo $language->get('button_sell'); ?>">
                    <i class="fa fa-shopping-cart"></i>
                  </a>
                  <?php endif; ?>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <!-- Profile Part End -->

    <!-- Contact Info Part Start -->
      <div class="col-sm-5 col-xs-12 contact">
        <div class="box box-info">
          <div class="box-header with-border text-center">
            <h3 class="box-title">
              <?php echo $language->get('text_contact_information'); ?>
            </h3>
          </div>
          <div class="box-body">
            <div class="well text-center">
              <address>
                <?php if ($customer['customer_mobile']) : ?>
                <h4>
                  <strong>
                    <?php echo $language->get('label_mobile_phone'); ?>:
                  </strong> 
                  <?php echo $customer['customer_mobile']; ?>
                </h4>
                <?php endif; ?>
                <?php if ($customer['customer_email']) : ?>
                  <h4>
                    <strong>
                      <?php echo $language->get('label_email'); ?>:
                    </strong> 
                    <?php echo $customer['customer_email']; ?>
                  </h4>
                <?php endif; ?>   
                <?php if ($customer['customer_address']) : ?>
                  <h4>
                    <strong>
                      <?php echo $language->get('label_address'); ?>:
                    </strong> 
                    <?php echo $customer['customer_address']; ?>
                  </h4>
                <?php endif; ?>  
              </address>
            </div>
          </div>
        </div>
      </div>
      <!-- Contact Info Part End -->

      <!-- Balance Part Start -->
      <div class="col-sm-3 col-xs-12 balance">
        <div class="info-box">
          <span class="info-box-icon bg-<?php echo $user->getPreference('base_color', 'black'); ?>">
            <i>
              <?php echo get_currency_symbol(); ?>
            </i>
          </span>
          <div class="info-box-content">
            <h2 class="info-box-text">
              <?php echo $language->get('label_balance'); ?>
            </h2>
            <?php $balance = $customer['balance']; ?>
            <span id="customer-due-amount" class="info-box-number">
              <?php echo currency_format($balance); ?>
            </span>
            <hr>
            <?php if ($balance > 0) : ?>
              <a target="_blink" href="invoice.php?type=due&customer_id=<?php echo $customer['customer_id']; ?>" class="btn btn-sm btn-success">
                <i class="fa fa-fw fa-plus"></i> 
                <?php echo $language->get('button_pay'); ?>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!-- Balance Part End -->
    </div>
    <div class="row">
      <!-- Customer Invoice List Start -->
      <div class="col-xs-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_invoice_list'); ?>
            </h3>
            <div class="box-tools pull-right">
              <div class="btn-group">
                <a type="button" class="btn btn-info" href="sell_transaction.php?customer_id=<?php echo $customer['customer_id'];?>"><?php echo $language->get('button_transaction_list'); ?></a>
              </div>
            </div>
          </div>
        	<div class='box-body'>     
            <?php
              $hide_colums = "";
              if ($user->getGroupId() != 1) {
                if (! $user->hasPermission('access', 'view_invoice')) {
                  $hide_colums .= "5,";
                }
              }
            ?> 
            <div class="table-responsive"> 
              <!-- Iinvoice List Start-->
              <table id="invoice-invoice-list" class="table table-bordered table-striped table-hovered" data-id="<?php echo $customer['customer_id']; ?>" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-20">
                      <?php echo $language->get('label_created_at'); ?>
                    </th>
                  	<th class="w-20">
                      <?php echo $language->get('label_invoice_id'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_payable_amount'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_paid_amount'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_due'); ?>
                    </th>
        						<th class="w-5">
                      <?php echo $language->get('label_view'); ?>
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
                  </tr>
                </tfoot>
              </table>		
              <!-- Invoice List End-->
            </div>
    		  </div> 
        </div>
      </div>
      <!-- Customer Invoice List End -->
    </div>
  </section>
  <!-- Content End-->
</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>