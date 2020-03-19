<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if (($user->getGroupId() != 1 && !$user->hasPermission('access', 'change_password')) || DEMO) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');;
}

//  Load Language File
$language->load('password');

// USER MODEL 
$user_model = $registry->get('loader')->model('user');

// FETCH ALL USER 
$users = $user_model->getUsers();

if(isset($request->post['form_change_password'])) 
{
    try {

      // cCheck Update Permission
      if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'change_password') || DEMO) {
        throw new Exception($language->get('error_password_permission'));
      }

      // Fetch User
      $the_user = $user_model->getUser($request->post['user_id']);
      if (!isset($the_user['id'])) {
        throw new Exception($language->get('error_password_user_found'));
      }

      if ($user->getGroupId() != 1) {

        // Old Passwod Validation
        if(empty($request->post['old'])) {
            throw new Exception($language->get('error_password_old'));
        }

        // Fetch User
        $old_password = md5($request->post['old']);

        // Check Old Passwrod
        if($old_password != $the_user['password']) {
            throw new Exception($language->get('error_password_old_wrong'));
        }     
      }
      
      // New Password Validation
      if(!validateAlphanumeric($request->post['new1'])) {
          throw new Exception($language->get('error_password_new'));
      }

      // Password  Length Check
      if(strlen($request->post['new1']) < 6) {
        throw new Exception($language->get('error_user_password_length'));
      }
      
      // Confirm Password Validation
      if(!validateAlphanumeric($request->post['new2'])) {
          throw new Exception($language->get('error_password_old'));
      }

      // Matching New and Confirm Password
      if($request->post['new1'] != $request->post['new2']) {
          throw new Exception($language->get('error_password_mismatch'));
      }
        
      $new_final_password = md5($request->post['new1']);
      
      // Updating Password
      $statement = $db->prepare("UPDATE `users` SET `password` = ?, `raw_password` = ? WHERE `id` = ?");
      $statement->execute(array($new_final_password, $request->post['new1'], $the_user['id']));
      $success_message = $language->get('text_success');
    }
    catch(Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Set Document Title
$document->setTitle($language->get('title_password'));

// ADD BODY CLASS
$document->setBodyClass('password-change');

// Include Header and Footer
include("header.php"); 
include("left_sidebar.php");
?>

<!-- Content Wrapper Start -->
<div class="content-wrapper">

  <!-- Content Header Start -->
  <section class="content-header">
    <h1>
      <?php echo $language->get('text_password_title'); ?>
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
        <?php echo $language->get('text_password_title'); ?>
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
      <div class="alert alert-warning mb-0">
          <p><span class="fa fa-fw fa-info-circle"></span> This feature is disabled in demo version</p>
        </div>
    </div>
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

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" data-toggle="modal" data-target="#passChangeModal">
      <div class="panel panel-app password-panel">
        <div class="panel-body">
          <h2>
            <span class="icon">
              <svg class="svg-icon"><use href="#icon-btn-password"></svg>
            </span>
          </h2>
          <div class="small password-style">
            <?php echo $language->get('text_password_box_title'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="passChangeModal" tabindex="-1" role="dialog" aria-labelledby="Login" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              <span class="fa fa-fw fa-lock"></span> <?php echo $language->get('text_password_title'); ?>
            </h4>
          </div>
          <div class="modal-body">   
            <form class="form-horizontal" action="" method="post" enctype="multipart/formdata">
              <div class="box-body">
                <?php if ($user->getGroupId() == 1) : ?>
                  <div class="form-group">
                    <label for="old" class="col-sm-4 control-label">
                      <?php echo $language->get('label_password_user'); ?>
                    </label>
                    <div class="col-sm-8">
                      <select name="user_id" class="form-control">
                        <?php foreach ($users as $the_user) : ?>
                          <option value="<?php echo $the_user['id']; ?>" <?php echo $the_user['id'] == $user->getId() ? 'selected' : null; ?>>
                            <?php echo $the_user['username'] . ' (' . $the_user['email'] . ')'; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="form-group">
                    <div class="col-sm-8">
                        <?php foreach ($users as $the_user) : ?>
                          <?php if ($the_user['id'] == $user->getId()) : ?>
                            <input type="hidden" name="user_id" id="user_id" value="<?php echo $the_user['id']; ?>">
                          <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                  </div>
                <?php endif; ?>
                <?php if ($user->getGroupId() != 1) : ?>
                <div class="form-group">
                  <label for="old" class="col-sm-4 control-label">
                    <?php echo $language->get('label_password_old'); ?>
                  </label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control" id="old" name="old" requied>
                  </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                  <label for="new1" class="col-sm-4 control-label">
                    <?php echo $language->get('label_password_new'); ?>
                  </label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control" id="new1" name="new1" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="new2" class="col-sm-4 control-label">
                    <?php echo $language->get('label_password_confirm'); ?>
                  </label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control" id="new2" name="new2" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 control-label">&nbsp;</label>
                  <div class="col-sm-8">
                    <button type="submit" class="btn btn-block btn-info pull-right" name="form_change_password">
                      <span class="fa fa-fw fa-pencil"></span> 
                      <?php echo $language->get('button_update'); ?>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Content End -->
</div>
<!-- Content Wrapper End -->

<?php include ("footer.php"); ?>