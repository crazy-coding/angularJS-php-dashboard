<!-- Main Sidebar Start -->
<aside class="main-sidebar">
  <section class="sidebar">

    <!--  Sidebar User Panel Start-->
    <div class="user-panel">
      <div class="pull-left image">
        <svg class="svg-icon"><use href="#icon-avatar"></svg>
      </div>
      <div class="pull-left info">
        <p class="username" title="<?php echo $user->getUserName(); ?>">
          <?php echo ucfirst(limit_char($user->getUserName(), 15)); ?>
        </p>
        <a href="" onClick="return false;">
          <i class="fa fa-circle user-status-dot"></i> 
          <?php echo limit_char($user->getRole(), 14); ?> 
        </a>
      </div>
    </div>  
    <!-- Sidebar User Panel End -->

    <!-- Sidebar Menu Start -->
    <ul class="sidebar-menu">
      <li class="<?php echo current_nav() == 'admin' || current_nav() == 'dashboard' ? ' active' : null; ?>">
        <a href="dashboard.php">
          <svg class="svg-icon"><use href="#icon-dashboard"></svg>
          <span>
            <?php echo $language->get('menu_dashboard'); ?>
          </span>
        </a>
      </li>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_invoice')) : ?>
        <li class="<?php echo current_nav() == 'pos' ? 'active' : null; ?>">
          <a href="pos.php">
            <svg class="svg-icon"><use href="#icon-create-invoice"></svg>
            <span>
              <?php echo $language->get('menu_pos'); ?>
            </span>
          </a>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_invoice_list')) : ?>
        <li class="<?php echo current_nav() == 'invoice' ? ' active' : null; ?>">
          <a href="invoice.php" title="<?php echo $language->get('text_invoice'); ?>">
            <svg class="svg-icon"><use href="#icon-invoice-list"></svg>
            <span>
              <?php echo $language->get('menu_invoice'); ?>
            </span>
          </a>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_customer') || $user->hasPermission('access', 'read_sell_transaction')) : ?>
        <li class="treeview<?php echo current_nav() == 'customer' || current_nav() == 'customer_profile' || current_nav() == 'sell_transaction' ? ' active' : null; ?>">
          <a href="customer.php">
            <svg class="svg-icon"><use href="#icon-group"></svg>
            <span>
              <?php echo $language->get('menu_customer'); ?>
            </span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_customer')): ?>
              <li class="<?php echo current_nav() == 'customer' ? ' active' : null; ?>">
                <a href="customer.php">
                  <svg class="svg-icon"><use href="#icon-group"></svg>
                  <span>
                    <?php echo $language->get('menu_customer'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_transaction')): ?>
              <li class="<?php echo current_nav() == 'sell_transaction' ? ' active' : null; ?>">
                <a href="sell_transaction.php">
                  <svg class="svg-icon"><use href="#icon-list"></svg>
                   <?php echo $language->get('menu_transaction_list'); ?>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_supplier')) : ?>
        <li class="<?php echo current_nav() == 'supplier' || current_nav() == 'supplier_profile' ? 'active' : null; ?>">
          <a href="supplier.php">
            <svg class="svg-icon"><use href="#icon-supplier"></svg>
            <span>
              <?php echo $language->get('menu_supplier'); ?>
            </span>
          </a>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_product')) : ?>
        <li class="treeview<?php echo current_nav() == 'product' || current_nav() == 'product_details' || current_nav() == 'category' || current_nav() == 'import_product' || current_nav() == 'stock_alert' || current_nav() == 'expired' ? ' active' : null; ?>">
          <a href="product.php">
            <svg class="svg-icon"><use href="#icon-star"></svg>
            <span>
              <?php echo $language->get('menu_product'); ?>
            </span> 
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_product')): ?>
              <li class="<?php echo current_nav() == 'product' || current_nav() == 'product_details' ? ' active' : null; ?>">
                <a href="product.php">
                  <svg class="svg-icon"><use href="#icon-star"></svg>
                  <?php echo $language->get('menu_product'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_category')): ?>
              <li class="<?php echo current_nav() == 'category' ? ' active' : null; ?>">
                <a href="category.php">
                  <svg class="svg-icon"><use href="#icon-category"></svg>
                   <?php echo $language->get('menu_category'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'import_product')): ?>
              <li class="<?php echo current_nav() == 'import_product' ? ' active' : null; ?>">
                <a href="import_product.php">
                  <svg class="svg-icon"><use href="#icon-import"></svg>
                  <?php echo $language->get('menu_product_import'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_stock_alert')): ?>
              <li class="<?php echo current_nav() == 'stock_alert' ? ' active' : null; ?>">
                <a href="stock_alert.php">
                  <svg class="svg-icon"><use href="#icon-alert"></svg>
                  <?php echo $language->get('menu_stock_alert'); ?>
                  <?php if (total_out_of_stock() > 0) : ?>
                    <span class="label label-danger bg-yellow">
                      <?php echo total_out_of_stock(); ?>
                    </span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_expired_product')): ?>
              <li class="<?php echo current_nav() == 'expired' ? ' active' : null; ?>">
                <a href="expired.php">
                  <svg class="svg-icon"><use href="#icon-expired"></svg>
                  <?php echo $language->get('menu_expired'); ?>
                  <?php if (total_expired() > 0) : ?>
                    <span class="label label-warning bg-yellow">
                      <?php echo total_expired(); ?>
                    </span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_transfer')) : ?>
        <li class="treeview<?php echo current_nav() == 'transfer' || current_nav() == 'transfer_add' ? ' active' : null; ?>">
          <a href="transfer.php">
            <svg class="svg-icon"><use href="#icon-transfer"></svg>
            <span>
              <?php echo $language->get('menu_transfer'); ?>
            </span> 
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_transfer')): ?>
              <li class="<?php echo current_nav() == 'transfer' ? ' active' : null; ?>">
                <a href="transfer.php">
                  <svg class="svg-icon"><use href="#icon-transfer"></svg>
                  <?php echo $language->get('menu_transfer_list'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'add_transfer') || $user->hasPermission('access', 'update_transfer')): ?>
              <li class="<?php echo current_nav() == 'transfer' ? ' active' : null; ?>">
                <a href="transfer.php?box_state=open">
                  <svg class="svg-icon"><use href="#icon-plus"></svg>
                   <?php echo $language->get('menu_add_transfer'); ?>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_purchase_list') || $user->hasPermission('access', 'create_purchase_invoice') || $user->hasPermission('access', 'read_buy_transaction')) : ?>
        <li class="treeview<?php echo current_nav() == 'purchase' || current_nav() == 'buy_transaction' ? ' active' : null; ?>">
          <?php if($user->getGroupId() == 1 || $user->hasPermission('access', 'read_purchase_list')) : ?>
          <a href="purchage.php">
            <svg class="svg-icon"><use href="#icon-money"></svg>
            <span><?php echo $language->get('menu_purchases'); ?></span>
             <i class="fa fa-angle-left pull-right"></i>
           </a>
          <?php endif; ?>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_purchase_invoice')) : ?>
              <li class="<?php echo current_nav() == 'add_purchase' ? 'active' : null; ?>">
                <a ng-click="BuyingProductModal({sup_id:'',invoice_id:''});" onClick="return false;" href="purchase.php">
                  <svg class="svg-icon"><use href="#icon-plus"></svg>
                  <span>
                    <?php echo $language->get('menu_add_purchase'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_purchase_list')) : ?>
              <li class="<?php echo current_nav() == 'purchase' ? 'active' : null; ?>">
                <a href="purchase.php">
                  <svg class="svg-icon"><use href="#icon-list"></svg>
                  <span>
                    <?php echo $language->get('menu_purchase_list'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_purchase_list')) : ?>
              <li class="<?php echo (current_nav() == 'purchase') && isset($request->get['type']) && ($request->get['type'] == 'due') ? 'active' : null; ?>">
                <a href="purchase.php?type=due">
                  <svg class="svg-icon"><use href="#icon-money"></svg>
                  <span>
                    <?php echo $language->get('menu_due_invoice'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_transaction')) : ?>
              <li class="<?php echo current_nav() == 'buy_transaction' ? 'active' : null; ?>">
                <a href="buy_transaction.php">
                  <svg class="svg-icon"><use href="#icon-report"></svg>
                  <span>
                    <?php echo $language->get('menu_transaction_list'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <li class="treeview<?php echo current_nav() == 'giftcard' || current_nav() == 'giftcard_topup' ? ' active' : null; ?>">
        <?php if($user->getGroupId() == 1 || $user->hasPermission('access', 'read_giftcard')): ?>
        <a href="giftcard.php">
          <svg class="svg-icon"><use href="#icon-card1"></svg>
          <span><?php echo $language->get('menu_giftcard'); ?></span>
           <i class="fa fa-angle-left pull-right"></i>
         </a>
        <?php endif; ?>
        <ul class="treeview-menu">
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_giftcard')) : ?>
            <li class="<?php echo current_nav() == 'giftcard' ? 'active' : null; ?>">
              <a href="giftcard.php">
                <svg class="svg-icon"><use href="#icon-card1"></svg>
                <span>
                  <?php echo $language->get('menu_giftcard_list'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_giftcard_topup')) : ?>
            <li class="<?php echo current_nav() == 'giftcard_topup' ? 'active' : null; ?>">
              <a href="giftcard_topup.php">
                <svg class="svg-icon"><use href="#icon-list"></svg>
                <span>
                  <?php echo $language->get('menu_giftcard_topup'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </li>

      <li class="treeview<?php echo current_nav() == 'report_overview' || current_nav() == 'report_collection' || current_nav() == 'report_sell_itemwise' || current_nav() == 'report_sell_categorywise' || current_nav() == 'report_sell_supplierwise' || current_nav() == 'report_buy_itemwise' || current_nav() == 'report_buy_categorywise' || current_nav() == 'report_buy_supplierwise' || current_nav() == 'report_customer_due_collection' || current_nav() == 'report_supplier_due_paid' || current_nav() == 'report_sell_payment' || current_nav() == 'report_buy_payment' || current_nav() == 'report_sell_tax' || current_nav() == 'report_buy_tax' || current_nav() == 'report_tax_overview' || current_nav() == 'report_stock'  ? ' active' : null; ?>">
        <?php if($user->getGroupId() == 1 || $user->hasPermission('access', 'read_overview_report') || $user->hasPermission('access', 'read_collection_report') || $user->hasPermission('access', 'read_sell_report') || $user->hasPermission('access', 'read_buy_report') || $user->hasPermission('access', 'read_sell_payment_report') || $user->hasPermission('access', 'read_buy_payment_report') || $user->hasPermission('access', 'read_sell_tax_report') || $user->hasPermission('access', 'read_buy_tax_report') || $user->hasPermission('access', 'read_tax_overview_report') || $user->hasPermission('access', 'read_stock_report')): ?>
        <a href="report_overview.php">
          <svg class="svg-icon"><use href="#icon-report"></svg>
          <span><?php echo $language->get('menu_reports'); ?></span>
           <i class="fa fa-angle-left pull-right"></i>
         </a>
        <?php endif; ?>

        <ul class="treeview-menu">
          
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_overview_report')) : ?>
            <li class="<?php echo current_nav() == 'report_overview' ? ' active' : null; ?>">
              <a href="report_overview.php">
                <svg class="svg-icon"><use href="#icon-eye"></svg>
                <?php echo $language->get('menu_report_overview'); ?>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_collection_report')) : ?>
            <li class="<?php echo current_nav() == 'report_collection' ? ' active' : null; ?>">
              <a href="report_collection.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <?php echo $language->get('menu_report_collection'); ?>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_customer_due_collection')) : ?>
            <li class="<?php echo current_nav() == 'report_customer_due_collection' ? ' active' : null; ?>">
              <a href="report_customer_due_collection.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <?php echo $language->get('menu_report_due_collection'); ?>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_supplier_due_paid')) : ?>
            <li class="<?php echo current_nav() == 'report_supplier_due_paid' ? ' active' : null; ?>">
              <a href="report_supplier_due_paid.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <?php echo $language->get('menu_report_due_paid'); ?>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_report')) : ?>
            <li class="<?php echo current_nav() == 'report_sell_itemwise' || current_nav() == 'report_sell_categorywise' || current_nav() == 'report_sell_supplierwise' ? ' active' : null; ?>">
              <a href="report_sell_itemwise.php"> 
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <?php echo $language->get('menu_sell_report'); ?>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_report')) : ?>
            <li class="<?php echo current_nav() == 'report_buy_itemwise' || current_nav() == 'report_buy_categorywise' || current_nav() == 'report_buy_supplierwise' ? 'active' : null; ?>">
              <a href="report_buy_supplierwise.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <span>
                  <?php echo $language->get('menu_buy_report'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_payment_report')) : ?>
            <li class="<?php echo current_nav() == 'report_sell_payment' ? 'active' : null; ?>">
              <a href="report_sell_payment.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <span>
                  <?php echo $language->get('menu_sell_payment_report'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_payment_report')) : ?>
            <li class="<?php echo current_nav() == 'report_buy_payment' ? 'active' : null; ?>">
              <a href="report_buy_payment.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <span>
                  <?php echo $language->get('menu_buy_payment_report'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_tax_report')) : ?>
            <li class="<?php echo current_nav() == 'report_sell_tax' ? 'active' : null; ?>">
              <a href="report_sell_tax.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <span>
                  <?php echo $language->get('menu_tax_report'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_tax_report')) : ?>
            <li class="<?php echo current_nav() == 'report_buy_tax' ? 'active' : null; ?>">
              <a href="report_buy_tax.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <span>
                  <?php echo $language->get('menu_buy_tax_report'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_tax_overview_report')) : ?>
            <li class="<?php echo current_nav() == 'report_tax_overview' ? 'active' : null; ?>">
              <a href="report_tax_overview.php">
                <svg class="svg-icon"><use href="#icon-eye"></svg>
                <span>
                  <?php echo $language->get('menu_tax_overview_report'); ?>
                </span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_stock_report')) : ?>
            <li class="<?php echo current_nav() == 'report_stock' ? 'active' : null; ?>">
              <a href="report_stock.php">
                <svg class="svg-icon"><use href="#icon-report"></svg>
                <span>
                  <?php echo $language->get('menu_report_stock'); ?>
                </span>
              </a>
            </li>
            <?php endif; ?>
        </ul>
      </li>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_analytics')) : ?>
        <li class="<?php echo current_nav() == 'analytics' ? 'active' : null; ?>">
          <a href="analytics.php">
            <svg class="svg-icon"><use href="#icon-analytics"></svg>
            <span>
              <?php echo $language->get('menu_analytics'); ?>
            </span>
          </a>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'send_sms') || $user->hasPermission('access', 'read_sms_setting') || $user->hasPermission('access', 'read_sms_report')) : ?>
        <li class="treeview<?php echo current_nav() == 'sms_send' || current_nav() == 'sms_setting' || current_nav() == 'sms_report' ? ' active' : null; ?>">
          <a href="sms_send.php">
            <svg class="svg-icon"><use href="#icon-sms"></svg>
            <span>
              <?php echo $language->get('menu_sms'); ?>
            </span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'send_sms')) : ?>
              <li class="<?php echo current_nav() == 'sms_send' ? 'active' : null; ?>">
                <a href="sms_send.php">
                  <svg class="svg-icon"><use href="#icon-sms"></svg>
                  <span>
                    <?php echo $language->get('menu_send_sms'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sms_report')) : ?>
              <li class="<?php echo current_nav() == 'sms_report' ? 'active' : null; ?>">
                <a href="sms_report.php">
                  <svg class="svg-icon"><use href="#icon-report"></svg>
                  <span>
                    <?php echo $language->get('menu_sms_report'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sms_setting')) : ?>
              <li class="<?php echo current_nav() == 'sms_setting' ? 'active' : null; ?>">
                <a href="sms_setting.php">
                  <svg class="svg-icon"><use href="#icon-settings"></svg>
                  <span>
                    <?php echo $language->get('menu_sms_setting'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'deposit') || $user->hasPermission('access', 'withdraw') || $user->hasPermission('access', 'transfer') || $user->hasPermission('access', 'read_bank_transfer') || $user->hasPermission('access', 'read_bank_transactions') || $user->hasPermission('access', 'read_bank_account') || $user->hasPermission('access', 'create_bank_account') || $user->hasPermission('access', 'read_bank_account_sheet')) : ?>
        <li class="treeview<?php echo current_nav() == 'bank_transactions' || current_nav() == 'new_deposit' || current_nav() == 'new_withdraw' || current_nav() == 'bank_transfer' || current_nav() == 'bank_transactions' || current_nav() == 'bank_account' || current_nav() == 'bank_account_sheet' ? ' active' : null; ?>">
          <a href="bank_transactions.php?type=report">
            <svg class="svg-icon"><use href="#icon-bank"></svg>
            <span>
              <?php echo $language->get('menu_accounting'); ?>
            </span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'deposit')): ?>
              <li class="">
                <a ng-click="BankingDepositModal()" onClick="return false;" href="#">
                  <svg class="svg-icon"><use href="#icon-plus"></svg>
                  <?php echo $language->get('menu_new_deposit'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'withdraw')): ?>
              <li class="">
                <a ng-click="BankingWithdrawModal()" onClick="return false;" href="bank_account.php">
                  <svg class="svg-icon"><use href="#icon-expense"></svg>
                  <?php echo $language->get('menu_new_withdraw'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'transfer')): ?>
              <li>
                <a ng-click="BankTransferModal()" onClick="return false;" href="bank_account.php">
                  <svg class="svg-icon"><use href="#icon-plus"></svg>
                  <?php echo $language->get('menu_new_transfer'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_bank_transfer')): ?>
              <li class="<?php echo current_nav() == 'bank_transfer' ? ' active' : null; ?>">
                <a href="bank_transfer.php">
                  <svg class="svg-icon"><use href="#icon-import"></svg>
                  <?php echo $language->get('menu_list_transfer'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_bank_transactions')): ?>
              <li class="<?php echo current_nav() == 'bank_transactions' ? ' active' : null; ?>">
                <a href="bank_transactions.php">
                  <svg class="svg-icon"><use href="#icon-list"></svg>
                  <?php echo $language->get('menu_list_transactions'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_bank_account') || $user->hasPermission('access', 'create_bank_account')): ?>
              <li class="<?php echo current_nav() == 'bank_account' ? ' active' : null; ?>">
                <a href="bank_account.php">
                  <svg class="svg-icon"><use href="#icon-bank"></svg>
                  <?php echo $language->get('menu_bank_accounts'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_bank_account_sheet')): ?>
              <li class="<?php echo current_nav() == 'bank_account_sheet' ? ' active' : null; ?>">
                <a href="bank_account_sheet.php">
                  <svg class="svg-icon"><use href="#icon-report"></svg>
                  <?php echo $language->get('menu_balance_sheet'); ?>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_expense') || $user->hasPermission('access', 'create_expense') || $user->hasPermission('access', 'read_expense_category') || $user->hasPermission('access', 'read_expense_summary')) : ?>
        <li class="treeview<?php echo current_nav() == 'expense' || current_nav() == 'expense_category' || current_nav() == 'expense_summary' ? ' active' : null; ?>">
          <a href="expense.php">
            <svg class="svg-icon"><use href="#icon-expense"></svg>
            <span>
              <?php echo $language->get('menu_expenditure'); ?>
            </span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_expense')): ?>
              <li class="<?php echo current_nav() == 'expense' ? ' active' : null; ?>">
                <a href="expense.php?box_state=open">
                  <svg class="svg-icon"><use href="#icon-plus"></svg>
                  <?php echo $language->get('menu_create_expense'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_expense')): ?>
              <li class="<?php echo current_nav() == 'expense' ? ' active' : null; ?>">
                <a href="expense.php">
                  <svg class="svg-icon"><use href="#icon-list"></svg>
                  <?php echo $language->get('menu_expense_list'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_expense_category')): ?>
              <li class="<?php echo current_nav() == 'expense_category' ? ' active' : null; ?>">
                <a href="expense_category.php">
                  <svg class="svg-icon"><use href="#icon-list"></svg>
                  <?php echo $language->get('menu_category'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_expense_summary')): ?>
              <li class="<?php echo current_nav() == 'expense_summary' ? ' active' : null; ?>">
                <a ng-click="ExpenseSummaryModal();" onClick="return false;" href="expense.php">
                  <svg class="svg-icon"><use href="#icon-report"></svg>
                  <?php echo $language->get('menu_summary'); ?>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_loan') || $user->hasPermission('access', 'take_loan') || $user->hasPermission('access', 'read_loan_summary')) : ?>
        <li class="treeview<?php echo current_nav() == 'loan' || current_nav() == 'loan_summary' ? ' active' : null; ?>">
          <a href="loan.php">
            <svg class="svg-icon"><use href="#icon-loan"></svg>
            <span>
              <?php echo $language->get('menu_loan_manager'); ?>
            </span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_loan')): ?>
              <li class="<?php echo current_nav() == 'loan' ? ' active' : null; ?>">
                <a href="loan.php">
                  <svg class="svg-icon"><use href="#icon-list"></svg>
                  <?php echo $language->get('menu_loan_list'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'take_loan')): ?>
              <li class="<?php echo current_nav() == 'loan' ? ' active' : null; ?>">
                <a href="loan.php?box_state=open">
                  <svg class="svg-icon"><use href="#icon-plus"></svg>
                  <?php echo $language->get('menu_take_loan'); ?>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_loan_summary')): ?>
              <li class="<?php echo current_nav() == 'loan_summary' ? ' active' : null; ?>">
                <a href="loan_summary.php?box_state=open">
                  <svg class="svg-icon"><use href="#icon-report"></svg>
                  <?php echo $language->get('menu_loan_summary'); ?>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_user') || $user->hasPermission('access', 'read_usergroup') || $user->hasPermission('access', 'change_password')) : ?>
        <li class="treeview<?php echo current_nav() == 'user' || current_nav() == 'user_group' || current_nav() == 'password' ? ' active' : null; ?>">
          <a href="user.php">
            <svg class="svg-icon"><use href="#icon-user"></svg>
            <span>
              <?php echo $language->get('menu_user'); ?>
            </span> 
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_user')) : ?>
              <li class="<?php echo current_nav() == 'user' ? 'active' : null; ?>">
                <a href="user.php">
                  <svg class="svg-icon"><use href="#icon-user"></svg>
                  <span>
                    <?php echo $language->get('menu_user'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_usergroup')) : ?>
              <li class="<?php echo current_nav() == 'user_group' ? 'active' : null; ?>">
                <a href="user_group.php">
                  <svg class="svg-icon"><use href="#icon-group"></svg>
                  <span>
                    <?php echo $language->get('menu_usergroup'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
            <?php if (($user->getGroupId() == 1 || $user->hasPermission('access', 'change_password')) && !DEMO) : ?>
              <li class="<?php echo current_nav() == 'password' ? 'active' : null; ?>">
                <a href="password.php">
                  <svg class="svg-icon"><use href="#icon-password"></svg>
                  <span>
                    <?php echo $language->get('menu_password'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if (($user->getGroupId() == 1 || $user->hasPermission('access', 'read_filemanager')) && !DEMO) : ?>
        <li class="<?php echo current_nav() == 'filemanager' ? 'active' : null; ?>">
          <a href="filemanager.php">
            <svg class="svg-icon"><use href="#icon-folder"></svg>
            <span>
              <?php echo $language->get('menu_filemanager'); ?>
            </span>
          </a>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_store') || $user->hasPermission('access', 'read_user_preference') || $user->hasPermission('access', 'read_unit') || $user->hasPermission('access', 'read_taxrate') || $user->hasPermission('access', 'read_pmethod') || $user->hasPermission('access', 'read_currency') || $user->hasPermission('access', 'read_box') || $user->hasPermission('access', 'read_printer') || $user->hasPermission('access', 'sms_setting') || $user->hasPermission('access', 'language_sync') || $user->hasPermission('access', 'backup_restore')) : ?>

        <li class="treeview<?php echo current_nav() == 'store' || current_nav() == 'store_create' || current_nav() == 'user_preference' || current_nav() == 'store_single' || current_nav() == 'currency' || current_nav() == 'pmethod' || current_nav() == 'unit' || current_nav() == 'taxrate' || current_nav() == 'box' || current_nav() == 'printer' || current_nav() == 'sms_setting' || current_nav() == 'language_sync' || current_nav() == 'backup_restore' ? ' active' : null; ?>">
          
          <a href="store_single.php">
            <svg class="svg-icon"><use href="#icon-settings"></svg>
            <span>
              <?php echo $language->get('menu_system'); ?>
            </span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          
          <ul class="treeview-menu">

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_store')) : ?>
              <li class="treeview<?php echo current_nav() == 'store' || current_nav() == 'store_create' || current_nav() == 'store_single' ? ' active' : null; ?>">
                <a href="store.php">
                  <svg class="svg-icon"><use href="#icon-list"></svg>
                  <span>
                    <?php echo $language->get('menu_store'); ?>
                  </span>
                  <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_store')): ?>
                    <li class="<?php echo current_nav() == 'store_create' ? ' active' : null; ?>">
                      <a href="store_create.php">
                        <svg class="svg-icon"><use href="#icon-plus"></svg>
                        <?php echo $language->get('menu_create_store'); ?>
                      </a>
                    </li>
                  <?php endif; ?>
                  <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_store')): ?>
                    <li class="<?php echo current_nav() == 'store' ? ' active' : null; ?>">
                      <a href="store.php">
                        <svg class="svg-icon"><use href="#icon-list"></svg>
                        <?php echo $language->get('menu_store_list'); ?>
                      </a>
                    </li>
                  <?php endif; ?>
                  <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_store')) : ?>
                    <li class="<?php echo current_nav() == 'store_single' ? 'active' : null; ?>">
                      <a href="store_single.php">
                        <svg class="svg-icon"><use href="#icon-settings"></svg>
                        <span>
                          <?php echo $language->get('menu_store_setting'); ?>
                        </span>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_user_preference')) : ?>
              <li class="<?php echo current_nav() == 'user_preference' ? 'active' : null; ?>">
                <a href="user_preference.php">
                  <svg class="svg-icon"><use href="#icon-heart"></svg>
                  <span>
                    <?php echo $language->get('menu_user_preference'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_currency')) : ?>
              <li class="<?php echo current_nav() == 'currency' ? 'active' : null; ?>">
                <a href="currency.php">
                  <svg class="svg-icon"><use href="#icon-money"></svg>
                  <span>
                    <?php echo $language->get('menu_currency'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_pmethod')) : ?>
              <li class="<?php echo current_nav() == 'pmethod' ? 'active' : null; ?>">
                <a href="pmethod.php">
                  <svg class="svg-icon"><use href="#icon-money"></svg>
                  <span>
                    <?php echo $language->get('menu_pmethod'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_unit')) : ?>
              <li class="<?php echo current_nav() == 'unit' ? 'active' : null; ?>">
                <a href="unit.php">
                  <svg class="svg-icon"><use href="#icon-unit"></svg>
                  <span>
                    <?php echo $language->get('menu_unit'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_taxrate')) : ?>
              <li class="<?php echo current_nav() == 'taxrate' ? 'active' : null; ?>">
                <a href="taxrate.php">
                  <svg class="svg-icon"><use href="#icon-money"></svg>
                  <span>
                    <?php echo $language->get('menu_taxrate'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_box')) : ?>
              <li class="<?php echo current_nav() == 'box' ? 'active' : null; ?>">
                <a href="box.php">
                  <svg class="svg-icon"><use href="#icon-box"></svg>
                  <span>
                    <?php echo $language->get('menu_box'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_printer')) : ?>
              <li class="<?php echo current_nav() == 'printer' ? 'active' : null; ?>">
                <a href="printer.php">
                  <svg class="svg-icon"><use href="#icon-printer"></svg>
                  <span>
                    <?php echo $language->get('menu_printer'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if (($user->getGroupId() == 1 || $user->hasPermission('access', 'language_sync')) && !DEMO) : ?>
              <li class="<?php echo current_nav() == 'language_sync' ? 'active' : null; ?>">
                <a href="language_sync.php">
                  <svg class="svg-icon"><use href="#icon-backup"></svg>
                  <span>
                    <?php echo $language->get('menu_language_sync'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

            <?php if (($user->getGroupId() == 1 || $user->hasPermission('access', 'backup') || $user->hasPermission('access', 'restore')) && !DEMO) : ?>
              <li class="<?php echo current_nav() == 'backup_restore' ? 'active' : null; ?>">
                <a href="backup_restore.php">
                  <svg class="svg-icon"><use href="#icon-backup"></svg>
                  <span>
                    <?php echo $language->get('menu_backup_restore'); ?>
                  </span>
                </a>
              </li>
            <?php endif; ?>

          </ul>
        </li>
      <?php endif; ?>

      <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'activate_store')) : ?>
        <li class="<?php echo current_nav() == 'store_select' ? 'active' : null; ?>">
          <a href="../store_select.php">
            <svg class="svg-icon"><use href="#icon-list"></svg>
            <span>
              <?php echo $language->get('menu_store_change'); ?>
            </span>
          </a>
        </li>
      <?php endif; ?>
      <li id="sidebar-bottom"></li>
    </ul>
    
  </section>
</aside>
<!-- Main Sidebar End -->