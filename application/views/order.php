<?php $this->load->view('header'); ?>
<link rel="stylesheet" type="text/css" href="http://demo.webslesson.info/php-ajax-inventory-system/css/datepicker.css" />
<script type="text/javascript" src="http://demo.webslesson.info/php-ajax-inventory-system/js/bootstrap-datepicker1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		var date = new Date();
		date.setDate(date.getDate());

		$('#inventory_order_date').datepicker({
			startDate: date,
			format:'yyyy-mm-dd',
			todayBtn: true,
			autoclose: true
		});
	});
</script>

<span id="alert_action"></span>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-lg-10">
						<h3 class="panel-title"><i class="glyphicon glyphicon-shopping-cart"></i> Order Lists</h3>
					</div>
					<div class="col-lg-2" align="right">
						<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#orderModal" class="btn btn-success btn-xs">Add New Order</button>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table id="order_data" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Order ID</th>
									<th>Customer Name</th>
									<th>Total Amount</th>
									<th>Payment Status</th>
									<th>Order Status</th>
									<th>Order Date</th>
									<?php
										if($user_type == 'master')
										{
											echo '<th>Created By</th>';
										}
									?>
									<th>View</th>
									<th>Edit</th>
									<th>Delete</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Order Modal -->
<div id="orderModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" id="order_form">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="glyphicon glyphicon-plus"></i> Create Order</h4>
	      		</div>
	      		<div class="modal-body">
	      			<div class="row">
	      				<div class="col-md-6">
	      					<div class="form-group">
			      				<label>Enter Receiver Name</label>
			      				<input type="text" name="inventory_order_name" id="inventory_order_name" class="form-control" required />
			      			</div>
	      				</div>
	      				<div class="col-md-6">
	      					<div class="form-group">
	      						<label>Order Date</label>
	      						<input type="text" name="inventory_order_date" id="inventory_order_date" class="form-control" required />
	      					</div>
	      				</div>
	      			</div>
	      			<div class="form-group">
	      				<label>Enter Receiver Address</label>
	      				<textarea name="inventory_order_address" id="inventory_order_address" class="form-control" required></textarea>
	      			</div>
	      			<div class="form-group">
	      				<label>Enter Product Details</label>
	      				<hr />
	      				<span id="span_product_details"></span>
	      				<hr />
	      			</div>
	      			<div class="form-group">
	      				<label>Select Payment Status</label>
	      				<select name="payment_status" id="payment_status" class="form-control">
	      					<option value="cash">Cash</option>
	      					<option value="credit">Credit</option>
	      				</select>
	      			</div>
	      		</div>
	      		<input hidden type="text" name="btn_action" id="btn_action" value="Add" />
	      		<div class="modal-footer">
	      			<input type="hidden" name="inventory_order_id" id="inventory_order_id" />
	      			<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	      	</form>
    	</div>
  	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var orderdataTable = $('#order_data').DataTable({
			'processing':true,
			'serverSide':true,
			'order':[],
			'ajax':{
				url:'<?php echo base_url(); ?>order/fetch_data',
				type:'POST'
			},
			<?php
				if($user_type == 'master')
				{
			?>		
			'columnDefs':[
				{
					'targets':[4,5,6,7,8,9],
					'orderable':false,
				}
			],		
			<?php		
				}			
				else
				{
			?>
			'columnDefs':[
				{
					'targets':[4,5,6,7,8],
					'orderable':false,
				}
			],
			<?php
				}
			?>			
			'pageLength':10
		});

		$('#add_button').click(function(){
			//$('#orderModal').modal('show');
			$('#order_form')[0].reset();
			$('#action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
		});

		function add_product_row(count = '')
		{
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-8">';
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control selectpicker" data-live-search="true" required>';
			html += '<?php echo $product_list; ?>';
			html += '</select><input type="hidden" name="hidden_product_id[]" id="hidden_product_id'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-3">';
			html += '<input type="text" name="quantity[]" class="form-control" required />';
			html += '</div>';
			html += '<div class="col-md-1">';
			if(count ==	'')
			{
				html += '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			}
			else
			{
				html += '<button type="button" name="remove" id="'+count+'" class="btn btn-danger btn-xs remove">-</button>';
			}
			html += '</div>';
			html += '</div></div><br/></span>';
			$('#span_product_details').append(html);

			$('.selectpicker').selectpicker();
		}

		var count = 0;
		$(document).on('click','#add_more',function(){
			count = count + 1;
			add_product_row(count);
		});
		$(document).on('click','.remove', function(){
			var row_no = $(this).attr('id');
			$('#row'+row_no).remove();
		});

		$(document).on('submit', '#order_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled','disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:'<?php echo base_url(); ?>order/action',
				method:'POST',
				data:form_data,
				success:function(data)
				{
					$('#order_form')[0].reset();
					$('#orderModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled',false);
					orderdataTable.ajax.reload();
				}
			});
		});

		$(document).on('click', '.update', function(){
			var inventory_order_id = $(this).attr('id');
			$.ajax({
				url:'<?php echo base_url(); ?>order/fetch_single',
				method:'POST',
				data:{inventory_order_id:inventory_order_id},
				dataType:'json',
				success:function(data)
				{
					$('#orderModal').modal('show');
					$('#inventory_order_name').val(data.inventory_order_name);
					$('#inventory_order_date').val(data.inventory_order_date);
					$('#inventory_order_address').val(data.inventory_order_address);
					$('#span_product_details').html(data.product_details);
					$('#payment_status').val(data.payment_status);
					$('.modal-title').html('<i class="glyphicon glyphicon-edit"></i> Edit Order');
					$('#inventory_order_id').val(inventory_order_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			});
		});

		$(document).on('click', '.change_status', function(){
			var order_id = $(this).attr('id');
			var status = $(this).data('status');

			if(confirm('Are you sure you want to change status?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>order/change_status',
					method:'POST',
					data:{order_id:order_id, status:status},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						orderdataTable.ajax.reload();
					}
				});
			}
			else
			{
				return false;
			}
		});

		$(document).on('click', '.delete', function(){
			var order_id = $(this).attr('id');
			if(confirm('Are you sure you want to delete?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>order/delete',
					method:'POST',
					data:{order_id:order_id},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						orderdataTable.ajax.reload();
					}
				});
			}
			else
			{
				return false;
			}
		});
	});
</script>

<?php $this->load->view('footer'); ?>
