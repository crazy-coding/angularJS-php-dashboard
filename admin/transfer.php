<?php 
ob_start();
session_start();
include ("../_init.php");

// redirect, if user not logged in
if (!$user->isLogged()) {
  redirect(store('base_url') . '/index.php?redirect_to=' . url());
}

// redirect, user haven't read permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_transfer')) {
  redirect(store('base_url') . '/admin/dashboard.php');
}

//  Load Language File
$language->load('transfer');

// Set Document Title
$document->setTitle($language->get('title_transfer'));

// Add Script
$document->addScript('../assets/itsolution24/angular/modals/TransferEditModal.js');
$document->addScript('../assets/itsolution24/angular/controllers/TransferController.js');

include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- content wrapper start -->
<div class="content-wrapper" ng-controller="TransferController">

  <!-- content header start -->
  <section class="content-header">
    <?php include ("../_inc/template/partials/apply_filter.php"); ?>
    <h1>
      <?php echo $language->get('text_transfer_title'); ?>
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
        <?php echo $language->get('text_transfer_title'); ?>
      </li>
    </ol>
  </section>
  <!-- content header end -->

  <!-- content start -->
  <section class="content">

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'add_transfer')) : ?>
      <div class="box box-info<?php echo create_box_state(); ?>">
        <div class="box-header with-border">
          <h3 class="box-title">
            <span class="fa fa-fw fa-plus"></span> <?php echo $language->get('text_add_transfer_title'); ?>
          </h3>
          <button type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
            <i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-plus'; ?>"></i>
          </button>
        </div>
        <?php include('../_inc/template/transfer_add_form.php'); ?>
      </div>
    <?php endif; ?>

    <div class="row">
      <!-- transfer list section start-->
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_list_transfer_title'); ?>
            </h3>
          </div>
          <div class='box-body'>     
            <?php
              $hide_colums = "";
              if ($user->getGroupId() != 1) {
                if (! $user->hasPermission('access', 'update_transfer')) {
                  $hide_colums .= "6,";
                }
              }
            ?> 
            <div class="table-responsive">                     
              <table id="transfer-transfer-list" class="table table-bordered table-striped table-hovered" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th class="w-20">
                      <?php echo $language->get('label_date'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo sprintf($language->get('label_ref_no'),null); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_from_store'); ?>
                    </th>
                    <th class="w-15">
                      <?php echo $language->get('label_to_store'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_total_item'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_total_quantity'); ?>
                    </th>
                    <th class="w-10">
                      <?php echo $language->get('label_edit'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="success">
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
            </div>
          </div>
        </div>
      </div>
       <!-- transfer list section end-->
    </div>
  </section>
  <!-- content end -->
</div>
<!-- content wrapper end -->

<?php include ("footer.php"); ?>