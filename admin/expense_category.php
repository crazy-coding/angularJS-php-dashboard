<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_expense_category')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('expense');

// Set Document Title
$document->setTitle($language->get('title_expense_category'));

// Add Script
$document->addScript('../assets/itsolution24/angular/modals/ExpenseCategoryEditModal.js');
$document->addScript('../assets/itsolution24/angular/modals/ExpenseCategoryDeleteModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/ExpenseCategoryController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php");
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="ExpenseCategoryController">

  <!-- Content Header Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_expense_category_title'); ?>
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
        <?php echo $language->get('text_expense_category_title'); ?>
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
    
    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_expense_category')) : ?>
      <div class="box box-info<?php echo create_box_state(); ?>">
        <div class="box-header with-border">
          <h3 class="box-title">
            <span class="fa fa-fw fa-plus"></span> <?php echo $language->get('text_new_expense_category_title'); ?>
          </h3>
          <button type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
            <i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-plus'; ?>"></i>
          </button>
        </div>

        <!-- Add ExpenseCategory Create Form -->
        <?php include('../_inc/template/expense_category_create_form.php'); ?>

      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_category_list_title'); ?>
            </h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                $hide_colums = "";
                if ($user->getGroupId() != 1) {
                  if (!$user->hasPermission('access', 'update_expense_category')) {
                    $hide_colums .= "6,";
                  }
                  if (!$user->hasPermission('access', 'delete_expense_category')) {
                    $hide_colums .= "7,";
                  }
                }
              ?> 
              
              <!-- ExpenseCategory List Start -->
              <table id="category-category-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-5">
                      <?php echo sprintf($language->get('label_id'), null); ?>
                    </th>
                    <th class="w-30">
                      <?php echo $language->get('label_category_name'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_total'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_sort_order'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_status'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo $language->get('label_created_at'); ?>
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
                      <?php echo sprintf($language->get('label_id'), null); ?>
                    </th>
                    <th class="w-30">
                      <?php echo $language->get('label_category_name'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_total'); ?>
                    </th>
                    <th class="w-5">
                      <?php echo $language->get('label_sort_order'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_status'); ?>
                    </th>
                    <th class="w-25">
                      <?php echo $language->get('label_created_at'); ?>
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
              <!-- ExpenseCategory List End -->
              
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