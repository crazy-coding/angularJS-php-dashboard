<?php $language->load('menu'); ?>

<!-- Main Header Start -->  
<header class="main-header">
  <a href="dashboard.php" class="logo">
    <span class="logo-mini">
      <b>
        <?php echo store('name')[0]; ?>
      </b>
      <?php echo mb_substr(store('name'), -1); ?>
    </span>
    <span class="logo-lg">
      <b>
        <?php echo limit_char(store('name'), 20); ?>
      </b>
    </span>
  </a>
  <nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">#</span>
    </a>
    <ul class="nav navbar-nav navbar-left">
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?php echo $user->getAllPreference()['language'];?>">
          <img src="../assets/itsolution24/img/flags/<?php echo $user->getAllPreference()['language'];?>.png" alt="<?php echo $user->getAllPreference()['language'];?>"></a>
        <ul class="dropdown-menu">       
          <?php foreach(get_dir_list(ROOT.'/language') as $langname) : if($user->getAllPreference()['language'] == $langname) continue; ?>
            <li>
              <a href="<?php echo $_SERVER['PHP_SELF'];?>?lang=<?php echo $langname;?>" title="<?php echo $langname;?>">
              <img src="../assets/itsolution24/img/flags/<?php echo $langname;?>.png" class="language-img"> &nbsp;&nbsp;<?php echo $language->get('text_'.$langname); ?>
            </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </li>
      <li>
        <a href="#" onClick="return false;" id="live_datetime"></a>
      </li>
    </ul>
    <!-- navbar custome menu start -->
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <?php if (in_array(current_nav(), array('dashboard','invoice','product_details','report_collection','report_sell_itemwise','report_sell_categorywise','report_sell_supplierwise','report_buy_itemwise','report_buy_categorywise','report_buy_supplierwise','report_customer_due_collection','report_payment','expense','supplier_profile','customer_profile','report_overview','analysis','bank_transactions','sms_report', 'loan', 'loan_summary', 'purchase','report_sell_tax', 'report_sell_payment','report_buy_payment','report_buy_tax','report_tax_overview','giftcard','giftcard_topup','buy_transaction','sell_transaction','transfer'))) : ?>
          <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'filtering')) : ?>
            <li class="user user-menu">
              <a id="show-filter-box" href="#">
                <svg class="svg-icon"><use href="#icon-search-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
              </a>
            </li>
          <?php endif; ?>
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'create_invoice')) : ?>
          <li class="user user-menu<?php echo current_nav() == 'pos' ? ' active' : null; ?> sell-btn">
            <a href="pos.php" title="<?php echo $language->get('text_pos'); ?>"> 
              <svg class="svg-icon"><use href="#icon-pos-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
              <span class="text">
                <?php echo $language->get('menu_sell'); ?>
              </span>
            </a>
          </li>
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_keyboard_shortcut')) : ?>
          <?php if (current_nav() == 'pos') : ?>
            <li>
              <a id="keyboard-shortcut" ng-click="keyboardShortcutModal()" onClick="return false;" href="#" title="<?php echo $language->get('text_keyboard_shortcut'); ?>">
                <svg class="svg-icon"><use href="#icon-keyboard-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
              </a>
            </li> 
          <?php endif; ?>
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_summary_report')) : ?>
          <li>
            <a ng-click="SummaryReportModal()" onClick="return false;" href="#" title="<?php echo $language->get('text_summary_report'); ?>">
              <svg class="svg-icon"><use href="#icon-eye-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
                <span class="text">
                  <?php echo $language->get('menu_summary'); ?>
                </span>
            </a>
          </li> 
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_invoice_list')) : ?>
          <li class="user user-menu<?php echo current_nav() == 'invoice' ? ' active' : null; ?>">
            <a href="invoice.php" title="<?php echo $language->get('text_invoice'); ?>">
              <svg class="svg-icon"><use href="#icon-invoice-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
              <span class="text">
                <?php echo $language->get('menu_invoice'); ?>
              </span>
            </a>
          </li>
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_user_preference')) : ?>
          <li class="user user-menu<?php echo current_nav() == 'user_preference' ? ' active' : null; ?> sell-btn">
            <a href="user_preference.php?store_id=<?php echo store_id(); ?>" title="<?php echo $language->get('text_user_preference'); ?>">
              <svg class="svg-icon"><use href="#icon-heart-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
            </a>
          </li>
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_store')) : ?>
          <li class="user user-menu<?php echo current_nav() == 'store_single' ? ' active' : null; ?> sell-btn">
            <a href="store_single.php" title="<?php echo $language->get('text_settings'); ?>">
              <svg class="svg-icon"><use href="#icon-settings-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
            </a>
          </li>
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_stock_alert')) : ?>
          <li class="user user-menu<?php echo current_nav() == 'stock_alert' ? ' active' : null; ?>">
            <a href="stock_alert.php" title="<?php echo $language->get('text_stock_alert'); ?>">
              <svg class="svg-icon"><use href="#icon-alert-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
              <?php if (total_out_of_stock() > 0) : ?>
                <span class="label label-warning">
                  <?php echo total_out_of_stock(); ?></span>
              <?php endif; ?>
            </a>
          </li>
        <?php endif; ?>
        <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_expired_product')) : ?>
          <li class="user user-menu<?php echo current_nav() == 'expired' ? ' active' : null; ?>">
            <a href="expired.php" title="<?php echo $language->get('text_expired'); ?>">
              <svg class="svg-icon"><use href="#icon-expired-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
              <?php if (total_expired() > 0) : ?>
                <span class="label label-warning">
                  <?php echo total_expired(); ?>
                </span>
              <?php endif; ?>
            </a>
          </li> 
        <?php endif; ?>
        <li class="user user-menu">
          <a ng-click="SupportDeskModal();" onClick="return false;" href="#" title="<?php echo $language->get('text_itsolution24'); ?>">
            <svg class="svg-icon"><use href="#icon-support-<?php echo $user->getPreference('base_color', 'black'); ?>"></svg>
          </a>
        </li>
        <li>
          <a id="togglingfullscreen" onClick="toggleFullScreenMode(); return false;" href="#" title="<?php echo $language->get('text_fullscreen'); ?>">
            <span class="fa fa-fw fa-expand"></span>
          </a>
        </li>
        <li class="user user-menu">
          <a href="logout.php" title="<?php echo $language->get('text_logout'); ?>">
            <i class="fa fa-sign-out"></i>
          </a>
        </li> 
      </ul>
    </div>
  </nav>
</header>
<!-- Main Header End --> 