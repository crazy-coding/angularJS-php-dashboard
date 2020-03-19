<br>
<br>
<div class="container">
    <div class="row">
	    <div class="col-sm-8 col-sm-offset-2">
	        <div class="panel panel-default">
		        <div class="panel-heading text-center bg-database">
                    <h2>Congratulations! Almost Done... :)</h2>
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
					  		<a href="#">
					  			<span class="fa fa-check"></span> Database
					  		</a>
					  	</li>
					  	<li>
					  		<a href="#">
					  			<span class="fa fa-check"> Timezone
					  		</a>
					  		</li>
					  	<li>
					  		<a href="#">
					  			<span class="fa fa-check"> Site Config
					  		</a>
					  	</li>
					  	<li class="active">
					  		<a href="#" onClick="return false">Done!
					  		</a>
					  	</li>
					</ul>
			    </div>
			    <div class="panel-body ins-bg-col">

			    	<div class="alert alert-warning">
			    		<p class="text-center">
			    			<b>Your Login Credentials</b>
			    		</p>
			    		<br>

						<table class="table table-striped">
			    			<thead>
			    				<tr class="active">
			    					<th>Role</th>
			    					<th>Username</th>
			    					<th>Password</th>
			    				</tr>
			    			</thead>
			    			<tbody>
			    				<tr class="success">
			    					<td>Admin</td>
			    					<td><?php echo $session->data['admin_username']; ?></td>
			    					<td><?php echo $session->data['password']; ?></td>
			    				</tr>
			    				<tr class="active">
			    					<td>Cashier</td>
			    					<td><?php echo $session->data['cashier_username']; ?></td>
			    					<td><?php echo $session->data['password']; ?></td>
			    				</tr>
			    				<tr class="info">
			    					<td>Salesman</td>
			    					<td><?php echo $session->data['salesman_username']; ?></td>
			    					<td><?php echo $session->data['password']; ?></td>
			    				</tr>
			    			</tbody>
			    		</table>

			    	</div>

			    	<div class="alert alert-danger text-center">
			    		<p>
			    			Please, Delete 'install' directory manually for security purpose.
			    		</p>
			    	</div>

					<div class="form-group">
						<div class="row">
				            <div class="col-sm-6 col-sm-offset-3">
				                <a class="btn btn-block btn-success" href="<?php echo root_url();?>/index.php">Login Now &rarr;</a>
				            </div>
						</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>