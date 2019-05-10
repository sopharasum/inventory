<?php $this->load->view('header'); ?>

<span id="alert_action"></span>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-lg-10">
						<h3 class="panel-title"><i class="glyphicon glyphicon-tags"></i> Product Lists</h3>
					</div>
					<div class="col-lg-2" align="right">
						<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#productModal" class="btn btn-success btn-xs">Add New Product</button>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table id="product_data" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>ID</th>
									<th>Category</th>
									<th>Brand</th>
									<th>Product Name</th>
									<th>Quantity</th>
									<th>Enter By</th>
									<th>Status</th>
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

<!-- Product Modal -->
<div id="productModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" id="product_form">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="glyphicon glyphicon-plus"></i> Add New Product</h4>
	      		</div>
	      		<div class="modal-body">
	      			<div class="form-group">
	      				<label>Select Product Category</label>
	      				<select name="category_id" id="category_id" class="form-control" required>
	      					<option value="">-- Select Category --</option>
	      					<?php
	      						foreach($category_list->result_array() as $category_row)
	      						{
	      					?>
	      					<option value="<?php echo $category_row['category_id'] ?>"><?php echo $category_row['category_name']; ?></option>
	      					<?php		
	      						}
	      					?>
	      				</select>
	      			</div>
	      			<div class="form-group">
	      				<label>Select Product Brand</label>
	      				<select name="brand_id" id="brand_id" class="form-control" required>
	      					<option value="">-- Select Brand --</option>
	      				</select>
	      			</div>
	        		<div class="form-group">
						<label>Enter Product Name</label>
						<input type="text" name="product_name" id="product_name" class="form-control" required />
					</div>
					<div class="form-group">
						<label>Enter Product Description</label>
						<textarea name="product_description" id="product_description" class="form-control" rows="5" required></textarea>
					</div>
					<div class="form-group">
						<label>Enter Product Quantity</label>
						<div class="input-group">
							<input type="text" name="product_quantity" id="product_quantity" class="form-control" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
							<span class="input-group-addon">
								<select name="product_unit" id="product_unit" required>
									<option value="">-- Select Unit --</option>
                                    <option value="Bags">Bags</option>
                                    <option value="Bottles">Bottles</option>
                                    <option value="Box">Box</option>
                                    <option value="Dozens">Dozens</option>
                                    <option value="Feet">Feet</option>
                                    <option value="Gallon">Gallon</option>
                                    <option value="Grams">Grams</option>
                                    <option value="Inch">Inch</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Liters">Liters</option>
                                    <option value="Meter">Meter</option>
                                    <option value="Nos">Nos</option>
                                    <option value="Packet">Packet</option>
                                    <option value="Rolls">Rolls</option>
								</select>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label>Enter Product Base Price</label>
						<input type="text" name="product_base_price" id="product_base_price" class="form-control" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
					</div>
					<div class="form-group">
						<label>Enter Product Tax (%)</label>
						<input type="text" name="product_tax" id="product_tax" class="form-control" required pattern="[+-]?([0-9]*[.])?[0-9]+" />
					</div>
					<div class="form-group">
						<label>Select Product Status</label>
						<select class="form-control" name="product_status" id="product_status">
							<option value="active">Active</option>
							<option value="inactive">Inactive</option>
						</select>
					</div>
	      		</div>
	      		<input hidden type="text" name="btn_action" id="btn_action" value="Add" />
	      		<div class="modal-footer">
	      			<input type="hidden" name="product_id" id="product_id" />
	      			<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	      	</form>
    	</div>
  	</div>
</div>

<!-- Product View Modal -->
<div id="productdetailsModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" id="product_form">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="glyphicon glyphicon-eye-open"></i> View Product Details</h4>
	      		</div>
	      		<div class="modal-body">
	      			<div id="product_details"></div>
	      		</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	      	</form>
    	</div>
  	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#add_button').click(function(){  
           $('#product_form')[0].reset(); 
           $('#action').val("Add");
      	});

      	var productdataTable = $('#product_data').DataTable({
			'processing':true,
			'serverSide':true,
			'order':[],
			'ajax':{
				url:'<?php echo base_url(); ?>product/fetch_data',
				type:'POST'
			},
			'columnDefs':[
				{
					'target':[7,8,9],
					'orderable':false,
				},
			],
			'pageLength':10
		});

		$('#category_id').change(function(){
			var category_id = $('#category_id').val();
			if(category_id != '')
			{
				$.ajax({
					url:'<?php echo base_url(); ?>product/fetch_brand',
					method:'POST',
					data:{category_id:category_id},
					success:function(data)
					{
						$('#brand_id').html(data);
					}
				});
			}
			else
			{
				$('#brand_id').html('<option value="">-- Select Brand --</option>');
			}			
		});

		$(document).on('submit','#product_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled','disabled'); //disable the submit button when form has submitted
			var form_data = $(this).serialize();
			$.ajax({
				url:'<?php echo base_url(); ?>product/action',
				method:'POST',
				data:form_data,
				success:function(data)
				{
					$('#product_form')[0].reset();
					$('#productModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled',false);
					productdataTable.ajax.reload();
				}
			});
		});

		$(document).on('click', '.view', function(){
			var product_id = $(this).attr('id');
			var btn_action = 'product_details';

			$.ajax({
				url:'<?php echo base_url(); ?>product/action',
				method:'POST',
				data:{product_id:product_id, btn_action:btn_action},
				success:function(data)
				{
					$('#productdetailsModal').modal('show');
					$('#product_details').html(data);
				}
			});
		});

		$(document).on('click', '.update', function(){
			var product_id = $(this).attr('id');
			$.ajax({
				url:'<?php echo base_url(); ?>product/fetch_single',
				method:'POST',
				data:{product_id:product_id},
				dataType:'json',
				success:function(data)
				{
					$('#productModal').modal('show');
					$('#category_id').val(data.category_id);
					$('#brand_id').html(data.brand_select_box);
					$('#brand_id').val(data.brand_id);
					$('#product_name').val(data.product_name);
					$('#product_description').val(data.product_description);
					$('#product_quantity').val(data.product_quantity);
					$('#product_unit').val(data.product_unit);
					$('#product_base_price').val(data.product_base_price);
					$('#product_tax').val(data.product_tax);
					$('#product_status').val(data.product_status);
					$('.modal-title').html('<i class="glyphicon glyphicon-edit"></i> Edit Product');
					$('#product_id').val(product_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			});
		});

		$(document).on('click', '.change_status', function(){
			var product_id = $(this).attr('id');
			var status = $(this).data('status');

			if(confirm('Are you sure you want to change status?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>product/change_status',
					method:'POST',
					data:{product_id:product_id, status:status},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						productdataTable.ajax.reload();
					}
				});
			}
			else
			{
				return false;
			}
		});

		$(document).on('click', '.delete', function(){
			var product_id = $(this).attr('id');
			if(confirm('Are you sure you want to delete?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>product/delete',
					method:'POST',
					data:{product_id:product_id},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						productdataTable.ajax.reload();
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