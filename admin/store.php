<?php 
ob_start();
session_start();
include ("../_init.php");

if (isset($request->get['active_store_id']))
{
  redirect(root_url() . '/'.ADMINDIRNAME.'/store.php');
}

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_store')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('store');

// Set Document Title
$document->setTitle($language->get('title_store'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/StoreController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper">

  <!-- Content Header Start -->
  <section class="content-header" ng-controller="StoreController">
    <h1>
      <?php echo $language->get('text_store_title'); ?>
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
        <?php echo $language->get('title_store'); ?>
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
          <p><span class="fa fa-fw fa-info-circle"></span> Store delete feature is disabled in demo version</p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_store_list_title'); ?>
            </h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                  $hide_colums = "";
                  if ($user->getGroupId() != 1) {
                    if (! $user->hasPermission('access', 'update_store')) {
                      $hide_colums .= "7,";
                    }
                    if (! $user->hasPermission('access', 'delete_store')) {
                      $hide_colums .= "8,";
                    }
                    if (! $user->hasPermission('access', 'activate_store')) {
                      $hide_colums .= "9,";
                    }
                  }
                ?> 
              <table id="store-store-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
                <thead>
                  <tr class="bg-gray">
                    <th>
                      <?php echo sprintf($language->get('label_serial_no'), null); ?>
                    </th>
                    <th>
                      <?php echo sprintf($language->get('label_name'), null); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_country'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_address'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_sort_order'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_created_at'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_status'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_edit'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_delete'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_action'); ?>
                    </th>
                  </tr>
                </thead>
                <tfoot>
                  <tr class="bg-gray">
                    <th>
                      <?php echo sprintf($language->get('label_id'), null); ?>
                    </th>
                    <th>
                      <?php echo sprintf($language->get('label_name'), null); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_country'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_address'); ?>
                     </th>
                    <th>
                      <?php echo $language->get('label_sort_order'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_created_at'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_status'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_edit'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_delete'); ?>
                    </th>
                    <th>
                      <?php echo $language->get('label_action'); ?>
                    </th>
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