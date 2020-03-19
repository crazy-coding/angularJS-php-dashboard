<?php $language->load('usergroup'); ?>
<?php
$permissions = array(
  'report' => array(
    'read_sell_report' => 'Read Sell Report', 
    'read_accounting_report' => 'Read Accounting Report', 
    'read_overview_report' => 'Read Overview Report', 
    'read_collection_report' => 'Read Collec. Report',
    'read_full_collection_report' => 'Read Full Collec. Report',
    'read_customer_due_collection_report' => 'Read Customer Due Collection Rpt',
    'read_supplier_due_paid_report' => 'Read Suppler Due Paid Rpt',
    'read_analytics' => 'Read Analytics', 
    'send_report_via_email' => 'Send Report via Email',
    'read_summary_report' => 'Read Summary Report',
    'read_overview_report' => 'Read Overview Report',
    'read_buy_report' => 'Read Buy Report',
    'read_buy_payment_report' => 'Read Buy Payment Report',
    'read_sell_payment_report' => 'Read sell Payment Report',
    'read_sell_tax_report' => 'Read Sell Tax Report',
    'read_buy_tax_report' => 'Read Buy Tax Report',
    'read_tax_overview_report' => 'Read Tax Overview Report',
    'read_stock_report' => 'Read Stock Report',
    'send_report_email' => 'Send Report Email',
  ),
  'accounting' => array(
    'withdraw' => 'Withdraw',
    'deposit' => 'Deposit',
    'transfer' => 'Transfer',
    'create_bank_account' => 'Create Bank Account',
    'update_bank_account' => 'Update Bank Account',
    'delete_bank_account' => 'Delete Bank Account',
    'read_bank_account' => 'View Bank Account',
    'read_bank_account_sheet' => 'View Bank Account Sheet',
    'read_bank_transfer' => 'View Bank Transfer',
    'read_bank_transactions' => 'View Bank Transactions',
  ),
  'purchase_and_sell' => array(
    'read_purchase_list' => 'View Purchase List',
    'create_purchase_invoice' => 'Create Purchase Invoice',
    'update_purchase_invoice_info' => 'Update Purchase Info',
    'delete_purchase_invoice' => 'Delete Purchase Invoice',
    'purchase_payment' => 'purchase_payment',
    'purchase_return' =>  'purchase_return',
    'read_buy_transaction' => 'Read Purchase Transaction',
    'read_sell_transaction' => 'Read Sell Transaction',
    'create_invoice' => 'Create sell Invoice', 
    'read_invoice_list' => 'Read Invoice List',
    'view_invoice' => 'View Invoice',
    'return_item' => 'Sell Return',
    'view_invoice' => 'View Sell Invoice',
    'email_invoice' => 'Send Invoice via Email', 
    'update_invoice_info' => 'Update Invocie Info',
    'delete_invoice' => 'Delete Invoice',
    'sell_return' => 'sell Return',
    'payment' => 'sell Payment',
  ),
  'due' => array(
    'create_due' => 'Create Due',
  ),
  'transfer' => array(
    'read_transfer' => 'Read Transfer',
    'add_transfer' => 'Add Transfer',
    'update_transfer' => 'Update Transfer',
  ),
  'giftcard' => array(
    'read_giftcard' => 'Read Giftcard',
    'add_giftcard' => 'Add Giftcard',
    'update_giftcard' => 'Update Giftcard',
    'delete_giftcard' => 'Delete Giftcard',
    'giftcard_topup' => 'Giftcard Topup',
    'read_giftcard_topup' => 'Read Giftcard Topup',
    'delete_giftcard_topup' => 'Delete Giftcard Topup',
  ),
  'product' => array(
    'read_product' => 'Read Product List',
    'create_product' => 'Create Product', 
    'update_product' => 'Update Product', 
    'delete_product' => 'Delete Product',
    'import_product' => 'Import Product',
    'product_bulk_action' => 'Product Bulk Action',
    'delete_all_product' => 'Delete All Product',
    'read_category' => 'Read Category List',
    'create_category' => 'Create Category', 
    'update_category' => 'Update Category', 
    'delete_category' => 'Delete Category',
    'read_stock_alert' => 'Read Stock Alert',
    'read_expired_product' => 'Read Expired Product List',
    'print_barcode' => 'Print Barcode',
    'restore_all_product' => 'Restore All Product',
  ),
  'supplier' => array(
    'read_supplier' => 'Read Supplier List',
    'create_supplier' => 'Create Supplier',
    'update_supplier' => 'Update Supplier',
    'delete_supplier' => 'Delete Supplier',
    'read_supplier_profile' => 'Read Supplier Profile',
  ),
  'storebox' => array(
    'read_box' => 'Read Box',
    'create_box' => 'Create Box',
    'update_box' => 'Update Box',
    'delete_box' => 'Delete Box',
  ),
  'unit' => array(
    'read_unit' => 'Read Unit',
    'create_unit' => 'Create Unit',
    'update_unit' => 'Update Unit',
    'delete_unit' => 'Delete Unit',
  ),
  'taxrate' => array(
    'read_taxrate' => 'Read Taxrate',
    'create_taxrate' => 'Create Taxrate',
    'update_taxrate' => 'Update Taxrate',
    'delete_taxrate' => 'Delete Taxrate',
  ),
  'expenditure' => array(
    'read_expense' => 'Read Expense',
    'create_expense' => 'Create Expense',
    'update_expense' => 'Update Expense',
    'delete_expense' => 'Delete Expense',
  ),
  'loan' => array(
    'read_loan' => 'Read Loan',
    'read_loan_summary' => 'Read Loan Summary',
    'take_loan' => 'Take Loan',
    'update_loan' => 'Update Loan',
    'delete_loan' => 'Delete Loan',
    'loan_pay' => 'Loan Pay',
  ),
  'customer' => array(
    'read_customer' => 'Read Customer List',
    'read_customer_profile' => 'Read Customer Profile',
    'create_customer' => 'Create Customer', 
    'update_customer' => 'Update Customer', 
    'delete_customer' => 'Delete Customer', 
  ),
  'user' => array(
    'read_user' => 'Read User List',
    'create_user' => 'Create User', 
    'update_user' => 'Update User', 
    'delete_user' => 'Delete User', 
    'change_password' => 'Change Password',
  ),
  'usergroup' => array(
    'read_usergroup' => 'Read Usergroup List',
    'create_usergroup' => 'Create Usergroup', 
    'update_usergroup' => 'Update Usergroup', 
    'delete_usergroup' => 'Delete Usergroup', 
  ),
  'currency' => array(
    'read_currency' => 'Read Currency',
    'create_currency' => 'Add Currency',
    'update_currency' => 'Update Currency',
    'change_currency' => 'Change Currency',
    'delete_currency' => 'Delete Currency',
  ),
  'filemanager' => array(
    'read_filemanager' => 'Read Filemanager',
  ),
  'payment_method' => array(
    'read_pmethod' => 'Read Payment Method List',
    'create_pmethod' => 'Create Payment Method',
    'update_pmethod' => 'Update Payment Method',
    'delete_pmethod' => 'Delete Payment Method',
  ),
  'store' => array(
    'read_store' => 'Read Store List',
    'create_store' => 'Create Store',
    'update_store' => 'Update Store',
    'delete_store' => 'Delete Store',
    'activate_store' => 'Active Store',
    'upload_favicon' => 'Upload Favicon',
    'upload_logo' => 'Upload Logo',
  ),
  'printer' => array(
    'read_printer' => 'View Printer',
    'create_printer' => 'Add Printer',
    'update_printer' => 'Update Printer',
    'delete_printer' => 'Delete Printer',
  ),
  'sms' => array(
    'read_sms_setting' => 'View SMS Setting',
    'update_sms_setting' => 'Update SMS Setting',
    'send_sms' => 'Send SMS',
  ),
  'settings' => array(
    'read_user_preference' => 'Read User Preference',
    'update_user_preference' => 'Update User Preference',
    'filtering' => 'Filtering',
    'read_keyboard_shortcut' => 'Keyboard Shortcut',
    'language_sync' => 'Language Sync',
    'backup' => 'Database Backup',
    'restore' => 'Database Restore',
    'show_buy_price' => 'Show Buy Price',
    'show_profit' => 'Show Profit',
    'show_graph' => 'Show Graph',
  ),
);
?>

<h4 class="sub-title">
  <?php echo $language->get('text_update_title'); ?>
</h4>

<form class="form-horizontal" id="user-group-form" action="user_group.php" method="post">
  
  <input type="hidden" id="action_type" name="action_type" value="UPDATE">
  <input type="hidden" id="group_id" name="group_id" value="<?php echo $usergroup['group_id']; ?>">
  
  <div class="box-body">
    <div class="form-group">
      <label for="name" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_name'), null); ?>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="name" ng-model="usergroupName" ng-init="usergroupName='<?php echo $usergroup['name']; ?>'" value="<?php echo $usergroup['name']; ?>" name="name" required>
      </div>
    </div>

    <div class="form-group">
      <label for="slug" class="col-sm-3 control-label">
        <?php echo sprintf($language->get('label_slug'), null); ?><i class="required">*</i>
      </label>
      <div class="col-sm-7">
        <input type="text" class="form-control" id="slug" value="{{ usergroupName | strReplace:' ':'_' | lowercase }}" name="slug" required readonly>
      </div>
    </div>

    <hr>

    <div class="form-group mb-0">
      <div class="col-sm-12">
        <h4 class="pull-left">
          <?php echo $language->get('text_permission'); ?>
        </h4>
        <button data-form="#user-group-form" data-datatable="#user-group-list" class="btn btn-info btn-lg pull-right user-group-update" name="btn_edit_user" data-loading-text="Updating...">
          <span class="fa fa-fw fa-pencil"></span>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>

    <hr>

    <?php $the_permissions = unserialize($usergroup['permission']); ?>

    <div class="form-group permission-list">
      <?php foreach ($permissions as $type => $lists) : ?>
      <div class="col-sm-3">
        <h4>
          <input type="checkbox" id="<?php echo $type; ?>_action" onclick="$('.<?php echo $type; ?>').prop('checked', this.checked);">
          <label for="<?php echo $type; ?>_action">
            <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
          </label>
        </h4>
        <div class="filter-searchbox">
            <input ng-model="search_<?php echo $type; ?>" class="form-control" type="text" placeholder="<?php echo $language->get('search'); ?>">
        </div>
        <div class="well well-sm permission-well">
          <div filter-list="search_<?php echo $type; ?>">
            <?php foreach ($lists as $key => $name) : ?>
              <div>
                <input type="checkbox" class="<?php echo $type; ?>" id="<?php echo $key; ?>" value="true" name="access[<?php echo $key; ?>]"<?php echo isset($the_permissions['access'][$key]) ? ' checked' : null; ?>>
                <label for="<?php echo $key; ?>"><?php echo ucfirst($name); ?></label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="box-footer">
    <div class="form-group">
      <div class="col-sm-12 text-center">
        <button data-form="#user-group-form" data-datatable="#user-group-list" class="btn btn-lg btn-info user-group-update" name="btn_edit_user" data-loading-text="Updating...">
          <span class="fa fa-fw fa-pencil"></span>
          <?php echo $language->get('button_update'); ?>
        </button>
      </div>
    </div>
  </div>
</form>