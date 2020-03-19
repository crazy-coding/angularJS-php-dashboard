<?php 
ob_start();
session_start();
include '../_init.php';

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_product')) {
	redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('product');

// Set Document Title
$document->setTitle($language->get('title_product'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/ProductController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php"); 
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="ProductController">

  	<!-- Content Header Start -->
	<section class="content-header">
		<h1>
			<?php echo $language->get('text_products'); ?>
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
				<?php echo $language->get('text_products'); ?>	
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
	    
    	<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_product')) : ?>
	    <div class="box box-info<?php echo create_box_state(); ?>">
	        <div class="box-header with-border">
				<h3 class="box-title">
					<span class="fa fa-fw fa-plus"></span> <?php echo sprintf($language->get('text_add_new'), $language->get('text_product')); ?>
				</h3>
				<button  type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
					<i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-plus'; ?>"></i>
				</button>
	        </div>

	        <?php if (isset($error_message)): ?>
	        	<div class="alert alert-danger">
					<p>
						<span class="fa fa-warning"></span> 
						<?php echo $error_message; ?>
					</p>
	        	</div>
	        <?php elseif (isset($success_message)): ?>
	          <div class="alert alert-success">
				<p>
					<span class="fa fa-check"></span> 
					<?php echo $success_message; ?>
				</p>
	          </div>
	        <?php endif; ?>

	        <!-- Include Product Form -->
	        <?php include('../_inc/template/product_create_form.php'); ?>

	    </div>
	    <?php endif; ?>

	    <div class="row">
		    <form action="product_bulk_action.php" method="post" enctype="multipart/form-data" id="product-list-form">
			    <div class="col-xs-12">
			        <div class="box box-success">
				        <div class="box-header">
				            <h3 class="box-title">
				            	<?php echo sprintf($language->get('text_view_all'), $language->get('text_product')); ?>	
				            </h3>

				            <!--Box Tools End-->
				            <div class="box-tools pull-right">

				            	<!-- Filter Product Supplier Wise -->
				               <?php include('../_inc/template/partials/product_filter.php'); ?>

					            <!-- Trash Box -->
				                <div class="btn-group">
					                <a type="button" class="btn btn-danger" href="product.php?location=trash">
					                  	<span class="fa fa-trash"></span> 
					                  	<?php echo $language->get('button_trash'); ?> 
					                  	<i class="badge badge-warning" id="total-trash">
					                  		<?php echo total_trash_product(); ?>
					                  	</i>
					                </a>
				                </div>

				                <!-- Bulk Action -->
			                	<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'product_bulk_action')) : ?>            
				                <div class="btn-group">
					                <button type="button" class="btn btn-danger">
					                  	<?php echo $language->get('button_bulk'); ?>
					                </button>
					                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
					                    <span class="caret"></span>
					                    <span class="sr-only">Toggle Dropdown</span>
					                </button>
					                <ul class="dropdown-menu" role="menu">
					                    <li>
					                    	<a id="delete-all" href="#" data-form="#product-list-form" data-loading-text="Deleting...">
					                    		<?php echo $language->get('button_delete_all'); ?>
					                    	</a>
					                    </li>
					                    <?php if(isset($request->get['location']) && $request->get['location'] == 'trash') : ?>
					                    <li>
					                    	<a id="restore-all" href="#" data-form="#product-list-form" data-datatable="product-product-list" data-loading-text="Restoring...">
					                      		<?php echo $language->get('button_restore_all'); ?>
					                    	</a>
					                    </li>
					                    <?php endif; ?>
					                 </ul>
				                </div>
					            <?php endif; ?>

				            </div>
				            <!--  Box Tools End-->

				        </div>
						<div class="box-body">
							<div class="table-responsive">
								<?php
									$print_columns = '1,2,3,4,5,6,7';
									if ($user->getGroupId() != 1) {
										if (! $user->hasPermission('access', 'show_buy_price')) {
											$print_columns = str_replace('6,', '', $print_columns);
										}
									}
									$hide_colums = "";
									if ($user->getGroupId() != 1) {
										if (! $user->hasPermission('access', 'product_bulk_action')) {
											$hide_colums .= "0,";
										}
										if (! $user->hasPermission('access', 'show_buy_price')) {
											$hide_colums .= "6,";
										}
										if (! $user->hasPermission('access', 'read_product')) {
											$hide_colums .= "8,";
										}
										if (! $user->hasPermission('access', 'update_product')) {
											$hide_colums .= "9,";
										}
										if (! $user->hasPermission('access', 'create_buying_invoice')) {
											$hide_colums .= "10,";
										}
										if (! $user->hasPermission('access', 'print_barcode')) {
											$hide_colums .= "11,";
										}
										if (! $user->hasPermission('access', 'delete_product')) {
											$hide_colums .= "12,";
										}
									}

								?>  
								<table id="product-product-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>" data-print-columns="<?php echo $print_columns;?>">
								    <thead>
								        <tr class="bg-gray">
								            <th class="w-5 product-head text-center">
								            	<input type="checkbox" onclick="$('input[name*=\'select\']').prop('checked', this.checked);">
								            </th>
								            <th class="w-10">
								            	<?php echo sprintf($language->get('label_pcode'),null); ?>
								            </th>
								            <th class="w-20">
								            	<?php echo sprintf($language->get('label_name'),$language->get('text_product')); ?>
								            </th>
								            <th class="w-15">
								            	<?php echo $language->get('label_supplier'); ?>
								            </th>
								            <th class="w-10">
								            	<?php echo $language->get('label_category'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_stock'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_buying_price'); ?>
								            </th>                        
								            <th class="w-5">
								            	<?php echo $language->get('label_selling_price'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_view'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_edit'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_buy'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_print_barcode'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_delete'); ?>
								            </th>
								        </tr>
								    </thead>
								    <tfoot>
										<tr class="bg-gray">
											<th class="w-5 product-head text-center">
								            	<input type="checkbox" onclick="$('input[name*=\'select\']').prop('checked', this.checked);">
								            </th>
								            <th class="w-10">
								            	<?php echo sprintf($language->get('label_pcode'),null); ?>
								            </th>
								            <th class="w-20">
								            	<?php echo sprintf($language->get('label_name'),$language->get('text_product')); ?>
								            </th>
								            <th class="w-15">
								            	<?php echo $language->get('label_supplier'); ?>
								            </th>
								            <th class="w-10">
								            	<?php echo $language->get('label_category'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_stock'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_buying_price'); ?>
								            </th>                        
								            <th class="w-5">
								            	<?php echo $language->get('label_selling_price'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_view'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_edit'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_buy'); ?>
								            </th>
								            <th class="w-5">
								            	<?php echo $language->get('label_print_barcode'); ?>
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
			</form>
	    </div>

	</section>
  	<!-- Content end -->

</div>
<!--  Content Wrapper End -->

<?php include ("footer.php"); ?>