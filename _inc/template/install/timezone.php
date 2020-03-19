<br>
<br>
<div class="container">
    <div class="row">
	    <div class="col-sm-8 col-sm-offset-2">
	        <div class="panel panel-default">
		        <div class="panel-heading text-center bg-database">
                    <h2>Timezone Setup</h2>
                    <p>Running step 4 of 6</p>
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
					  	<li>
					  		<a href="database.php">
					  			<span class="fa fa-check"></span> Database
					  		</a>
					  	</li>
					  	<li class="active">
					  		<a href="timezone">Timezone
					  		</a>
					  	</li>
					  	<li>
					  		<a href="">Site Config
					  		</a>
					  	</li>
					  	<li>
					  		<a href="#" onClick="return false">Done!
					  		</a>
					  	</li>
					</ul>
			    </div>
			    <div class="panel-body ins-bg-col">

			    	<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data">
						<?php if($errors['timezone'])  
						    echo "<div class='form-group has-error' >";
						else     
						    echo "<div class='form-group' >";
						?>
							<label for="sname" class="col-sm-3 control-label">
							    <p>Timezone <span class="text-aqua">*</span></p>
							</label>
							<div class="col-sm-8">
							    <select class="form-control" name="timezone" id="timezone">
									<option selected="selected" disabled hidden value="">
										<?php echo $language->get('text_select'); ?>
									</option>
									<?php include('../_inc/helper/timezones.php'); ?>
								</select>
								<p class="control-label">
									<?php echo $errors['timezone']; ?>
								</p>
							</div>
						</div>

						<br>

						<div class="form-group">
				            <div class="col-sm-6 col-sm-offset-3">
				                <input type="submit" class="btn btn-success" value="Next Step &rarr;" >
				            </div>
				        </div>
					</form>
			    </div>
			</div>
		</div>
	</div>
</div>