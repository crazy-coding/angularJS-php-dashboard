<?php 
ob_start();
session_start();
include '../_init.php';

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_store')) {
	redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// LOAD STORE MODEL
$store_model = $registry->get('loader')->model('store');

$store_id = isset($request->get['store_id']) ? $request->get['store_id'] : store_id();
$timezone = get_preference('timezone');
if (!$store_model->getStore($store_id)) {
	redirect(root_url() . '/'.ADMINDIRNAME.'/store.php');
}

//  Load Language File
$language->load('store');

// Set Document Title
$document->setTitle($language->get('title_edit_store'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/StoreActionController.js');
$document->addScript('../assets/itsolution24/js/upload.js');

// Include Header and Footer
include ("header.php");
include ("left_sidebar.php");

$store->setStore($store_id);
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="StoreActionController">

	<!-- Content Header Start-->
	<section class="content-header">
		<h1>
			<?php echo $language->get('text_title'); ?>
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
				<a href="store.php">
					<?php echo $language->get('title_store'); ?>
				</a>
			</li>
			<li class="active">
				<?php echo $language->get('text_title'); ?>
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
	        <div class="alert alert-warning mb-0">
	          <p><span class="fa fa-fw fa-info-circle"></span> Email & FTP settings are disabled in demo version</p>
	        </div>
	      </div>
	    </div>
	    <?php endif; ?>
	    
		<form id="store-form" class="form-horizontal" action="store.php" method="post">
			<input type="hidden" name="action_type" value="UPDATE">
			<input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
			<div class="box box-success box-no-border">
				<div class="nav-tabs-custom">
			        <ul class="nav nav-tabs store-m15">
			        	<li class="active">
			          		<a href="#general" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_general'); ?>
			       			</a>
			       		</li>
			       		<li>
			          		<a href="#pos-setting" data-toggle="tab" aria-expanded="false">
			          		<?php echo $language->get('text_pos_setting'); ?>
			       			</a>
			       		</li>
			       		<?php if (!DEMO) : ?>
				       		<li>
								<a href="#email-setting" data-toggle="tab" aria-expanded="false">
									<?php echo $language->get('text_email_setting'); ?>
								</a>
							</li>
							<li>
								<a href="#ftp-setting" data-toggle="tab" aria-expanded="false">
									<?php echo $language->get('text_ftp_setting'); ?>
								</a>
							</li>
						<?php endif; ?>
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
								<input type="text" class="form-control" id="name" value="<?php echo store('name'); ?>" name="name">
							</div>
						</div>
						<div class="form-group">
							<label for="country" class="col-sm-3 control-label">
								<?php echo $language->get('label_country'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<?php echo countrySelector(store('country'), 'store-country', 'country'); ?>
							</div>
						</div>
						<div class="form-group">
							<label for="mobile" class="col-sm-3 control-label">
								<?php echo $language->get('label_mobile'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="mobile" value="<?php echo store('mobile'); ?>" name="mobile">
							</div>
						</div>
						<div class="form-group">
							<label for="zip_code" class="col-sm-3 control-label">
								<?php echo $language->get('label_zip_code'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<input type="number" class="form-control" id="zip_code" value="<?php echo store('zip_code'); ?>" name="zip_code">
							</div>
						</div>
						<div class="form-group">
							<label for="address" class="col-sm-3 control-label">
								<?php echo $language->get('label_address'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<textarea class="form-control" id="address" name="address"><?php echo store('address'); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="gst_reg_no" class="col-sm-3 control-label">
								<?php echo $language->get('label_gst_reg_no'); ?>
							</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="gst_reg_no" name="preference[gst_reg_no]" value="<?php echo get_preference('gst_reg_no'); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="vat_reg_no" class="col-sm-3 control-label">
								<?php echo $language->get('label_vat_reg_no'); ?>
							</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" id="vat_reg_no" value="<?php echo store('vat_reg_no'); ?>" name="vat_reg_no">
							</div>
						</div>
						<div class="form-group">
							<label for="cashier_id" class="col-sm-3 control-label">
								<?php echo $language->get('label_cashier_name'); ?><i class="required">*</i>
							</label>
							<div class="col-sm-7">
								<select id="cashier_id" name="cashier_id"> 
									<?php foreach (get_cashiers() as $cashier) : ?>
										<option value="<?php echo $cashier['id']; ?>" <?php echo store('cashier_id') == $cashier['id'] ? 'selected' : null; ?>>
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
							<label for="tax" class="col-sm-3 control-label">
								<?php echo $language->get('label_tax'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_tax'); ?>">
								</span>
							</label>
							<div class="col-sm-7">
							  <input type="number" class="form-control" id="tax" name="preference[tax]" value="<?php echo get_preference('tax'); ?>" onClick="this.select()" onKeyUp="if(this.value<0){this.value='0';}else if(this.value>99){this.value='99';}">
							</div>
						</div>
						<div class="form-group">
							<label for="sms_gateway" class="col-sm-3 control-label">
								<?php echo $language->get('label_sms_gateway'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_sms_gateway'); ?>">
								</span>
							</label>
							<div class="col-sm-7">
							  <select id="sms_gateway" name="preference[sms_gateway]"> 
									<option value="Clickatell" <?php echo get_preference('sms_gateway') == 'Clickatell' ? 'selected' : null; ?>>
										Clickatell
									</option>
									<option value="Twilio" <?php echo get_preference('sms_gateway') == 'Twilio' ? 'selected' : null; ?>>
										Twilio
									</option>
									<option value="Msg91" <?php echo get_preference('sms_gateway') == 'Msg91' ? 'selected' : null; ?>>
										Msg91
									</option>
									<option value="Onnorokomsms" <?php echo get_preference('sms_gateway') == 'Onnorokomsms' ? 'selected' : null; ?>>
										Onnorokomsms
									</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="sms_alert" class="col-sm-3 control-label">
								<?php echo $language->get('label_sms_alert'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_sms_alert'); ?>">
								</span>
							</label>
							<div class="col-sm-7">
							  <select id="sms_alert" name="preference[sms_alert]"> 
									<option value="1" <?php echo get_preference('sms_alert') ? 'selected' : null; ?>>
										<?php echo $language->get('text_yes'); ?>
									</option>
									<option value="0" <?php echo !get_preference('sms_alert') ? 'selected' : null; ?>>
										<?php echo $language->get('text_no'); ?>
									</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="auto_sms" class="col-sm-3 control-label">
								<?php echo $language->get('label_auto_sms'); ?>
							</label>
							<div class="col-sm-7">
								<div class="well wel-sm">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="preference[invoice_auto_sms]" value="1" <?php echo get_preference('invoice_auto_sms') == '1' ? 'checked' : null; ?>> <?php echo $language->get('text_sms_after_creating_invoice'); ?>
											</label>
									</div>
									<!-- <div class="checkbox">
										<label>
											<input type="checkbox" name="preference[due_auto_sms]" value="1" <?php //echo get_preference('due_auto_sms') == '1' ? 'checked' : null; ?>> <?php //echo $language->get('text_sms_after_creating_due_invoice'); ?>
										</label>
									</div> -->
								</div>
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
							<label for="sort_order" class="col-sm-3 control-label">
								<?php echo $language->get('label_sort_order'); ?>
							</label>
							<div class="col-sm-7">
								<input type="number" class="form-control" id="sort_order" value="<?php echo store('sort_order'); ?>" name="sort_order">
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
									<option value="1" <?php echo store('status') ? 'selected' : null; ?>>
										<?php echo $language->get('text_active'); ?>
									</option>
									<option value="0" <?php echo !store('status') ? 'selected' : null; ?>>
										<?php echo $language->get('text_in_active'); ?>
									</option>
								</select>
							</div>
						</div>
					</div> 
					<!-- General Setting End -->

					<!-- POS Setting Start -->
					<div class="tab-pane" id="pos-setting">

						<!-- <div class="form-group">
							<div class="col-sm-3"></div>
							<div class="col-sm-7">
								<button id="btn-edit-template" class="btn btn-info" data-loading-text="Processing...">
									<span class="fa fa-fw fa-pencil"></span> 
									<?php //echo $language->get('button_template_update'); ?>
								</button>
							</div>
						</div> -->

						<div class="form-group">
							<label for="invoice_prefix" class="col-sm-3 control-label">
								<?php echo $language->get('label_invoice_prefix'); ?>
							</label>
							<div class="col-sm-7">
							  <input type="text" class="form-control" id="invoice_prefix" name="preference[invoice_prefix]" value="<?php echo get_preference('invoice_prefix'); ?>" onClick="this.select()" onKeyUp="if(this.value<0){this.value='0';}">
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
							<label for="invoice_view" class="col-sm-3 control-label">
								<?php echo $language->get('label_invoice_view'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_invoice_view'); ?>">
								</span>
							</label>
							<div class="col-sm-3">
							  <select id="invoice_view" name="preference[invoice_view]"> 
									<option value="standard" <?php echo get_preference('invoice_view') == 'standard' ? 'selected' : null; ?>>
										<?php echo $language->get('text_standard'); ?>
									</option>
									<option value="tax_invoice" <?php echo get_preference('invoice_view') == 'tax_invoice' ? 'selected' : null; ?>>
										<?php echo $language->get('text_tax_invoice'); ?>
									</option>
									<option value="indian_gst" <?php echo get_preference('invoice_view') == 'indian_gst' ? 'selected' : null; ?>>
										<?php echo $language->get('text_indian_gst'); ?>
									</option>
								</select>
							</div>
							<div ng-show="indianGST" class="col-sm-4">
								<?php echo stateSelector(get_preference('business_state'), 'business_state', 'preference[business_state]'); ?>
							</div>
						</div>
						<div class="form-group">
							<label for="change_item_price_while_billing" class="col-sm-3 control-label">
								<?php echo $language->get('label_change_item_price_while_billing'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_change_item_price_while_billing'); ?>">
								</span>
							</label>
							<div class="col-sm-7">
							  <select id="change_item_price_while_billing" name="preference[change_item_price_while_billing]"> 
									<option value="1" <?php echo get_preference('change_item_price_while_billing') ? 'selected' : null; ?>>
										<?php echo $language->get('text_yes'); ?>
									</option>
									<option value="0" <?php echo !get_preference('change_item_price_while_billing') ? 'selected' : null; ?>>
										<?php echo $language->get('text_no'); ?>
									</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="pos_product_display_limit" class="col-sm-3 control-label">
								<?php echo $language->get('label_pos_product_display_limit'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_pos_product_display_limit'); ?>"></span>
							</label>
							<div class="col-sm-7">
							  <input type="number" class="form-control" id="pos_product_display_limit" name="preference[pos_product_display_limit]" value="<?php echo get_preference('pos_product_display_limit'); ?>" onClick="this.select()" onKeyUp="if(this.value<0){this.value='0';}">
							</div>
						</div>
						<div class="form-group">
							<label for="after_sell_page" class="col-sm-3 control-label">
								<?php echo $language->get('label_after_sell_page'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_after_sell_page'); ?>">
								</span>
							</label>
							<div class="col-sm-7">
								<select class="form-control" name="preference[after_sell_page]" id="after_sell_page">
								  	<option value="pos" <?php echo get_preference('after_sell_page') == 'pos' ? 'selected' : null; ?>>
								  		Point of Sell (POS)
								  	</option>
								  	<option value="invoice" <?php echo get_preference('after_sell_page') == 'invoice' ? 'selected' : null; ?>>
								  		Invoice
								  	</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="invoice_footer_text" class="col-sm-3 control-label">
								<?php echo $language->get('label_invoice_footer_text'); ?>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_invoice_footer_text'); ?>"></span>
							</label>
							<div class="col-sm-7">
								<textarea class="form-control" id="invoice_footer_text" name="preference[invoice_footer_text]"><?php echo get_preference('invoice_footer_text'); ?></textarea>
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
									<option value="1" <?php echo store('sound_effect') ? 'selected' : null; ?>>
										<?php echo $language->get('text_active'); ?>
									</option>
									<option value="0" <?php echo !store('sound_effect') ? 'selected' : null; ?>>
										<?php echo $language->get('text_in_active'); ?>
									</option>
								</select>
							</div>
						</div>
					</div>
					<!-- POS Setting End -->

					<?php if  (!DEMO) : ?>
					<!-- Email Setting Start -->
					<div class="tab-pane" id="email-setting">
						<div class="form-group">
							<label for="preference[email_from]" class="col-sm-3 control-label">
								<?php echo $language->get('label_email_from'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_email_from'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="email_from" value="<?php echo get_preference('email_from'); ?>" name="preference[email_from]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[email_address]" class="col-sm-3 control-label">
								<?php echo $language->get('label_email_address'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_email_address'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="email_address" value="<?php echo get_preference('email_address'); ?>" name="preference[email_address]">
							</div>
						</div>
						<div class="form-group">
							<label for="preference[email_driver]" class="col-sm-3 control-label">
								<?php echo $language->get('label_email_driver'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_email_driver'); ?>"></span>
							</label>
							<div class="col-sm-7">
								<select id="email_driver" name="preference[email_driver]"> 
									<option value="mail_function" <?php echo get_preference('email_driver') == 'mail_function' ? 'selected' : null; ?>>
										Use built in php mail() function
									</option>
									<option value="send_mail" <?php echo get_preference('email_driver') == 'send_mail' ? 'selected' : null; ?>>
										Use Send Mail
									</option>
									<option value="smtp_server" <?php echo get_preference('email_driver') == 'smtp_server' ? 'selected' : null; ?>>
										Send Email through SMTP Server
									</option>
								</select>
							</div>
						</div>
						<div ng-show="isSendMail" class="form-group">
							<label for="preference[send_mail_path]" class="col-sm-3 control-label">
								<?php echo $language->get('label_send_mail_path'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_send_mail_path'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="send_mail_path" value="<?php echo get_preference('send_mail_path') ? get_preference('send_mail_path') : '/usr/sbin/sendmail'; ?>" name="preference[send_mail_path]">
							</div>
						</div>
						<div ng-show="isSMTP" class="form-group">
							<label for="preference[smtp_host]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_host'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_host'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_host" value="<?php echo get_preference('smtp_host'); ?>" name="preference[smtp_host]">
							</div>
						</div>
						<div ng-show="isSMTP" class="form-group">
							<label for="preference[smtp_username]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_username'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_username'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_username" value="<?php echo get_preference('smtp_username'); ?>" name="preference[smtp_username]">
							</div>
						</div>
						<div ng-show="isSMTP" class="form-group">
							<label for="preference[smtp_password]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_password'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_password'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_password" value="<?php echo get_preference('smtp_password'); ?>" name="preference[smtp_password]">
							</div>
						</div>
						<div ng-show="isSMTP" class="form-group">
							<label for="preference[smtp_port]" class="col-sm-3 control-label">
								<?php echo $language->get('label_smtp_port'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_smtp_port'); ?>"></span>
							</label>
							<div class="col-sm-7">
			              		<input type="text" class="form-control" id="smtp_port" value="<?php echo get_preference('smtp_port'); ?>" name="preference[smtp_port]">
							</div>
						</div>
						<div ng-show="isSMTP" class="form-group">
							<label for="preference[ssl_tls]" class="col-sm-3 control-label">
								<?php echo $language->get('label_ssl_tls'); ?><i class="required">*</i>
								<span data-toggle="tooltip" title="" data-original-title="<?php echo $language->get('hint_ssl_tls'); ?>"></span>
							</label>
							<div class="col-sm-7">
								<select id="ssl_tls" name="preference[ssl_tls]"> 
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

					<!-- FTP setting start -->
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

					<?php endif; ?>
				</div>
				<div class="box-footer">
					<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'update_store')) : ?>
					<div class="form-group">
						<label for="address" class="col-sm-3 control-label"></label>
						<div class="col-sm-7">
							<a id="back-btn" class="btn btn-danger" href="store.php">
								<span class="fa fa-fw fa-angle-left"></span> 
								<?php echo $language->get('button_back'); ?>
							</a>
							<button id="update-store-btn" class="btn btn-info pull-right" type="button" data-form="#store-form" data-loading-text="Updating...">
								<span class="fa fa-fw fa-pencil"></span> 
								<?php echo $language->get('button_update'); ?>
							</button>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</form>

		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">
					<?php echo $language->get('text_logo'); ?>
				</h3>
				<button  type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
					<i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-minus'; ?>"></i>
				</button>
			</div>
			<div class="box-body">
				<form id="uploadlogo" class="upload-form" action="" method="post" enctype="multipart/form-data">
					<input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
					<div class="form-group">
						<label for="logo" class="col-sm-3 control-label">&nbsp;</label>
						<div class="col-sm-2 text-center">	            
							<div id="logo_preview">
								<?php if (store('logo')): ?>
									<img id="logo" src="../assets/itsolution24/img/logo-favicons/<?php echo store('logo'); ?>">
								<?php else: ?>
									<img id="logo" src="../assets/itsolution24/img/logo-favicons/nologo.png">
								<?php endif; ?>
							</div>
							<p>
								W/H: <?php echo $language->get('label_logo_size'); ?>
							</p>
						</div>
						<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'upload_logo')) : ?>
						    <div class="col-sm-4">	            
								<div class="upload-field" id="selectImage">
									<input class="file-field" type="file" name="file" id="file" required>
									<div class="message"></div>
									<button type="submit" class="btn btn-sm btn-warning btn-logo-upload" data-loading-text="Uploading...">
										<span class="fa fa-fw fa-upload"></span> 
										<?php echo $language->get('button_upload'); ?>
									</button>
									<img class="loader logo-loader" src="../assets/itsolution24/img/loading.gif">
								</div>
						    </div>
						<?php endif; ?>
						<div class="clearfix"></div>
					</div>
				</form>
			</div>
	    </div>
	    <div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">
					<?php echo $language->get('text_favicon'); ?>
				</h3>
				<button  type="button" class="btn btn-box-tool add-new-btn" data-widget="collapse" data-collapse="true">
					<i class="fa <?php echo !create_box_state() ? 'fa-minus' : 'fa-minus'; ?>"></i>
				</button>
			</div>
			<div class="box-body">
				<form id="uploadFavicon" class="upload-form" action="" method="post" enctype="multipart/form-data">
					<input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
					<div class="form-group">
						<label for="favicon" class="col-sm-3 control-label">&nbsp;</label>
						<div class="col-sm-2 text-center">	            
							<div id="favicon_preview">
								<?php if (store('favicon')): ?>
									<img id="favicon" src="../assets/itsolution24/img/logo-favicons/<?php echo store('favicon'); ?>">
								<?php else: ?>
									<img id="favicon" src="../assets/itsolution24/img/logo-favicons/nofavicon.png">
								<?php endif; ?>
							</div>
							<p>W/H: <?php echo $language->get('label_favicon_size'); ?></p>
						</div>
						<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'upload_favicon')) : ?>
							<div class="col-sm-4">	            
								<div class="upload-field" id="selectFavicon">
									<input class="file-field" type="file" name="faviconFile" id="faviconFile" required>
									<div class="message"></div>
									<button type="submit" class="btn btn-sm btn-warning btn-favicon-upload" data-loading-text="Uploading...">
										<span class="fa fa-fw fa-upload"></span> 
										<?php echo $language->get('button_upload'); ?>
									</button>
									<img class="loader favicon-loader" src="../assets/itsolution24/img/loading.gif">
								</div>
							</div>
						<?php endif; ?>
						<div class="clearfix"></div>
					</div>
			  	</form>
			</div>
		</div>
		<div class="alert alert-info" role="alert">
			<p>
	            <a class="btn btn-warning btn-xs pull-right" target="_blank" href="<?php echo root_url(); ?>/<?php echo ADMINDIRNAME;?>/cron.php">
	            	Run Manually
	            </a>
            </p>
            <p><strong>Cron Job</strong> (Run at 1:00 AM daily):</p>
	        <pre>0 1 * * * wget -qO- <?php echo root_url(); ?>/<?php echo ADMINDIRNAME;?>/cron.php &gt;/dev/null 2&gt;&amp;1</pre>
		</div>
	</section>
	<!-- Content End-->

</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>