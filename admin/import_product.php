<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'import_product')) {
	redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('import');
$message = $language->get('text_product_import_alert');

// Set Document Title
$document->setTitle($language->get('title_import_product'));

// INCLUDE EXEL READER
require('../_inc/vendor/spreadsheet-reader/php-excel-reader/excel_reader2.php');
require('../_inc/vendor/spreadsheet-reader/SpreadsheetReader.php');

// Include Header and Footer
include("header.php");
include ("left_sidebar.php");

if (isset($request->post['submit'])) 
{
	try {

		if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'import_product') || DEMO) {
	      throw new Exception($language->get('error_permission'));
	    }

		if (!$_FILES['filename']['name']) 
		{
			throw new Exception($language->get('error_invalid_file'));
		}

		if ($_FILES["filename"]["type"] != "application/vnd.ms-excel") 
		{
			throw new Exception($language->get('error_invalid_file'));
		} 

		if(isset($_FILES["filename"]["type"]))
		{
			$validextensions = array("xls");
			$temporary = explode(".", $_FILES["filename"]["name"]);
			$file_extension = end($temporary);
			
			if (in_array($file_extension, $validextensions)) {
				if ($_FILES["filename"]["error"] > 0) {
					throw new Exception("Return Code: " . $_FILES['filename']['error']);
				} else {
					$temp = explode(".", $_FILES["filename"]["name"]);
					$newfilename = 'products.' . end($temp);
					$sourcePath = $_FILES["filename"]["tmp_name"];
					$targetPath = "../storage/".$newfilename;
					if(!move_uploaded_file($sourcePath, $targetPath)) {
						throw new Exception($language->get('error_upload'));
					}
				}
			} else {
				throw new Exception($language->get('error_invalid_file'));
			}
		}

		$file_path = realpath(__DIR__.'/../').DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'products.xls';
		if(!file_exists($file_path)) 
		{
			throw new Exception($language->get('error_invalid_file'));
		}

		$p_date = date('Y-m-d');
		$insert_status = array();
		$update_status = array();
		$total_item_no = 0;

		// SpreadsheetReater Initialize
		$Reader = new SpreadsheetReader($file_path);
		$Sheets = $Reader->Sheets();

		foreach ($Sheets as $Index => $Name)
		{
			$Reader->ChangeSheet($Index);			
			switch ($Name) {
				case 'Product':
					foreach ($Reader as $Row)
					{
						if ($Row[1] == 'ProductName' || !$Row[1]) continue;

						$pro_data['product_name'] = $Row[1];
						$store_ids = explode(',', $Row[2]);
						if (count($store_ids) < 1) {
							$store_ids = array_unique(array($store_ids));
						}
						$pro_data['category_id'] = $Row[3];
						$pro_data['unit_id'] = $Row[4];
						$pro_data['taxrate_id'] = $Row[5];
						$pro_data['tax_method'] = $Row[6];
						$pro_data['sup_id'] = $Row[7];
						$pro_data['box_id'] = $Row[8];
						$pro_data['alert_quantity'] = $Row[9];
						$pro_data['sell_price'] = $Row[10];
						$pro_data['expired_date'] = $Row[11];
						$pro_data['description'] = $Row[12];
						$pro_data['status'] = $Row[13];

						$statement = $db->prepare("SELECT * FROM `products` WHERE `p_name` = ?");
      					$statement->execute(array($pro_data['product_name']));
      					$product = $statement->fetch();

      					if (!$product) {

      						$p_code = randomNumber(8);
      						// check pcode and confirm as unique
      						$p = 1;
	      					while ($p) {
	      						$p_code = randomNumber(8);
	      						$statement = $db->prepare("SELECT * FROM `products` WHERE `p_code` = ?");
		      					$statement->execute(array($p_code));
		      					$p = $statement->fetch();
	      					}

							$statement = $db->prepare("INSERT INTO `products` (`p_code`, `p_name`, `category_id`, `unit_id`, `description`) VALUES (?, ?, ?, ?, ?)");
			        		$statement = $statement->execute(array($p_code, $pro_data['product_name'], $pro_data['category_id'], $pro_data['unit_id'], $pro_data['description']));
			        		$product_id = $db->lastInsertId();

							if ($product_id) {

								foreach ($store_ids as $store_id) {
									$statement = $db->prepare("INSERT INTO `product_to_store` (product_id, store_id, sell_price, alert_quantity, sup_id, box_id, taxrate_id, tax_method, e_date, p_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			        				$statement = $statement->execute(array($product_id, $store_id, $pro_data['sell_price'], $pro_data['alert_quantity'], $pro_data['sup_id'], $pro_data['box_id'], $pro_data['taxrate_id'], $pro_data['tax_method'], $pro_data['expired_date'], $p_date, $pro_data['status']));
								}
								
								$insert_status[] = 'ok';

							} else {

								$insert_status[] = 'error';

							} 

							$total_item_no++;

						} elseif ($product) {

							$product_id = $product['p_id'];
							$statement = $db->prepare("UPDATE `products` SET `category_id` = ?, `unit_id` = ?, `description` = ? WHERE `p_id` = ?");
			      			$statement->execute(array($pro_data['category_id'], $pro_data['unit_id'], $pro_data['description'], $product_id));

							if ($statement) {

								foreach ($store_ids as $store_id) {
									$statement = $db->prepare("UPDATE `product_to_store` SET `sell_price` = ?, `alert_quantity` = ?, `sup_id` = ?, `box_id` = ?, `taxrate_id` = ?, `tax_method` = ?, `e_date` = ?, `status` = ? WHERE `product_id` = ? AND `store_id` = ?");
				      				$statement->execute(array($pro_data['sell_price'], $pro_data['alert_quantity'], $pro_data['sup_id'], $pro_data['box_id'], $pro_data['taxrate_id'], $pro_data['tax_method'], $pro_data['expired_date'], $pro_data['status'], $product_id, $store_id));
				      			}

								$update_status[] = 'ok';

							} else {

								$update_status[] = 'error';
							}

							$total_item_no++;
						}
					}
					break;
				default:
					# code...
					break;
			}
		}

		$success = 0;
		$error = 0;
		$message = '';
		$message .= '<h4>Total Item: ' . $total_item_no . '</h4>';
		if ( count($insert_status) > 0 ) {
			for ($i=0; $i < count($insert_status); $i++) { 
				if ( $insert_status[$i] == 'ok' ) {
					$success++;
				}
				if ( $insert_status[$i] == 'error' ) {
					$error++;
				}
			} 
			$message .= '<p><strong>Insert Status</strong></p>';
			$message .= '<ul>';
			$message .= '<li>Total Inserted: ' . $success . '</li>';
			$message .= '<li>Error in: ' . $error . '</li>';
			$message .= '</ul>';
		}

		if (count($update_status) > 0) {
			for ($i=0; $i < count($update_status); $i++) 
			{ 
				if ($update_status[$i]=='ok') {
					$success++;
				}
				if ($update_status[$i]=='error') {
					$error++;
				}
			}
			$message .= '<p><strong>Update Status</strong></p>';
			$message .= '<ul>';
			$message .= '<li>Total Updated: ' . $success . '</li>';
			$message .= '<li>Unchanged in: ' . $error . '</li>';
			$message .= '</ul>';
		}
	}
	catch(Exception $e) { 
	    $error_message = $e->getMessage();
	}
} ?>

<!-- Content Wrapper Start -->
<div class="content-wrapper">

	<!-- Content Header Start -->
	<section class="content-header">
		<h1>
		  <?php echo sprintf($language->get('text_import_title'), $language->get('text_product')); ?>
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
			  	<?php echo sprintf($language->get('text_import_title'), $language->get('text_product')); ?>
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
	        <div class="alert alert-danger mb-0">
	          <p><span class="fa fa-fw fa-info-circle"></span> Product import feature is disabled in demo version</p>
	        </div>
	      </div>
	    </div>
	    <?php endif; ?>
    
		<div class="row">
			<div class="col-sm-12">
				<div class="box box-no-border">

					<div class="alert alert-info">
						<span class="fa fa-fw fa-info-circle"></span> <?php echo $message ; ?>
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

					<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
						<div class="box-body">

							<div class="form-group">
								<div class="col-sm-3">&nbsp;</div>
								<div class="col-sm-9">
							    	<?php echo $language->get('text_download'); ?>
								    <a href="../storage/pos-products.xls" id="download_demo">
								    	<span class="fa fa-fw fa-download"></span> 
								    	<?php echo $language->get('button_download'); ?>
								    </a>
							 	</div>
							</div>

						  	<div class="form-group">
						    	<label for="filename" class="col-sm-3 control-label">
						    		<?php echo $language->get('text_select_xls_file'); ?>
						    	</label>
						        <div class="col-sm-5">	            
									<input type="file" class="form-control" name="filename" id="filename" accept=".xls" required>
						        </div>
						 	</div>
						    <div class="form-group">
						        <div class="col-sm-5 col-sm-offset-3">
							        <button type="submit" class="btn btn-success" name="submit">
							        	<span class="fa fa-fw fa-upload"></span> 
							          	<?php echo $language->get('button_import'); ?>
							        </button>
						        </div>
						    </div>
						</div>
				  	</form>
				</div>
			</div>
		</div>
	</section>
	<!-- Content End -->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>