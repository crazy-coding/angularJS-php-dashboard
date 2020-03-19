<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_expense')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('expense');

// Set Document Title
$document->setTitle($language->get('title_expense'));

// Add Script
$document->addScript('../assets/itsolution24/angular/modals/ExpenseViewModal.js');
$document->addScript('../assets/itsolution24/angular/modals/ExpenseEditModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/ExpenseController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper">

  <!-- Content Header Start -->
  <section class="content-header" ng-controller="ExpenseController">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_expense_title'); ?>
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
        <?php echo $language->get('text_expense_title'); ?>
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
    
    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_expense')) : ?>
      <div class="box box-info<?php echo create_box_state(); ?>">
        <div class="box-header with-border">
          <h3 class="box-title">
            <span class="fa fa-fw fa-plus"></span> <?php echo $language->get('text_new_expense_title'); ?>
          </h3>
          <button type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
            <i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-plus'; ?>"></i>
          </button>
        </div>

        <!-- Add Expend Create Form -->
        <?php include('../_inc/template/expense_create_form.php'); ?>
        
      </div>
    <?php endif; ?>

    <div class="row">

      <!-- Expense List Section Start-->
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_expense_list_title'); ?>
            </h3>
          </div>
          <div class='box-body'>     
            <?php
              $hide_colums = "";
              if ($user->getGroupId() != 1) {
                if (! $user->hasPermission('access', 'read_expense')) {
                  $hide_colums .= "5,";
                }
                if (! $user->hasPermission('access', 'update_expense')) {
                  $hide_colums .= "6,";
                }
                if (! $user->hasPermission('access', 'delete_expense')) {
                  $hide_colums .= "7,";
                }
              }
            ?> 
            <div class="table-responsive">                     
              <table id="expense-expense-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo $language->get('label_serial_no'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_category_name'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_title'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_amount'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo $language->get('label_created_at'); ?>
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
                    <th class="w-20">
                      <?php echo $language->get('label_category_name'); ?>
                    </th>
                    <th class="w-20">
                      <?php echo $language->get('label_title'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_amount'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo $language->get('label_created_at'); ?>
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