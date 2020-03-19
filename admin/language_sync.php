<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_language_sync')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('language_sync');

// Set Document Title
$document->setTitle($language->get('title_language_sync'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/LanguageSyncController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php") ;
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="LanguageSyncController">

  <!-- Content Header Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_language_sync_title'); ?>
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
        <?php echo $language->get('text_language_sync_title'); ?>
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
    
    <div class="row">

      <!-- Lang Sync Start -->
      <div class="col-xs-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <?php echo $language->get('text_language_list_title'); ?>
            </h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">  
              <?php
                  $hide_colums = "";
                ?>  
              <table id="language-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
                <tbody>
                  <?php $inc=1;foreach(get_dir_list(ROOT.'/language') as $lang) : if($lang=='english') continue; ?>
                    <tr>
                      <td colspan="4">
                        <div class="alert alert-success mb-0"><b><?php echo $language->get('text_'.$lang); ?></b></div>
                        <div class="table-responsive">  
                          <table class="table table-bordered table-striped table-hover">
                             <thead>
                              <tr class="bg-gray">
                                <th class="w-5 text-center" >
                                  <?php echo $language->get('serial_no'); ?>
                                </th>
                                <th class="w-30 text-center" >
                                  <?php echo $language->get('label_language_name'); ?>
                                </th>
                                <th class="w-5 text-center">
                                  <?php echo $language->get('label_action'); ?>
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $source_files = get_filenames(ROOT.'/language/'.$lang);?>
                              <?php $inc=1;foreach($source_files as $lang_file) : ?>
                                <tr>
                                  <td class="w-10 text-center" >
                                    <?php echo $inc; ?>
                                  </td>
                                  <td class="w-70 text-center">
                                    <?php echo $lang_file; ?>
                                  </td>
                                  <td class="w-20 text-center">
                                    <button data-lang="<?php echo $lang;?>" data-file="<?php echo $lang_file;?>" class="btn btn-info btn-block btn-synclangfile">
                                      <span class="fa fa-fw fa-refresh"></span> <?php echo $language->get('button_sync'); ?>
                                    </button>
                                  </td>
                                </tr>
                              <?php $inc++;endforeach;?>
                            </tbody>
                            <tfoot>
                              <tr class="bg-gray">
                                <th class="w-10 text-center">
                                  <?php echo $language->get('serial_no'); ?>
                                </th>
                                <th class="w-70 text-center">
                                  <?php echo $language->get('label_language_name'); ?>
                                </th>
                                <th class="w-20 text-center">
                                  <?php echo $language->get('label_action'); ?>
                                </th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </td>
                    </tr>
                  <?php $inc++;endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Language Sync End -->
    </div>
  </section>
  <!-- Content End -->
  
</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>