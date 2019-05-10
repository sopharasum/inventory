<!DOCTYPE html>
<html>
<head>
	<title>Inventory Management System</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
      <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
      <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>            
      <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />  
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
      <style type="text/css">
      	h1{
      		color: #317eac;
      	}
      </style>
</head>
<body>
	<br />
	<div class="container">
		<h2 align="center">Inventory Management System</h2>
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="#" class="navbar-brand">IMS</a>
				</div>
				<ul class="nav navbar-nav">
					<li <?php if($this->uri->segment(1) == 'home'){ echo 'class="active"'; } ?>><a href="<?php echo base_url(); ?>home">Home</a></li>
					<?php
						if($user_type == 'master')
						{
					?>
					<li <?php if($this->uri->segment(1) == 'user'){ echo 'class="active"'; } ?>><a href="<?php echo base_url(); ?>user">User</a></li>
					<li <?php if($this->uri->segment(1) == 'category'){ echo 'class="active"'; } ?>><a href="<?php echo base_url(); ?>category">Category</a></li>
					<li <?php if($this->uri->segment(1) == 'brand'){ echo 'class="active"'; } ?>><a href="<?php echo base_url(); ?>brand">Brand</a></li>
					<li <?php if($this->uri->segment(1) == 'product'){ echo 'class="active"'; } ?>><a href="<?php echo base_url(); ?>product">Product</a></li>
					<?php		
						}
					?>
					<li <?php if($this->uri->segment(1) == 'order'){ echo 'class="active"'; } ?>><a href="<?php echo base_url(); ?>order">Order</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span class="label label-pill label-danger count"></span>
							<?php echo $user_name; ?>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo base_url(); ?>account/profile">Profile</a></li>
							<li><a href="<?php echo base_url(); ?>account/logout">Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
