<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_loan')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('loan');

// Set Document Title
$document->setTitle($language->get('title_loan'));

// Add Script
$document->addScript('../assets/itsolution24/angular/modals/LoanPayModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanViewModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanCreateModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanEditModal.js');
$document->addScript('../assets/itsolution24/angular/modals/LoanDeleteModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/LoanController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="LoanController">

  <!-- Content Header Start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_loan_title'); ?>
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
        <?php echo $language->get('text_loan_title'); ?>
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
    
    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'take_loan')) : ?>
      <div class="box box-info<?php echo create_box_state(); ?>">
        <div class="box-header with-border">
          <h3 class="box-title">
            <span class="fa fa-fw fa-plus"></span> <?php echo $language->get('text_take_loan_title'); ?>
          </h3>
          <button type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
            <i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-plus'; ?>"></i>
          </button>
        </div>
      
        <?php if (isset($error_message)): ?>
          <div class="alert alert-danger">
            <p>
              <span class="fa fa-warning"></span> 
              <?php echo $error_message ; ?>
            </p>
          </div>
        <?php elseif (isset($success_message)): ?>
          <div class="alert alert-success">
            <p>
              <span class="fa fa-check"></span> 
              <?php echo $success_message ; ?>
            </p>
          </div>
        <?php endif; ?>

        <!-- Add Expend Create Form -->
        <?php include('../_inc/template/loan_take_form.php'); ?>
        
      </div>
    <?php endif; ?>

    <div class="row">

      <!-- Expense List Section Start-->
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_loan_list_title'); ?>
            </h3>
            <div class="box-tools pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-info">
                  <span class="fa fa-filter"></span> 
                  <?php if (isset($request->get['type']) && $request->get['type'] == 'paid') : ?>
                    <?php echo $language->get('text_paid'); ?>
                  <?php elseif (isset($request->get['type']) && $request->get['type'] == 'due') : ?>
                    <?php echo $language->get('text_due'); ?>
                  <?php elseif (isset($request->get['type']) && $request->get['type'] == 'disabled') : ?>
                    <?php echo $language->get('text_disabled'); ?>
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
                      <a href="loan.php">
                        <?php echo $language->get('button_all'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="loan.php?type=paid">
                        <?php echo $language->get('button_paid'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="loan.php?type=due">
                        <?php echo $language->get('button_due'); ?>
                      </a>
                    </li>
                    <li>
                      <a href="loan.php?type=disabled">
                        <?php echo $language->get('button_disabled'); ?>
                      </a>
                    </li>
                 </ul>
              </div>
            </div>
          </div>
          <div class='box-body'>     
            <?php
              $hide_colums = "";
              if ($user->getGroupId() != 1) {
                if (! $user->hasPermission('access', 'loan_pay')) {
                  $hide_colums .= "8,";
                }
                if (! $user->hasPermission('access', 'view_loan')) {
                  $hide_colums .= "9,";
                }
                if (! $user->hasPermission('access', 'update_loan')) {
                  $hide_colums .= "10,";
                }
                if (! $user->hasPermission('access', 'delete_loan')) {
                  $hide_colums .= "11,";
                }
              }
            ?> 
            <div class="table-responsive">                     
              <table id="loan-loan-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo $language->get('label_datetime'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_loan_from'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_basic_amount'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_interest'); ?> (%)
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_payable'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_paid'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_due'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_pay'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_view'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_edit'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_delete'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo $language->get('label_datetime'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_loan_from'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_basic_amount'); ?>
                    </th>
                    <th class="w-10">&nbsp;</th>
                    <th class="w-10">
                      <?php echo $language->get('label_payable'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_paid'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_due'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_pay'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_view'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_edit'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_delete'); ?>
                    </th>
                  </tr>
                </tfoot>
              </table>    
            </div>
          </div>
        </div>
      </div>
       <!-- Expense List Section End-->
    </div>
  </section>
  <!-- Content End -->
</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>