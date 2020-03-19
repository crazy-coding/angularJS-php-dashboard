<?php
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_stock_report')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('stock');

// Set Document Title
$document->setTitle($language->get('title_stock_report'));
$document->setBodyClass('sidebar-collapse');

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/ReportStockController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php");
?>
<!-- Content Wrapper Start -->
<div class="content-wrapper">

	<!-- Content Header Start -->
	<section class="content-header">
		<?php include ("../_inc/template/partials/apply_filter.php"); ?>
	  <h1>
	    <?php echo $language->get('text_stock_report_title'); ?>
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
	    	<?php echo $language->get('text_stock_report_title'); ?>
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
			<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_stock_report')) : ?>
		      	<div id="reprot_stock_parent_wrapper" class="col-md-12">
			        <div class="box box-info">
			          <div class="box-header with-border">
			            <h3 class="box-title">
			              <?php echo $language->get('text_stock_report'); ?>
			            </h3>
			            <!--Box Tools End-->
			            <div class="box-tools pull-right">
							<select id="sup_id" class="select2" name="sup_id">
								<option value="">
									--- <?php echo sprintf($language->get('text_view_all'), 'Stock Report'); ?> ---
								</option>
								<?php foreach (get_suppliers() as $supplier): ?>
								<option value="<?php echo $supplier['sup_id'];?>">
							    	<?php echo $supplier['sup_name']; ?>
							    </option>
							<?php endforeach; ?>
							</select>
			            </div>
			          </div>
			          <div class="box-body">
			            <?php include('../_inc/template/partials/report_stock.php'); ?>
			          </div>
			        </div>
		    	</div>
		    <?php endif; ?>
	    </div>
	</section>
	<!-- Content End -->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>