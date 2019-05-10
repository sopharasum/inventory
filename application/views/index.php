<?php $this->load->view('header'); ?>
<div class="row">
<?php
	if($user_type == 'master')
	{
?>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total User</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo $total_user; ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Category</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo $total_category; ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Brand</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo $total_brand; ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Item in Stock</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo $total_product; ?></h1>
			</div>
		</div>
	</div>
<?php
	}
?>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Order</strong></div>
			<div class="panel-body" align="center">
				<h1>$ <?php echo $total_order_value; ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Cash Order Value</strong></div>
			<div class="panel-body" align="center">
				<h1>$ <?php echo $total_cash_order_value; ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Credit Order Value</strong></div>
			<div class="panel-body" align="center">
				<h1>$ <?php echo $total_credit_order_value; ?></h1>
			</div>
		</div>
	</div>
	<hr />
	<?php
		if($user_type == 'master')
		{
	?>
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Order Value User Wise</strong></div>
			<div class="panel-body" align="center">
				<?php echo $get_user_wise_total_order; ?>
			</div>
		</div>
	</div>
	<?php		
		}
	?>
</div>

<?php
	$this->load->view('footer');
?>
