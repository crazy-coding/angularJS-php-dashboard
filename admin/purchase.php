<?php
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_purchase_list')) {
  redirect(root_url() . '/admin/dashboard.php');
}

//  Load Language File
$language->load('purchase');

// Set Document Title
$document->setTitle($language->get('title_purchase'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/PurchaseController.js');

// Include Header and Footer
include("header.php"); 
include ("left_sidebar.php");
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="PurchaseController">

	<!-- Content Header Start -->
	<section class="content-header">
		<?php include ("../_inc/template/partials/apply_filter.php"); ?>
		<h1>
		    <?php echo $language->get('text_purchase_title'); ?>
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
		    	<?php echo $language->get('text_purchase_title'); ?>
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
		    <div class="col-xs-12">
		      	<div class="box box-info">
		      		<div class="box-header">
				        <h3 class="box-title">
				        	<?php echo $language->get('text_purchase_sub_title'); ?>
				        </h3>
				        <div class="box-tools pull-right">
			                <div class="btn-group">
				                <button type="button" class="btn btn-info">
				                  	<span class="fa fa-fw fa-filter"></span> 
				                  	<?php if(isset($request->get['type'])) : ?>
				                  		<?php echo ucfirst($request->get['type']); ?>
					                <?php else : ?>
					                	<?php echo $language->get('button_filter'); ?>
					                <?php endif; ?>
				                </button>
				                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
				                    <span class="caret"></span>
				                    <span class="sr-only">Toggle Dropdown</span>
				                </button>
				                <ul class="dropdown-menu" role="menu">
				                    <li>
				                    	<a href="purchase.php">
				                    		<?php echo $language->get('button_all_purchase'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="purchase.php?type=due">
				                    		<?php echo $language->get('button_due_purchase'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="purchase.php?type=paid">
				                    		<?php echo $language->get('button_paid_purchase'); ?>
				                    	</a>
				                    </li>
				                    <li>
				                    	<a href="purchase.php?type=transfer">
				                    		<?php echo $language->get('button_stock_transfer'); ?>
				                    	</a>
				                    </li>
				                 </ul>
			                </div>
			            </div>
				     </div>
			      	<div class='box-body'>  
						<div class="table-responsive"> 
						<?php
				            $hide_colums = "";
				            if ($user->getGroupId() != 1) {
								if (! $user->hasPermission('access', 'purchase_payment')) {
									$hide_colums .= "9,";
								}
								if (! $user->hasPermission('access', 'purchase_return')) {
									$hide_colums .= "10,";
								}
								if (! $user->hasPermission('access', 'read_purchase_list')) {
									$hide_colums .= "11,";
								}
								if (! $user->hasPermission('access', 'update_purchase_invoice_info')) {
									$hide_colums .= "12,";
								}
								if (! $user->hasPermission('access', 'delete_purchase_invoice')) {
									$hide_colums .= "13,";
								}
				            }
				          ?>  

				          <!-- Invoice List Start -->
						  <table id="invoice-invoice-list"  class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
						    <thead>
						      	<tr class="bg-gray">
						      		<th class="w-5">
		                            	<?php echo $language->get('label_type'); ?>
		                          	</th>
							        <th class="w-10">
							        	<?php echo $language->get('label_invoice_id'); ?>
							        </th>
							        <th class="w-15">
							        	<?php echo $language->get('label_datetime'); ?>
							        </th>
							        <th class="w-15">
							        	<?php echo $language->get('label_supplier'); ?>
							        </th>
							        <th class="w-10">
							        	<?php echo $language->get('label_creator'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_invoice_amount'); ?> 
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_invoice_paid'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_due'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_status'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_pay'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_return'); ?>
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
		                            	<?php echo $language->get('label_type'); ?>
		                          	</th>
							        <th class="w-10">
							        	<?php echo $language->get('label_invoice_id'); ?>
							        </th>
							        <th class="w-15">
							        	<?php echo $language->get('label_datetime'); ?>
							        </th>
							        <th class="w-15">
							        	<?php echo $language->get('label_supplier'); ?>
							        </th>
							        <th class="w-10">
							        	<?php echo $language->get('label_creator'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_invoice_amount'); ?> 
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_invoice_paid'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_due'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_status'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_pay'); ?>
							        </th>
							        <th class="w-5">
							        	<?php echo $language->get('label_return'); ?>
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
						  <!-- Invoice List End -->
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