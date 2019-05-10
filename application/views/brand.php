<?php $this->load->view('header'); ?>

<span id="alert_action"></span>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-lg-10">
						<h3 class="panel-title"><i class="glyphicon glyphicon-bitcoin"></i> Brand Lists</h3>
					</div>
					<div class="col-lg-2" align="right">
						<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#brandModal" class="btn btn-success btn-xs">Add New Brand</button>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table id="brand_data" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>ID</th>
									<th>Category</th>
									<th>Brand Name</th>
									<th>Status</th>
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

<!-- Brand Modal -->
<div id="brandModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" id="brand_form">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="glyphicon glyphicon-plus"></i> Add New Brand</h4>
	      		</div>
	      		<div class="modal-body">
	      			<div class="form-group">
	      				<label>Select Brand Category</label>
	      				<select name="category_id" id="category_id" class="form-control" required>
	      					<option value="">-- Select Category --</option>
	      					<?php
	      						foreach($category_list->result_array() as $row)
	      						{
	      					?>
	      					<option value="<?php echo $row['category_id'] ?>"><?php echo $row['category_name']; ?></option>
	      					<?php		
	      						}
	      					?>
	      				</select>
	      			</div>
	        		<div class="form-group">
						<label>Enter Brand Name</label>
						<input type="text" name="brand_name" id="brand_name" class="form-control" required />
					</div>
					<div class="form-group">
						<label>Select Brand Status</label>
						<select class="form-control" name="brand_status" id="brand_status">
							<option value="active">Active</option>
							<option value="inactive">Inactive</option>
						</select>
					</div>
	      		</div>
	      		<input hidden type="text" name="btn_action" id="btn_action" value="Add" />
	      		<div class="modal-footer">
	      			<input type="hidden" name="brand_id" id="brand_id" />
	      			<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	      	</form>
    	</div>
  	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#add_button').click(function(){  
           $('#brand_form')[0].reset(); 
           $('#action').val("Add");
      	});

		var branddataTable = $('#brand_data').DataTable({
			'processing':true,
			'serverSide':true,
			'order':[],
			'ajax':{
				url:'<?php echo base_url(); ?>brand/fetch_data',
				type:'POST'
			},
			'columnDefs':[
				{
					'targets':[4,5],
					'orderable':false,
				},
			],
			'pageLength':10
		});

		$(document).on('submit','#brand_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled','disabled'); //disable the submit button when form has submitted
			var form_data = $(this).serialize();
			$.ajax({
				url:'<?php echo base_url(); ?>brand/action',
				method:'POST',
				data:form_data,
				success:function(data)
				{
					$('#brand_form')[0].reset();
					$('#brandModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled',false);
					branddataTable.ajax.reload();
				}
			});
		});

		$(document).on('click', '.update', function(){
			var brand_id = $(this).attr('id');
			$.ajax({
				url:'<?php echo base_url(); ?>brand/fetch_single',
				method:'POST',
				data:{brand_id:brand_id},
				dataType:'json',
				success:function(data)
				{
					$('#brandModal').modal('show');
					$('#category_id').val(data.category_id);
					$('#brand_name').val(data.brand_name);
					$('#brand_status').val(data.brand_status);
					$('.modal-title').html('<i class="glyphicon glyphicon-edit"></i> Edit Brand');
					$('#brand_id').val(brand_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			});
		});

		$(document).on('click', '.change_status', function(){
			var brand_id = $(this).attr('id');
			var status = $(this).data('status');

			if(confirm('Are you sure you want to change status?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>brand/change_status',
					method:'POST',
					data:{brand_id:brand_id, status:status},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						branddataTable.ajax.reload();
					}
				});
			}
			else
			{
				return false;
			}
		});

		$(document).on('click', '.delete', function(){
			var brand_id = $(this).attr('id');
			if(confirm('Are you sure you want to delete?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>brand/delete',
					method:'POST',
					data:{brand_id:brand_id},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						branddataTable.ajax.reload();
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