<?php 
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
include("_init.php");

// Load Language File
$language->load('login');

// Check, If User Login or Not
// If User Already Logged In Then
// Redirect to Dashbaord
if ($user->isLogged()) {
  redirect('admin/dashboard.php');
}

if ($request->server['REQUEST_METHOD'] == 'POST' && $request->get['action_type'] == "LOGIN")
{
  try {

    // Validate Username
    if (!isset($request->post['username']) || !isset($request->post['username'])) {
        throw new Exception($language->get('error_username_or_password'));
    }
    if (!validateString($request->post['username'])) {
        throw new Exception($language->get('error_username'));
    }

    // Validate Password
    if (empty($request->post['password'])) {
        throw new Exception($language->get('error_password'));
    }

    $username = $request->post['username']; 
    $password = $request->post['password']; 

    // Attempt to Log In
    if ($user->login($username, $password)) {

      if (DEMO) {
        $log_path = DIR_STORAGE . 'logs/v.txt';
        write_file($log_path, get_real_ip() . ' | ', 'a');
      }

      header('Content-Type: application/json; charset=UTF-8');
      echo json_encode(array('msg' => $language->get('login_success'), 'sessionUserId' => $session->data['id'], 'count_user_store' => count_user_store(), 'store_id' => $user->getSingleStoreId()));
      exit();
    }

    throw new Exception($language->get('error_invalid_username_password'));

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// Sending Password Resetting Code Via Email
if ($request->server['REQUEST_METHOD'] == 'POST' && $request->get['action_type'] == "SEND_PASSWORD_RESET_CODE")
{
  try {

    if(DEMO) {
      throw new Exception($language->get('error_disable_in_demo'));
    }

    // Validate Email Address
    if (!validateEmail($request->post['email'])) {
        throw new Exception($language->get('error_email'));
    }

    $email = $request->post['email']; 

    // Check, If Email Address Exist In Database or Not
    $statement = $db->prepare("SELECT * FROM `users` LEFT JOIN `user_to_store` as `u2s` ON (`users`.`id` = `u2s`.`user_id`) WHERE `email` = ? AND `u2s`.`status` = ?");
    $statement->execute(array($email, 1));
    $the_user = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$the_user) {
      throw new Exception($language->get('error_user_not_found'));
    }

    // Check, If SMTP Server Is Enabled or Not
    $driver = get_preference('email_driver');
    if ($driver != 'smtp_server') {
      throw new Exception($language->get('error_smtp_server'));
    }

    $subject        = $language->get('text_password_reset');
    $recipient_name = $the_user['username'];
    $from_name      = get_preference('email_from');
    $from_address   = get_preference('email_address');
    $smtp_host      = get_preference('smtp_host');
    $smtp_username  = get_preference('smtp_username');
    $smtp_password  = get_preference('smtp_password');
    $smtp_port      = get_preference('smtp_port');
    $ssl_tls        = get_preference('ssl_tls');

    // Email Start

    require_once('_inc/vendor/PHPMailer/PHPMailerAutoload.php');
    $mail = new PHPMailer;
    $mail->IsSMTP();    // Telling The Class To Use SMTP
    $mail->SMTPAuth = true;     // Enable SMTP Authentication
    $mail->SMTPSecure = $ssl_tls;     // Sets The Prefix To The Server
    $mail->Host = $smtp_host;     // Sets GMAIL as the SMTP server
    $mail->Port = $smtp_port;     // Set The SMTP Port For The GMAIL Server
    $mail->Username = $smtp_username;     // GMAIL Username
    $mail->Password = $smtp_password;     // GMAIL Password
    $mail->AddAddress($email, $recipient_name);
    $mail->SetFrom($smtp_username, $from_name);
    $mail->Subject = $subject;
    $mail->IsHTML(true);
    $template_name = 'password-reset';
    if (!file_exists(DIR_EMAIL_TEMPLATE . $template_name . '.php') || !is_file(DIR_EMAIL_TEMPLATE . $template_name . '.php')) {
        throw new Exception($language->get('error_email_template_not_found'));
    }

    //Generate Unique String
    $uniqid_str = md5(uniqid(mt_rand()));
    $reset_pass_link = root_url() . '/password_reset.php?fp_code=' . $uniqid_str;

    ob_start();
    require('_inc/template/email/' . $template_name . '.php');
    $body = ob_get_contents();
    ob_end_clean();
    $mail->Body = $body;

    if (!$mail->Send()) {
        throw new Exception($language->get('error_email_not_sent'));
    }

    // Email End

    // Update Users Password Reset Code
    $statement = $db->prepare("UPDATE `users` SET `pass_reset_code` = ?, `reset_code_time` = ? WHERE `id` = ?");
    $statement->execute(array($uniqid_str, date('Y-m-d H:i:s'), $the_user['id']));

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('msg' => $language->get('success_reset_code_sent')));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} ?>
<!DOCTYPE html>
<html lang="<?php echo $document->langTag($active_lang);?>">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Log in<?php echo store('name') ? ' | ' . store('name') : null; ?></title>
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

  <!-- JS -->
  <script type="text/javascript">
    var isDemo = false;
  <?php if(DEMO) : ?>
    var isDemo = true;
  <?php endif;?>
  </script>

  <script type="text/javascript">
    var baseUrl = "<?php echo root_url(); ?>";
    var adminDir = "<?php echo ADMINDIRNAME; ?>";
    var refUrl = "<?php echo isset($request->get['redirect_to']) ? $request->get['redirect_to'] : ''?>";
  </script>

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
        <p><strong><?php echo store('name'); ?></strong></p>
      </div>
    </div>
    <?php
    if (isset($error_message)) : ?>
      <div class="alert alert-danger">
          <p><span class="fa fa-fw fa-warning"></span> <?php echo $error_message ; ?></p>
      </div>
      <br>
    <?php endif; ?>
    <div class="login-box-body" ng-controller="LoginController">
      <p class="login-box-msg">
        <strong><?php echo $language->get('text_login'); ?></strong>
      </p>
      <form id="login-form" action="login.php" method="post">       

        <div class="form-group">
          <div class="input-group">
            <div class="input-group-addon input-sm">
              <svg class="svg-icon"><use href="#icon-avatar"></svg>
            </div>
            <input type="text" class="form-control" placeholder="Email / Phone No." name="username">
          </div>
        </div>

        <div class="form-group">
          <div class="input-group">
            <div class="input-group-addon input-sm">
              <svg class="svg-icon"><use href="#icon-password"></svg>
            </div>
            <input type="password" class="form-control" placeholder="Password" name="password">
          </div>
        </div>

        <button type="submit" id="login-btn" class="btn btn-success btn-block btn-flat" data-loading-text="Logging...">
          <i class="fa fa-fw fa-sign-in"></i> 
          <?php echo $language->get('button_sign_in'); ?>
        </button>
      </form>
      <?php if(DEMO) : ?>
      <div id="credentials">
        <table class="table table-bordered table-striped">
          <tbody>
            <?php foreach (get_users() as $the_user) : ?>
              <?php if (in_array($the_user['email'], array('admin@itsolution24.com', 'cashier@itsolution24.com', 'salesman@itsolution24.com'))) : ?>
                <tr title="Login as Admin">
                  <td class="username" data-username="<?php echo $the_user['email'];?>"><?php echo $the_user['email'];?></td>
                  <td class="password text-center" data-password="<?php echo $the_user['raw_password'];?>"><?php echo $the_user['raw_password'];?></td>
                </tr>
              <?php endif;?>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
      <?php endif;?>

      <?php if (!DEMO) : ?>
        <div>
          <br>
          <p class="text-center">
            <a href="#forgotPasswordModal" data-toggle="modal" data-target="#forgotPasswordModal" title="<?php echo $language->get('text_forgot_password'); ?>" class="text-danger">
              <?php echo $language->get('text_forgot_password'); ?>
            </a>
          </p>
        </div>
      <?php endif; ?>
    </div>
    <div class="copyright text-center">
      <p>&copy; <a href="#">Sunny demo</a>, v<?php echo settings('version'); ?></p>
    </div>
  </section>

  <?php if (!DEMO) : ?>
    <!--Forgot Password Modal Start -->
    <div id="forgotPasswordModal" class="modal fade" aria-hidden="false" aria-labelledby="forgotPasswordModal" role="dialog" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="#" method="post" accept-charset="utf-8">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <h4 class="modal-title"><?php echo $language->get('title_forgot_password'); ?></h4>
            </div>
            <div class="modal-body">
              <label for="email"><?php echo $language->get('text_forgot_password_instruction'); ?></label>
              <input id="email" type="email" name="email" placeholder="Email" autocomplete="off" class="form-control">
            </div>
            <div class="modal-footer">
              <button data-dismiss="modal" class="btn btn-warning pull-left" type="button"><?php echo $language->get('button_close'); ?></button>
              <button id="reset-btn" name="reset-btn" class="btn btn-success" type="submit" data-loading-text="Email Sending...">
                <?php echo $language->get('button_submit'); ?>
              </button>
            </div>
          </form> 
        </div>
      </div>
    </div>
    <!-- Forgot Password Modal End -->
  <?php endif; ?>

<noscript>You need to have javascript enabled in order to use <strong><?php echo store('name');?></strong>.</noscript>
</body>
</html>