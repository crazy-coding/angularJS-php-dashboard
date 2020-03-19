<?php 
ob_start();
session_start();
include '../_init.php';

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permissionn
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_store')) {
	redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('store');

// Set Document Title
$document->setTitle($language->get('title_create_store'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/StoreActionController.js');
$document->addScript('../assets/itsolution24/js/upload.js');

// Include Header and Footer
include ("header.php");
include ("left_sidebar.php");
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="StoreActionController">

	<!-- Content Header Start-->
	<section class="content-header">
		<h1>
			<?php echo $language->get('text_create_store_title'); ?>
		</h1>
		<ol class="breadcrumb">
			<li>
				<a href="dashboard.php">
					<i class="fa fa-dashboard"></i>
					<?php echo $language->get('text_dashboard'); ?>
				</a>
			</li>
			<li>
				<a href="store.php">
					<?php echo $language->get('text_stores'); ?>
				</a>
			</li>
			<li class="active">
				<?php echo $language->get('text_create_store_title'); ?>
			</li>
		</ol>
	</section>
	<!-- Content Header End-->
	
	<!-- Content Start-->
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
	    
		<form id="store-form" class="form-horizontal" action="store.php" method="post">
			<input type="hidden" name="action_type" value="CREATE">
			<div class="box box-success box-no-border">
				<div class="nav-tabs-custom">
			        <ul class="nav nav-tabs">
			          <li class="active">
			          	<a href="#general" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_general'); ?>
			          	</a>
			          </li>
			          <li class="">
			          	<a href="#product-setting" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_product'); ?>
			          	</a>
			          </li>
			          <li class="">
			          	<a href="#currency-setting" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_currency'); ?>
			          	</a>
			          </li>
			          <li class="">
			          	<a href="#payment-method-setting" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_payment_method'); ?>
			          	</a>
			          </li>
			          <li class="">
			          	<a href="#email-setting" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_email_setting'); ?>
			          	</a>
			          </li>
			          <li class="">
			          	<a href="#ftp-setting" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_ftp_setting'); ?>
			          	</a>
			          </li>
			        </ul>
			        <div class="tab-content">

			        <!-- General Setting Start -->
			        <div class="tab-pane active" id="general">
			          	<?php if (isset($error)) : ?>
			              <div class="alert alert-danger">
			                <p>
			                	<span class="fa fa-fw fa-warning"></span> 
			                	<?php echo $error; ?>
			                </p>
			              </div>
			            <?php endif; ?>
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
						<div class="form-group">
							<label for="name" class="col-sm-3 control-label">
								<?php echo sprintf($language->get('label_name'), null); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="name" value="<?php echo isset($request->post['name']) ? $request->post['name'] : null; ?>" name="name">
							</div>
						</div>
						<div class="form-group">
							<label for="country" class="col-sm-3 control-label">
								<?php echo $language->get('label_country'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<?php echo countrySelector(isset($request->post['country']) ? $request->post['country'] : null, 'store-country', 'country'); ?>
							</div>
						</div>
						<div class="form-group">
							<label for="mobile" class="col-sm-3 control-label">
								<?php echo $language->get('label_mobile'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="mobile" value="<?php echo isset($request->post['mobile']) ? $request->post['mobile'] : null; ?>" name="mobile">
							</div>
						</div>
						<div class="form-group">
							<label for="zip_code" class="col-sm-3 control-label">
								<?php echo $language->get('label_zip_code'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<input type="number" class="form-control" id="zip_code" value="<?php echo isset($request->post['zip_code']) ? $request->post['zip_code'] : null; ?>" name="zip_code">
							</div>
						</div>
						<div class="form-group">
							<label for="address" class="col-sm-3 control-label">
								<?php echo $language->get('label_address'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<textarea class="form-control" id="address" name="address"><?php echo isset($request->post['address']) ? $request->post['address'] : null; ?></textarea>
							</div>
						</div>	
						<div class="form-group">
							<label for="vat_reg_no" class="col-sm-3 control-label">
								<?php echo $language->get('label_vat_reg_no'); ?>
							</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="vat_reg_no" value="<?php echo isset($request->post['vat_reg_no']) ? $request->post['vat_reg_no'] : null; ?>" name="vat_reg_no">
							</div>
						</div>
						<div class="form-group">
							<label for="cashier_id" class="col-sm-3 control-label">
								<?php echo $language->get('label_cashier_name'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<select id="cashier_id" name="cashier_id"> 
									<option value="">
										<?php echo $language->get('text_select'); ?>
									</option>
									<?php foreach (get_cashiers() as $cashier) : ?>
										<option value="<?php echo $cashier['id']; ?>" <?php echo $store->get('cashier_id') == $cashier['id'] ? 'selected' : null; ?>>
											<?php echo $cashier['username']; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="timezone" class="col-sm-3 control-label">
								<?php echo $language->get('label_timezone'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<select class="form-control" name="preference[timezone]" id="timezone">
									<option selected="selected" disabled hidden value="">
										<?php echo $language->get('text_select'); ?>
									</option>
								<?php include('../_inc/helper/timezones.php'); ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="invoice_edit_lifespan" class="col-sm-3 control-label">
								<?php echo $language->get('label_invoice_edit_lifespan'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_invoice_edit_lifespan'); ?>">
								</span>
							</label>
							<div class="col-sm-4">
								<input type="number" class="form-control" id="invoice_edit_lifespan" value="<?php echo get_preference('invoice_edit_lifespan'); ?>" name="preference[invoice_edit_lifespan]">
							</div>
							<div class="col-sm-3">
								<select class="form-control" name="preference[invoice_edit_lifespan_unit]" id="invoice_edit_lifespan_unit">
									<option selected="selected" disabled hidden value="">
										<?php echo $language->get('text_select'); ?>
									</option>
									<option value="minute" <?php echo get_preference('invoice_edit_lifespan_unit') == 'minute' ? 'selected' : null; ?>><?php echo $language->get('text_minute'); ?></option>
									<option value="second" <?php echo get_preference('invoice_edit_lifespan_unit') == 'second' ? 'selected' : null; ?>><?php echo $language->get('text_second'); ?></option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="invoice_delete_lifespan" class="col-sm-3 control-label">
								<?php echo $language->get('label_invoice_delete_lifespan'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_invoice_delete_lifespan'); ?>">
								</span>
							</label>
							<div class="col-sm-4">
								<input type="number" class="form-control" id="invoice_delete_lifespan" value="<?php echo get_preference('invoice_delete_lifespan'); ?>" name="preference[invoice_delete_lifespan]">
							</div>
							<div class="col-sm-3">
								<select class="form-control" name="preference[invoice_delete_lifespan_unit]" id="invoice_delete_lifespan_unit">
									<option selected="selected" disabled hidden value="">
										<?php echo $language->get('text_select'); ?>
									</option>
									<option value="minute" <?php echo get_preference('invoice_delete_lifespan_unit') == 'minute' ? 'selected' : null; ?>><?php echo $language->get('text_minute'); ?></option>
									<option value="second" <?php echo get_preference('invoice_delete_lifespan_unit') == 'second' ? 'selected' : null; ?>><?php echo $language->get('text_second'); ?></option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="after_sell_page" class="col-sm-3 control-label">
								<?php echo $language->get('label_after_sell_page'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_after_sell_page'); ?>"></span>
							</label>
							<div class="col-sm-7">
								<select class="form-control" name="preference[after_sell_page]" id="after_sell_page">
								  	<option value="pos" <?php echo get_preference('after_sell_page') == 'pos' ? 'selected' : null; ?>><?php echo $language->get('text_pos'); ?>
								  	</option>
								  	<option value="invoice" <?php echo get_preference('after_sell_page') == 'invoice' ? 'selected' : null; ?>><?php echo $language->get('text_invoice'); ?>
								  	</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="remote_printing" class="col-sm-3 control-label">
								<?php echo $language->get('label_pos_printing'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<select class="form-control" name="remote_printing" id="pos_printing">
								  	<option value="0" <?php echo store('remote_printing') == 0 ? 'selected' : null; ?>>
								  		Web Browser
								  	</option>
								  	<option value="1" <?php echo store('remote_printing') == 1 ? 'selected' : null; ?>>
								  		PHP Server
								  	</option>
								</select>
								<div class="well wel-sm">
									<i>For local single machine installation: PHP Server will be the best choice and for live server or local server setup (LAN): you can install PHP Pos Print Server locally on each machine (recommended) or use web browser printing feature.</i>
								</div>
								<div class="well wel-sm">
									<div class="form-group">
										<div class="col-sm-6">
											<label for="receipt_printer" class="control-label">
												<?php echo $language->get('label_receipt_printer'); ?>
											</label>
											<div>
											  	<select class="form-control" name="receipt_printer" id="receipt_printer">
											  		<option value=""><?php echo $language->get('text_select');?></option>
											  		<?php foreach (get_printers() as $printer) : ?>
											  			<option value="<?php echo $printer['printer_id'];?>" <?php echo store('receipt_printer') == $printer['printer_id'] ? 'selected' : null; ?>>
											  				<?php echo $printer['title'];?>
												  		</option>
											  		<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-sm-6">
											<label for="auto_print_receipt" class="control-label">
												<?php echo $language->get('label_auto_print_receipt'); ?>
											</label>
											<div>
											  	<select class="form-control" name="auto_print" id="auto_print_receipt">
												  	<option value="1" <?php echo store('auto_print') == 1 ? 'selected' : null; ?>>Yes
												  	</option>
												  	<option value="0" <?php echo store('auto_print') == 0 ? 'selected' : null; ?>>No
												  	</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="tax" class="col-sm-3 control-label">
								<?php echo $language->get('label_tax'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_tax'); ?>"></span>
							</label>
							<div class="col-sm-7">
							  <input type="number" class="form-control" id="tax" name="preference[tax]" value="<?php echo get_preference('tax'); ?>" onClick="this.select()" onKeyUp="if(this.value<0){this.value='0';}else if(this.value>99){this.value='99';}">
							</div>
						</div>
						<div class="form-group">
							<label for="stock_alert_quantity" class="col-sm-3 control-label">
								<?php echo $language->get('label_stock_alert_quantity'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_stock_alert_quantity'); ?>"></span>
							</label>
							<div class="col-sm-7">
							  <input type="number" class="form-control" id="stock_alert_quantity" name="preference[stock_alert_quantity]" value="<?php echo get_preference('stock_alert_quantity'); ?>" onClick="this.select()" onKeyUp="if(this.value<0){this.value='0';}">
							</div>
						</div>
						<div class="form-group">
							<label for="datatable_item_limit" class="col-sm-3 control-label">
								<?php echo $language->get('label_datatable_item_limit'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_datatable_item_limit'); ?>"></span>
							</label>
							<div class="col-sm-7">
							  <input type="number" class="form-control" id="datatable_item_limit" name="preference[datatable_item_limit]" value="<?php echo get_preference('datatable_item_limit'); ?>" onClick="this.select()" onKeyUp="if(this.value<0){this.value='0';}">
							</div>
						</div>
						<div class="form-group">
							<label for="invoice_footer_text" class="col-sm-3 control-label">
								<?php echo $language->get('label_invoice_footer_text'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_invoice_footer_text'); ?>"></span>
							</label>
							<div class="col-sm-7">
								<textarea class="form-control" id="invoice_footer_text" name="preference[invoice_footer_text]"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="sound_effect" class="col-sm-3 control-label">
								<?php echo $language->get('label_sound_effect'); ?>
							</label>
							<div class="col-sm-7">
								<select id="sound_effect" name="sound_effect"> 
									<option value="">
										<?php echo $language->get('text_select'); ?>
									</option>
									<option value="1" <?php echo $store->get('sound_effect') ? 'selected' : null; ?>>
										<?php echo $language->get('text_active'); ?>
									</option>
									<option value="0" <?php echo !$store->get('sound_effect') ? 'selected' : null; ?>>
										<?php echo $language->get('text_in_active'); ?>
									</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="status" class="col-sm-3 control-label">
								<?php echo $language->get('label_status'); ?>
							</label>
							<div class="col-sm-7">
								<select id="status" name="status"> 
									<option value="">
										<?php echo $language->get('text_select'); ?>
									</option>
									<option value="1" <?php echo $store->get('status') ? 'selected' : null; ?>>
										<?php echo $language->get('text_active'); ?>
									</option>
									<option value="0" <?php echo !$store->get('status') ? 'selected' : null; ?>>
										<?php echo $language->get('text_in_active'); ?>
									</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="sort_order" class="col-sm-3 control-label">
								<?php echo $language->get('label_sort_order'); ?>
							</label>
							<div class="col-sm-7">
								<input type="number" class="form-control" id="sort_order" value="<?php echo isset($request->post['sort_order']) ? $request->post['sort_order'] : 0; ?>" name="sort_order">
							</div>
						</div>
					</div>
					<!-- General Setting End -->

					<!-- Email Setting Start -->
					<div class="tab-pane" id="email-setting">

						<div class="form-group">
							<label for="preference[email_from]" class="col-sm-3 control-label">
								<?php echo $language->get('label_email_from'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_email_from'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="email_from" value="<?php echo get_preference('email_from'); ?>" name="preference[email_from]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[email_address]" class="col-sm-3 control-label">
								<?php echo $language->get('label_email_address'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_email_address'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="email_address" value="<?php echo get_preference('email_address'); ?>" name="preference[email_address]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[email_driver]" class="col-sm-3 control-label">
								<?php echo $language->get('label_email_driver'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_email_driver'); ?>"></span>
							</label>
							<div class="col-sm-7">
								<select id="email_driver" name="preference[email_driver]"> 
									<option value="">
										<?php echo $language->get('text_select'); ?>
									</option>
									<option value="mail_function" <?php echo get_preference('email_driver') == 'mail_function' ? 'selected' : null; ?>>
										Use built in php mail() function
									</option>
									<option value="smtp_server" <?php echo get_preference('email_driver') == 'smtp_server' ? 'selected' : null; ?>>
										send Email through SMTP Server
									</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="preference[smtp_host]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_host'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_host'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_host" value="<?php echo get_preference('smtp_host'); ?>" name="preference[smtp_host]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[smtp_username]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_username'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_username'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_username" value="<?php echo get_preference('smtp_username'); ?>" name="preference[smtp_username]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[smtp_password]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_password'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_password'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_password" value="<?php echo get_preference('smtp_password'); ?>" name="preference[smtp_password]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[smtp_port]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_port'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_port'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_port" value="<?php echo get_preference('smtp_port'); ?>" name="preference[smtp_port]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[ssl_tls]" class="col-sm-3 control-label">
								<?php echo $language->get('label_ssl_tls'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_ssl_tls'); ?>"></span>
							</label>
							<div class="col-sm-7">
								<select id="ssl_tls" name="preference[ssl_tls]"> 
									<option value="" <?php echo get_preference('ssl_tls') == false ? 'selected' : null; ?>>
										None
									</option>
									<option value="tls" <?php echo get_preference('ssl_tls') == 'tls' ? 'selected' : null; ?>>
										TLS
									</option>
									<option value="ssl" <?php echo get_preference('ssl_tls') == 'ssl' ? 'selected' : null; ?>>
										SSL
									</option>
								</select>
							</div>
						</div>
						
					</div>
					<!-- Email Setting End -->

					<!-- FTP Setting Start -->
					<div class="tab-pane" id="ftp-setting">
						<div class="form-group">
							<label for="preference[ftp_hostname]" class="col-sm-3 control-label">
								<?php echo $language->get('label_ftp_hostname'); ?>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="ftp_hostname" value="<?php echo get_preference('ftp_hostname'); ?>" name="preference[ftp_hostname]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[ftp_username]" class="col-sm-3 control-label">
								<?php echo $language->get('label_ftp_username'); ?>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="ftp_username" value="<?php echo get_preference('ftp_username'); ?>" name="preference[ftp_username]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[ftp_password]" class="col-sm-3 control-label">
								<?php echo $language->get('label_ftp_password'); ?>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="ftp_password" value="<?php echo get_preference('ftp_password'); ?>" name="preference[ftp_password]">
							</div>
						</div>
					</div>
					<!-- FTP Setting End -->

					<!-- Product Setting Start -->
					<div class="tab-pane" id="product-setting">
						<div class="form-group">
					      <label class="col-sm-3 control-label"></label>
					      <div class="col-sm-7 product-selector">
					        <div class="checkbox selector">
					          <label>
					            <input type="checkbox" onclick="$('input[name*=\'product\']').prop('checked', this.checked);"> Select / Deselect
					          </label>
					        </div>
					        <div class="filter-searchbox">
					          <input ng-model="search_product" class="form-control" type="text" id="search_product" placeholder="<?php echo $language->get('search'); ?>">
					        </div>
					        <div class="well well-sm product-well"> 
					          <div filter-list="search_product">
					          <?php foreach(get_products() as $the_product) : ?>                    
					            <div class="checkbox">
					              <label>                         
					                <input type="checkbox" name="product[]" value="<?php echo $the_product['p_id']; ?>">
					                <?php echo $the_product['p_name']; ?>
					              </label>
					            </div>
					          <?php endforeach; ?>
					          </div>
					        </div>
					      </div>
					    </div>
					</div>
					<!-- Product Setting End -->

					<!-- Currency Ssetting Start -->
					<div class="tab-pane" id="currency-setting">
						<div class="form-group">
					      <label class="col-sm-3 control-label"></label>
					      <div class="col-sm-7 currency-selector">
					        <div class="checkbox selector">
					          <label>
					            <input type="checkbox" onclick="$('input[name*=\'currency\']').prop('checked', this.checked);"> Select / Deselect
					          </label>
					        </div>
					        <div class="filter-searchbox">
					          <input ng-model="search_currency" class="form-control" type="text" id="search_currency" placeholder="<?php echo $language->get('search'); ?>">
					        </div>
					        <div class="well well-sm currency-well"> 
					          <div filter-list="search_currency">
					          <?php foreach(get_currencies() as $the_currency) : ?>                    
					            <div class="checkbox">
					              <label>                         
					                <input type="checkbox" name="currency[]" value="<?php echo $the_currency['currency_id']; ?>">
					                <?php echo $the_currency['title']; ?>
					              </label>
					            </div>
					          <?php endforeach; ?>
					          </div>
					        </div>
					      </div>
					    </div>
					</div>
					<!-- Currency Setting End -->

					<!-- Payment Method Setting Start -->
					<div class="tab-pane" id="payment-method-setting">
						<div class="form-group">
					      <label class="col-sm-3 control-label"></label>
					      	<div class="col-sm-7 payment_method-selector">
						        <div class="checkbox selector">
						          <label>
						            <input type="checkbox" onclick="$('input[name*=\'payment_method\']').prop('checked', this.checked);"> Select / Deselect
						          </label>
						        </div>
						        <div class="filter-searchbox">
						          <input ng-model="search_payment_method" class="form-control" type="text" id="search_payment_method" placeholder="<?php echo $language->get('search'); ?>">
						        </div>
						        <div class="well well-sm payment_method-well"> 
							        <div filter-list="search_payment_method">
							          <?php foreach(get_pmethods() as $the_pmethod) : ?>                    
							            <div class="checkbox">
							              <label>                         
							                <input type="checkbox" name="pmethod[]" value="<?php echo $the_pmethod['pmethod_id']; ?>">
							                <?php echo $the_pmethod['name']; ?>
							              </label>
							            </div>
							          <?php endforeach; ?>
							        </div>
						        </div>
					      	</div>
					    </div>
					</div>
					<!-- Payment Method Setting End -->
				</div>
				<div class="box-footer">
					<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_store')) : ?>
					<div class="form-group">
						<label for="address" class="col-sm-3 control-label"></label>
						<div class="col-sm-7">
							<a id="back-btn" class="btn btn-danger" href="store.php">
								<span class="fa fa-fw fa-angle-left"></span> 
								<?php echo $language->get('button_back'); ?>
							</a>

							<button id="create-store-btn" class="btn btn-info pull-right" type="button" data-form="#store-form" data-loading-text="Saving...">
								<span class="fa fa-fw fa-save"></span> 
								<?php echo $language->get('button_save'); ?>
							</button>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</form>
	</section>
	<!-- Content End -->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>