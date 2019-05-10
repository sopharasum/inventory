<!DOCTYPE html>
<html>
<head>
	<title>Inventory Management System</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />  
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<h2 align="center">Inventory Management System</h2>
		<br>
		<div class="panel panel-default">
			<div class="panel-heading">Login</div>
			<div class="panel-body">
				<?php
					if($this->session->flashdata('error'))
					{
						echo '	<div class="alert alert-danger alert-dismissible" role="alert">
  									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
  									<span aria-hidden="true">&times;</span></button>'.$this->session->flashdata('error').'
								</div>';
					}
				?>
				<form method="POST" action="<?php echo base_url(); ?>account/login">
					<div class="form-group">
						<label>User Email</label>
						<input type="text" name="user_email" class="form-control" value="<?php echo set_value('user_email'); ?>" />
						<span class="text-danger"><?php echo form_error('user_email'); ?></span>
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" name="user_password" class="form-control" />
						<span class="text-danger"><?php echo form_error('user_password'); ?></span>
					</div>
					<div class="form-group">
						<input type="submit" name="login" value="Login" class="btn btn-info" />
						
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>