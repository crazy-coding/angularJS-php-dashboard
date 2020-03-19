<?php 
ob_start();
session_start();
include ("_init.php");

// Load Language File
$language->load('login');

// Redirect, If User Not Logged In
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}?>
<!DOCTYPE html>
<html lang="<?php echo $document->langTag($active_lang);?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Select Store<?php echo store('name') ? ' | ' . store('name') : null; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!--Set Favicon-->
  <?php if ($store->get('favicon')): ?>
      <link rel="shortcut icon" href="assets/itsolution24/img/logo-favicons/<?php echo $store->get('favicon'); ?>">
  <?php else: ?>
      <link rel="shortcut icon" href="assets/itsolution24/img/logo-favicons/nofavicon.png">
  <?php endif; ?>

  <!-- All CSS -->

  <?php if (DEMO || USECOMPILEDASSET) : ?>

    <!-- Login Combined CSS -->
    <link type="text/css" href="assets/itsolution24/cssmin/login.css" rel="stylesheet">

  <?php else : ?>

    <!-- Bootstrap CSS -->
    <link type="text/css" href="assets/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link type="text/css" href="assets/toastr/toastr.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link type="text/css" href="assets/itsolution24/css/theme.css" rel="stylesheet">

    <!-- Login CSS -->
    <link type="text/css" href="assets/itsolution24/css/login.css" rel="stylesheet">

  <?php endif; ?>

  <!-- All JS -->

  <script type="text/javascript">
    var baseUrl = "<?php echo root_url(); ?>";
    var adminDir = "<?php echo ADMINDIRNAME; ?>";
    var refUrl = "<?php echo isset($session->data['ref_url']) ? $session->data['ref_url'] : ''?>";
  </script>

  <?php if (DEMO || USECOMPILEDASSET) : ?>

    <!-- Login Combined JS -->
    <script src="assets/itsolution24/jsmin/login.js"></script>

  <?php else : ?>

    <!-- jQuery JS  -->
    <script src="assets/jquery/jquery.min.js" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    <!-- Toastr JS -->
    <script src="assets/toastr/toastr.min.js" type="text/javascript"></script>

    <!-- Common JS -->
    <script src="assets/itsolution24/js/common.js"></script>

    <!-- Login JS -->
    <script src="assets/itsolution24/js/login.js"></script>

  <?php endif; ?>

</head>
<body class="login-page">
<div class="hidden"><?php include('assets/itsolution24/img/iconmin/membership/membership.svg');?></div>

  <section class="login-box">
    <div class="login-logo">
      <div class="text">
        <p>
          <strong>
            <?php echo $language->get('text_select_store'); ?>
          </strong>
        </p>
      </div>
    </div>
    <?php if (isset($error_message)) { ?>
      <div class="alert alert-danger">
          <p class=""><span class="fa fa-fw fa-warning"></span> <?php echo $error_message ; ?></p>
      </div>
      <br>
    <?php } ?>
    <div class="login-box-body" ng-controller="StoreController">
      <ul class="list-unstyled list-group store-list">
        <?php foreach (get_stores() as $the_store): ?>
          <li class="list-group-item">
            <a class="activate-store" href="<?php echo root_url();?>/<?php echo ADMINDIRNAME;?>/store.php?active_store_id=<?php echo $the_store['store_id']; ?>">
              <div class="store-icon">
                <svg class="svg-icon"><use href="#icon-store"></svg>
              </div>
              <div class="store-name">
                <?php echo $the_store['name']; ?>
                <span class="pull-right">&rarr;</span>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="copyright text-center">
      <p>&copy; <a href="http://itsolution24.com">ITsolution24.com</a>, v<?php echo settings('version'); ?></p>
    </div>
  </section>

<noscript>You need to have javascript enabled in order to use <strong><?php echo store('name');?></strong>.</noscript>
</body>
</html>