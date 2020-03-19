<?php 
ob_start();
session_start();
include ("../_init.php");

// Redirect, If user is not logged in
if (!$user->isLogged()) {
  redirect(root_url() . '/index.php?redirect_to=' . url());
}

// Redirect, If User has not Read Permission
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_user_preference')) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

//  Load Language File
$language->load('system');

if ($request->server['REQUEST_METHOD'] == 'POST')
{
  try {

  	// Check Permission
  	if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_user_preference')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Language Validation
    if (empty($request->post['preference']['language'])) {
      throw new Exception($language->get('error_preference_language'));
    }

    // Base Color Validation
    if (empty($request->post['preference']['base_color'])) {
      throw new Exception($language->get('error_preference_base_color'));
    }

    // Base Color Validation
    if (empty($request->post['preference']['base_color'])) {
      throw new Exception($language->get('error_preference_base_color'));
    }

    // POS Side Panel Position
    if (empty($request->post['preference']['pos_side_panel'])) {
      throw new Exception($language->get('error_preference_pos_side_panel'));
    }

    // POS Pattern Validation
    if (empty($request->post['preference']['pos_pattern'])) {
      throw new Exception($language->get('error_pos_backgorund_pattern'));
    }

    $statement = $db->prepare("UPDATE `users` SET `preference` = ? WHERE `id` = ? ");
    $statement->execute(array(serialize($request->post['preference']), $user->getId()));

    // SET OUTPUT CONTENT TYPE
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success')));
    exit;

  } catch(Exception $e) { 

    // SET OUTPUT CONTENT TYPE
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit;
  }
}
// FETCH USER PREFERENCE
$preference = $user->getAllPreference();

// Set Document Title
$document->setTitle($language->get('title_user_preference'));

// Add Script
$document->addScript('../assets/itsolution24/angular/controllers/SystemController.js');

// Include Header and Footer
include ("header.php");
include ("left_sidebar.php");
?>

<script type="text/javascript">
	$(document).ready(function() {
		$(".pos-pattern li").on("click", function() {
			var $this = $(this);
			var patternName = $this.data('name');

			$this.parent().find("li").removeClass("selected");

			$this.addClass("selected");

			$("#pos_pattern_input").val(patternName);
		});
	});
</script>

<!-- Content Wrapper Start -->
<div class="content-wrapper" ng-controller="SystemController">

	<!-- Content Header Start -->
	<section class="content-header">  
		<h1>
		    <?php echo $language->get('text_user_preference_title'); ?>
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
		    	<?php echo $language->get('text_user_preference_title'); ?>
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
	      </div>
	    </div>
	    <?php endif; ?>
	    
		<form id="user-preference-form" class="form-horizontal" action="user_preference.php" method="post">
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

		    <div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">
						<?php echo $language->get('text_language_preference_title'); ?>
					</h3>
				</div>
				<div class="box-body">
			        <div class="box-body">
						<div class="form-group">
							<label for="language" class="col-sm-3 control-label">
								<?php echo $language->get('label_select_language'); ?>
							</label>
							<div class="col-sm-6">
								<select class="form-control" name="preference[language]" id="language">
									<?php foreach(get_dir_list(ROOT.'/language') as $langname) : ?>
									  	<option value="<?php echo $langname;?>"<?php echo isset($preference['language']) && $preference['language'] == $langname  ? ' selected' : null; ?> name="preference[language]">
									  		<?php echo $language->get('text_'.$langname); ?>
									  	</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
			        </div>
				</div>
		    </div>

		    <div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">
						<?php echo $language->get('text_color_preference_title'); ?>
					</h3>
				</div>
				<div class="box-body">
			        <div class="box-body">
						<div class="form-group">
							<label for="base_color" class="col-sm-3 control-label">
								<?php echo $language->get('label_base_color'); ?>
							</label>
							<div class="col-sm-6">
								<select class="form-control" name="preference[base_color]" id="base_color">
								  	<option value="black"<?php echo isset($preference['base_color']) && $preference['base_color'] == 'black'  ? ' selected' : null; ?> name="preference[base_color]">
								  		<?php echo $language->get('text_color_black'); ?>
								  	</option>
								  	<option value="blue"<?php echo isset($preference['base_color']) && $preference['base_color'] == 'blue'  ? ' selected' : null; ?> name="preference[base_color]">
								  		<?php echo $language->get('text_color_blue'); ?>
								  	</option>
								  	<option value="green"<?php echo isset($preference['base_color']) && $preference['base_color'] == 'green'  ? ' selected' : null; ?> name="preference[base_color]">
								  		<?php echo $language->get('text_color_green'); ?>
								  	</option>

								  	<option value="red"<?php echo isset($preference['base_color']) && $preference['base_color'] == 'red'  ? ' selected' : null; ?> name="preference[base_color]">
								  		<?php echo $language->get('text_color_red'); ?>
								  	</option>
								  	<option value="yellow"<?php echo isset($preference['base_color']) && $preference['base_color'] == 'yellow'  ? ' selected' : null; ?> name="preference[base_color]">
								  		<?php echo $language->get('text_color_yellow'); ?>
								  	</option>
								</select>
							</div>
						</div>
			        </div>
				</div>
		    </div>

		    <div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">
						<?php echo $language->get('text_pos_side_panel_position_title'); ?>
					</h3>
				</div>
				<div class="box-body">
			        <div class="box-body">
						<div class="form-group">
							<label for="pos_side_panel" class="col-sm-3 control-label">
								<?php echo $language->get('label_pos_side_panel_position'); ?>
							</label>
							<div class="col-sm-6">
								<select class="form-control" name="preference[pos_side_panel]" id="pos_side_panel">
								  	<option value="right"<?php echo isset($preference['pos_side_panel_right']) && $preference['pos_side_panel'] == 'right'  ? ' selected' : null; ?> name="preference[pos_side_panel_right]">
								  		<?php echo $language->get('text_right'); ?>
								  	</option>
								  	<option value="left"<?php echo isset($preference['pos_side_panel']) && $preference['pos_side_panel'] == 'left'  ? ' selected' : null; ?> name="preference[pos_side_panel_left]">
								  		<?php echo $language->get('text_left'); ?>
								  	</option>
								</select>
							</div>
						</div>
			        </div>
				</div>
		    </div>

		    <div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">
						<?php echo $language->get('text_pos_pattern_title'); ?>
					</h3>
				</div>
				<div class="box-body">
			        <div class="box-body">
						<div class="form-group">
							<label for="language" class="col-sm-3 control-label">
								<?php echo $language->get('label_select_pos_pattern'); ?>
							</label>
							<div class="col-sm-7">
								<?php 
								$patterns = get_filenames(DIR_ASSET.'itsolution24/img/pos/patterns'); 
								$total_pattern = count($patterns); ?>

								<input type="hidden" name="preference[pos_pattern]" id="pos_pattern_input" value="<?php echo isset($preference['pos_pattern']) ? $preference['pos_pattern'] : null; ?>">

								<ul class="list-unstyled pos-pattern">
								<?php for ($i=0; $i < $total_pattern; $i++) : ?>
									<li class="<?php echo isset($preference['pos_pattern']) && $preference['pos_pattern'] == $patterns[$i] ? 'selected' : ''; ?>" data-name="<?php echo $patterns[$i]; ?>" title="<?php echo str_replace(array('.jpg','.jpeg', '.png','.gif','.svg'), '', $patterns[$i]); ?>">
										<img src="../assets/itsolution24/img/pos/patterns/<?php echo $patterns[$i]; ?>">
									</li>
								<?php endfor; ?>
								</ul>
							</div>
						</div>
			        </div>
				</div>
				<div class="box-footer">
					<?php if ($user->getGroupId() == 1 || $user->hasPermission('access', 'update_user_preference')) : ?>
			            <div class="col-sm-3 col-sm-offset-3">
				            <button class="btn btn-info btn-block save" type="button" data-form="#user-preference-form" data-loading-text="Saving...">
				              	<span class="fa fa-fw fa-pencil"></span> 
				              	<?php echo $language->get('button_update'); ?>
				            </button>
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