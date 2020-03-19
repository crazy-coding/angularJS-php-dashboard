<div id="action-button" class="row">
  <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_pos')) : ?>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2" id="button_pos">
      <div class="panel panel-app">
        <div class="panel-body">
          <a class="panel-app-link" href="pos.php">
            <h2>
              <span class="icon">
                <svg class="svg-icon"><use href="#icon-btn-pos"></svg>
              </span>
            </h2>
            <div class="small small2">
              <?php echo $language->get('button_pos'); ?>
            </div>
          </a>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_invoice')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2" id="button_invoice">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="invoice.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-invoice"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_invoice'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_overview_report')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2" id="button_overview_report">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="report_overview.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-overview-report"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_overview_report'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_sell_report')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2" id="button_sell">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="report_sell_itemwise.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-sell-report"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_sell_report'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_buy_report')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2" id="button_buy_report">
          <div class="panel panel-app">
            <div class="panel-body">
              <a class="panel-app-link" href="report_buy_itemwise.php">
                <h2>
                  <span class="icon">
                    <svg class="svg-icon"><use href="#icon-btn-buy-report"></svg>
                  </span>
                </h2>
                <div class="small small2">
                  <?php echo $language->get('button_buy_report'); ?>
                </div>
              </a>
            </div>
          </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_stock_alert')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2" id="button_stock_alert">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="stock_alert.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-stock-alert"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_stock_alert'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_expired_product')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2 tour-item" id="button_expired_product">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="expired.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-expired"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_expired_alert'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_product_import')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2 tour-item" id="button_import">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="import_product.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-import"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_import'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_backup_restore')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2 tour-item" id="button_backup_restore">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="backup_restore.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-backup-restore"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_backup_restore'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'read_store')) : ?>
      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2 tour-item" id="button_settings">
        <div class="panel panel-app">
          <div class="panel-body">
            <a class="panel-app-link" href="store.php">
              <h2>
                <span class="icon">
                  <svg class="svg-icon"><use href="#icon-btn-stores"></svg>
                </span>
              </h2>
              <div class="small small2">
                <?php echo $language->get('button_stores'); ?>
              </div>
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>
</div>