<br>
<br>
<div class="container">
    <div class="row">
	    <div class="col-sm-8 col-sm-offset-2">
	        <div class="panel panel-default">
		        <div class="panel-heading text-center bg-database">
                    <h2>Database Configuration</h2>
                    <p>Running step 3 of 6</p>
                </div>
	        </div>
	    </div>
    </div>
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">    
		    <div class="panel panel-default">
		        <div class="panel-heading bg-white">
					<ul class="nav nav-pills">
					  	<li>
					  		<a href="index.php">
					  			<span class="fa fa-check"></span> Checklist
					  		</a>
					  	</li>
					  	<li class="active">
					  		<a href="database.php">Database
					  		</a>
					  	</li>
					  	<li>
					  		<a href="#" onClick="return false">Timezone
					  		</a>
					  	</li>
					  	<li>
					  		<a href="#" onClick="return false">Site Config</a>
					  	</li>
					  	<li>
					  		<a href="#" onClick="return false">Done!</a>
					  	</li>
					</ul>
			    </div>
			    <div class="panel-body ins-bg-col">

			    	<?php if($errors['database_import']) : ?>
				    	<div class="alert alert-danger">
				    		<p><?php echo $errors['database_import']; ?></p>
				    	</div>
				    <?php endif; ?>
			    	
			    	<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
						<?php 
						if($errors['host']) 
						    echo "<div class='form-group has-error' >";
						else     
						    echo "<div class='form-group' >";
						?>
							<label for="host" class="col-sm-3 control-label">
							    <p>Hostname <span class="text-aqua">*</span></p>
							</label>
							<div class="col-sm-8">
							    <input type="text" class="form-control" id="host" name="host" value="<?php echo isset($request->post['host']) ? $request->post['host'] : 'localhost'; ?>" >

							    <p class="control-label">
							    	<?php echo $errors['host']; ?>
							    </p>
							</div>
						</div>

						<?php 
						if($errors['database']) {
						    echo "<div class='form-group has-error' >";
						}
						else {
						    echo "<div class='form-group' >";
						}
						?>
							<label for="database" class="col-sm-3 control-label">
							    <p>Database <span class="text-aqua">*</span></p>
							</label>
							<div class="col-sm-8">
							    <input type="text" class="form-control" id="database" name="database" value="<?php echo isset($request->post['database']) ? $request->post['database'] : null; ?>" >

							    <p class="control-label">
							    	<?php echo $errors['database']; ?>
							    </p>
							</div>
						</div>

						<?php 
						if($errors['user']) 
						    echo "<div class='form-group has-error' >";
						else     
						    echo "<div class='form-group' >";
						?>
							<label for="user" class="col-sm-3 control-label">
							    <p>Username <span class="text-aqua">*</span></p>
							</label>
							<div class="col-sm-8">
							    <input type="text" class="form-control" id="user" name="user" value="<?php echo isset($request->post['user']) ? $request->post['user'] : 'root'; ?>" >

							    <p class="control-label">
							    	<?php echo $errors['user']; ?>
							    </p>
							</div>
						</div>

						<?php 
						if($errors['password']) 
						    echo "<div class='form-group has-error' >";
						else     
						    echo "<div class='form-group' >";
						?>
							<label for="password" class="col-sm-3 control-label">
							    <p>Password <span class="text-aqua">*</span></p>
							</label>
							<div class="col-sm-8">
							    <input type="password" class="form-control" id="password" name="password" value="<?php echo isset($request->post['password']) ? $request->post['password'] : null; ?>" >

							    <p class="control-label">
							    	<?php echo $errors['password']; ?>
							    </p>
							</div>
						</div>

						<div class="alert alert-info">
							<p>*** This action may take several minutes. Please keep patience while processing this action and never close the browser. Otherwise system will not work properly. Enjoy a cup of coffee while you are waiting... :)</p>
						</div>

				        <div class="form-group">
							<div class="col-sm-6 text-right">
				                <a href="timezone.php" class="btn btn-default">&larr; Previous Step</a>
				            </div>
				            <div class="col-sm-6 text-left">
				                <input type="submit" class="btn btn-success" value="Next Step &rarr;" >
				            </div>
				        </div>
					</form>
			    </div>
			</div>
		</div>
	</div>
</div>