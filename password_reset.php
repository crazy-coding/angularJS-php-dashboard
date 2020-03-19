<?php include("_init.php"); 

/*
| -----------------------------------------------------
| PRODUCT NAME: 	Modern POS - Point of Sale with Stock Management System
| -----------------------------------------------------
| AUTHOR:     ITSOLUTION24.COM
| -----------------------------------------------------
| EMAIL:      info@itsolution24.com
| -----------------------------------------------------
| COPYRIGHT:    RESERVED BY ITSOLUTION24.COM
| -----------------------------------------------------
| WEBSITE:      http://itsolution24.com
| -----------------------------------------------------
*/

//Load Language File
$language->load('login');

// Post Request: Reset Password by Password Reset Code
if ($request->server['REQUEST_METHOD'] == 'POST' && $request->get['action_type'] == "RESET")
{
  try {

    if (!isset($request->post['fp_code'])) {
      throw new Exception($language->get('error_password_reset_code'));
    }

    $reset_code =  $request->post['fp_code'];

    // Validate Password Reset Code
    $time = time()-(24*60)*60;
    $statement = $db->prepare("SELECT * FROM `users` WHERE `pass_reset_code` = ? AND `reset_code_time` > NOW() - $time");
    $statement->execute(array($reset_code));
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
      throw new Exception($language->get('error_invalid_reset_code'));
    }

    // Validate Password
    if (strlen($request->post['password']) < 6 || !validateAlphanumeric($request->post['password'])) {
        throw new Exception($language->get('error_password'));
    }

    // Validate Confirm Password
    if (empty($request->post['password_confirm'])) {
        throw new Exception($language->get('error_password_confirm'));
    }

    $password = $request->post['password']; 
    $password_confirm = $request->post['password_confirm']; 

    // Match Password and Confirm Password
    if ($password !== $password_confirm) {
      throw new Exception($language->get('error_password_not_match'));
    }

    // Up-Date Password
    $statement = $db->prepare("UPDATE `users` SET `password` = ?, `pass_reset_code` = ? WHERE `id` = ?");
    $statement->execute(array(md5($password), '', $user['id']));

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('msg' => $language->get('password_reset_success')));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Validate Password Reset Code
$reset_code =  $request->get['fp_code'];
if (!$reset_code) {
  redirect('index.php');
}

// Check, If Password Reset Code Exist or Not
$time = time()-(24*60)*60;
$statement = $db->prepare("SELECT * FROM `users` WHERE `pass_reset_code` = ? AND `reset_code_time` > NOW() - $time");
$statement->execute(array($reset_code));
$user = $statement->fetch(PDO::FETCH_ASSOC);
if (!$user) {
  redirect('index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Password Reset | <?php echo store('name'); ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!--Set Favicon-->
  <?php if ($store->get('favicon')): ?>
      <link rel="shortcut icon" href="assets/itsolution24/img/logo-favicons/<?php echo $store->get('favicon'); ?>">
  <?php else: ?>
      <link rel="shortcut icon" href="assets/itsolution24/img/logo-favicons/nofavicon.png">
  <?php endif; ?>

  <!-- All CSS -->

  <?php if (DEMO || USECOMPILEDASSET) : ?>

    <!-- LOGIN COMBINED CSS -->
    <link type="text/css" href="assets/itsolution24/cssmin/login.css" rel="stylesheet">

  <?php else : ?>

    <!-- Bootstrap CSS -->
    <link type="text/css" href="assets/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Toastr CSS-->
    <link type="text/css" href="assets/toastr/toastr.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link type="text/css" href="assets/itsolution24/css/theme.css" rel="stylesheet">

    <!-- Login CSS -->
    <link type="text/css" href="assets/itsolution24/css/login.css" rel="stylesheet">

  <?php endif; ?>

  <?php if (DEMO || USECOMPILEDASSET) : ?>

    <!-- Login Combined JS -->
    <script src="assets/itsolution24/jsmin/login.js"></script>

  <?php else : ?>

    <!-- jQuery JS -->
    <script src="assets/jquery/jquery.min.js" type="text/javascript"></script>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    <!-- Toastr JS -->
    <script src="assets/toastr/toastr.min.js" type="text/javascript"></script>

    <!-- Forgot Password JS-->
    <script src="assets/itsolution24/js/forgot-password.js"></script>

    <!-- Login JS -->
    <script src="assets/itsolution24/js/login.js"></script>

  <?php endif; ?>
</head>
<body class="login-page">
<div class="hidden"><?php include('assets/itsolution24/img/iconmin/membership/membership.svg');?></div>

  <section class="login-box">
    <div class="login-logo">
      <div class="text">
        <p><strong><?php echo store('name'); ?></strong></p>
      </div>
    </div>
    <?php
      if (isset($error_message)) { ?>
        <div class="alert alert-danger">
            <p class=""><span class="fa fa-fw fa-warning"></span> <?php echo $error_message ; ?></p>
        </div>
        <br>
    <?php } ?>
    <div class="login-box-body">
      <p class="login-box-msg"><strong><?php echo $language->get('text_password_reset'); ?></strong></p>
      <form id="reset-form" action="passowrd_reset.php" method="post">
        <input type="hidden" name="fp_code" value="<?php echo isset($request->get['fp_code']) ? $request->get['fp_code'] : null; ?>">

        <div class="form-group">
          <div class="input-group">
            <div class="input-group-addon input-sm">
              <svg class="svg-icon"><use href="#icon-password"></svg>
            </div>
            <input type="password" class="form-control" placeholder="<?php echo $language->get('label_new_password'); ?>" name="password">
          </div>
        </div>

        <div class="form-group">
          <div class="input-group">
            <div class="input-group-addon input-sm">
              <svg class="svg-icon"><use href="#icon-password"></svg>
            </div>
            <input type="password" class="form-control" placeholder="<?php echo $language->get('label_confirm_new_password'); ?>" name="password_confirm">
          </div>
        </div>

        <button type="submit" id="reset-confirm-btn" class="btn btn-success btn-block btn-flat" data-loading-text="Wait Resetting..."><i class="fa fa-fw fa-sign-in"></i> <?php echo $language->get('button_password_reset'); ?></button>
      </form>
    </div>
    <div class="copyright text-center">
      <p>&copy; <a href="http://itsolution24.com">ITsolution24.com</a>, v<?php echo settings('version'); ?></p>
    </div>
  </section>

<noscript>You need to have javascript enabled in order to use <strong><?php echo store('name');?></strong>.</noscript>
</body>
</html>