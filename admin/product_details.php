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

// LOAD PRODUCT MODEL
$product_model = $registry->get('loader')->model('product');

// FETCH PRODUCT INFO
$p_id = isset($request->get['p_id']) ? $request->get['p_id'] : '';
$product = $product_model->getProduct($p_id);
if (count($product) <= 1) {
	redirect(root_url() . '/'.ADMINDIRNAME.'/product.php');
}

// Set Document Title
$document->setTitle($language->get('title_product'));

// Add Script
if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_report')) {
	$document->addScript('../assets/itsolution24/angular/controllers/ReportProductSellController.js');
}
if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_report')) {
	$document->addScript('../assets/itsolution24/angular/controllers/ReportProductBuyController.js');
}

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
			<?php echo $language->get('text_product'); ?> &raquo; <?php echo $product['p_name'];?>
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
			<li>
				<a href="product.php">
					<?php echo $language->get('text_products'); ?>
				</a>
			</li>
			<li class="active">
				<?php echo $product['p_name'];?>
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

			    <div class="nav-tabs-custom">
	                <ul class="nav nav-tabs">
	                    <li class="active">
	                    	<a href="#details" data-toggle="tab" aria-expanded="false">
	                    		<?php echo $language->get('text_details'); ?>
	                    	</a>
	                    </li>
	                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_report')) : ?>
		                <li class="">
	                    	<a href="#sells" data-toggle="tab" aria-expanded="false">
	                    		<?php echo $language->get('text_sells'); ?>
		                    </a>
		                </li>
			            <?php endif; ?>
			            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_report')) : ?>
		                <li class="">
	                    	<a href="#stock" data-toggle="tab" aria-expanded="false">
	                    		<?php echo $language->get('text_stock'); ?>
		                    </a>
		                </li>
			            <?php endif; ?>
		                <li class="">
	                    	<a href="#chart" data-toggle="tab" aria-expanded="false">
	                    		<?php echo $language->get('text_chart'); ?>
		                    </a>
		                </li>
		                <li class="box-tools pull-right">
		                	<div class="btn-group">
				                <a href="product.php?p_id=<?php echo $product['p_id'];?>&p_name=<?php echo $product['p_name'];?>" type="button" class="btn btn-primary">
				                  	<span class="fa fa-fw fa-pencil"></span> <?php echo $language->get('button_edit'); ?>
				                </a>
				            </div>
		                </li>
	                </ul>
	                <div class="tab-content">
	                    <div class="tab-pane active" id="details">
	                        <?php include '../_inc/template/product_view_form.php'; ?>
	                    </div>
	                    <!-- End Details Tab -->

	                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_report')) : ?>
	                    <div class="tab-pane" id="sells" ng-controller="ReportProductSellController">
	                    	<div class="box box-success">
					          <div class="box-header">
					            <h3 class="box-title">
					              <?php echo $language->get('text_selling_report_sub_title'); ?>
					            </h3>
					          </div>
					          <div class="box-body">
					            <div class="table-responsive">  
					            	<?php
					                  $hide_colums = "2, ";
					                  if ($user->getGroupId() != 1) {
					                    if (! $user->hasPermission('access', 'view_buy_price')) {
					                      $hide_colums .= "4,";
					                    }
					                  }
					                ?>
					              	<table id="report-report-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
						                <thead>
						                  <tr class="bg-gray">
						                    <th class="w-10">
						                      <?php echo $language->get('label_serial_no'); ?>
						                    </th>
						                    <th class="w-20">
						                      <?php echo $language->get('label_created_at'); ?>
						                    </th>
						                    <th class="w-20">
						                      <?php echo sprintf($language->get('label_product_name'), null); ?>
						                    </th>
						                    <th class="w-10">
						                      <?php echo $language->get('label_quantity'); ?>
						                    </th>
						                    <th class="w-10">
						                      <?php echo $language->get('label_buying_price'); ?>
						                    </th>
						                    <th class="w-10">
						                      <?php echo $language->get('label_selling_price'); ?>
						                    </th>
						                    <th class="w-10">
						                      <?php echo $language->get('label_tax_amount'); ?>
						                    </th>
						                    <th class="w-10">
						                      <?php echo $language->get('label_discount_amount'); ?>
						                    </th>
						                    <th class="w-10">
						                      <?php echo $language->get('label_profit'); ?>
						                    </th>
						                  </tr>
						                </thead>
						                <tfoot>
						                  <tr class="bg-gray">
						                    <th></th>
						                    <th></th>
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
		                <?php endif; ?>
	                    <!-- End Sells Tab -->

	                    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_report')) : ?>
	                    <div class="tab-pane" id="stock" ng-controller="ReportProductBuyController">
                    		<div class="box-header">
					            <h3 class="box-title">
					              <?php echo $language->get('text_buying_report_sub_title'); ?>  
					            </h3>
					        </div>
					        <div class="box-body">
					            <div class="table-responsive">  
					            	<?php
					                  $hide_colums = "";
					                  if ($user->getGroupId() != 1) {
					                    if (! $user->hasPermission('access', 'view_buy_price')) {
					                      $hide_colums .= "3,";
					                    }
					                  }
					                ?>
					              <table id="buyreport-buyreport-list" class="table table-bordered table-striped table-hover" data-hide-colums="<?php echo $hide_colums; ?>">
					                <thead>
					                  <tr class="bg-gray">
					                    <th class="w-5">
					                      <?php echo $language->get('label_serial_no'); ?>
					                    </th>
					                    <th class="w-25">
					                      <?php echo $language->get('label_created_at'); ?>
					                    </th>
					                    <th class="w-40">
					                      <?php echo sprintf($language->get('label_invoice_id'), 
					                      $language->get('label_product')); ?>
					                    </th>
					                    <th class="w-5">
					                      <?php echo $language->get('label_buying_price'); ?>
					                    </th>
					                    <th class="w-5">
					                      <?php echo $language->get('label_selling_price'); ?>
					                    </th>
					                    <th class="w-5">
					                      <?php echo $language->get('label_quantity'); ?>
					                    </th>
					                    <th class="w-5">
					                      <?php echo $language->get('label_sold'); ?>
					                    </th>
					                    <th class="w-5">
					                      <?php echo $language->get('label_available'); ?>
					                    </th>
					                    <th class="w-5">
					                      <?php echo $language->get('label_status'); ?>
					                    </th>
					                  </tr>
					                </thead>
					                <tfoot>
					                  <tr class="bg-gray">
					                    <th></th>
					                    <th></th>
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
		                <?php endif; ?>
	                    <!-- End Stock Tab -->

	                    <div class="tab-pane" id="chart">
	                    	<?php
	                    	if (from()) {
	                    		$label = 'From ' . from() . ' to ' . to();
	                    	} else {
	                    		$label = 'Date:  ' . date('Y-m-d');
	                    	}
	                    	$labels = array($label); 
	                    	$sells_array = array(product_selling_price($p_id, from(), to()));
	                    	$buys_array = array(product_buying_price($p_id, from(), to()));
	                    	?>
	                    	<canvas id="buy-sell-comparison"></canvas>
	                    </div>
	                    <!-- End Chart Tab -->

	                </div>
	            </div>

			</div>
		</div>

	</section>
  	<!-- Content End -->

</div>
<!-- Content Wrapper End -->

<script type="text/javascript"> 
$(function() {
  var labels = <?php echo json_encode($labels); ?>;
  var sellData = <?php echo json_encode($sells_array); ?>;
  var buyData = <?php echo json_encode($buys_array); ?>;
  var ctx = document.getElementById("buy-sell-comparison");
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: labels,
          datasets: [
              {
                  label: "Selling",
                  borderColor: "#27CDF7",
                  borderWidth: "1",
                  backgroundColor: "#27CDF7",
                  pointHighlightStroke: "rgba(26,179,148,1)",
                  data: sellData
              },
              {
                  label: "Buying",
                  borderColor: "#27CDF7",
                  borderWidth: "1",
                  backgroundColor: "#00A65A",
                  pointHighlightStroke: "rgba(26,179,148,1)",
                  data: buyData
              }
          ]
      },
      options: {
          responsive: true,
          tooltips: {
              mode: 'index',
              intersect: false
          },
          hover: {
              mode: 'nearest',
              intersect: true
          },
          barPercentage: 0.5
      }
  });
});
</script>

<?php include ("footer.php"); ?>