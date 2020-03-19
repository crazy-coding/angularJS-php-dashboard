<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_usergroup')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('usergroup');

// Set Document Title
$document->setTitle($language->get('title_user_group'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/UserGroupController.js');

// ADD BODY CLASS
$document->setBodyClass('usergroup');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="UserGroupController">
  <!-- Content Header Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_group_title'); ?>
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
        <?php echo $language->get('text_group_title'); ?>
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
        <div class="alert alert-warning mb-0">
          <p><span class="fa fa-fw fa-info-circle"></span> Edit & Delete features are disabled in demo version</p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_usergroup')) : ?>
      <div class="box box-info<?php echo create_box_state(); ?>">
        <div class="box-header with-border">
          <h3 class="box-title">
            <span class="fa fa-fw fa-plus"></span> <?php echo $language->get('text_new_group_title'); ?>
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

        <!-- Add Usergroup Create Form -->
        <?php include('../_inc/template/user_group_create_form.php'); ?>

      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_group_list_title'); ?>
            </h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                $hide_colums = "";
                if ($user->getGroupId() != 1) {
                  if (!$user->hasPermission('access', 'update_usergroup')) {
                    $hide_colums .= "3,";
                  }
                  if (!$user->hasPermission('access', 'delete_usergroup')) {
                    $hide_colums .= "4,";
                  }
                }
              ?> 
              <table id="user-group-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-10">
                      <?php echo sprintf($language->get('label_id'), null); ?>
                    </th>
                    <th class="w-40">
                      <?php echo sprintf($language->get('label_name'), null); ?>
                    </th>
                    <th class="w-20">
                      <?php echo sprintf($language->get('label_total_user'), null); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_edit'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_delete'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th><?php echo sprintf($language->get('label_id'), null); ?></th>
                    <th><?php echo sprintf($language->get('label_name'), null); ?></th>
                    <th><?php echo sprintf($language->get('label_total_user'), null); ?></th>
                    <th><?php echo $language->get('label_edit'); ?></th>
                    <th><?php echo $language->get('label_delete'); ?></th>
                  </tr>
                </tfoot>
              </table>
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