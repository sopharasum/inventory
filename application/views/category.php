<?php $this->load->view('header'); ?>

<span id="alert_action"></span>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-lg-10">
						<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Category Lists</h3>
					</div>
					<div class="col-lg-2" align="right">
						<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#categoryModal" class="btn btn-success btn-xs">Add New Category</button>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table id="category_data" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>ID</th>
									<th>Category Name</th>
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

<!-- Category Modal -->
<div id="categoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" id="category_form">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="glyphicon glyphicon-plus"></i> Add New Category</h4>
	      		</div>
	      		<div class="modal-body">
	        		<div class="form-group">
						<label>Enter Category Name</label>
						<input type="text" name="category_name" id="category_name" class="form-control" required />
					</div>
					<div class="form-group">
						<label>Select Category Status</label>
						<select class="form-control" name="category_status" id="category_status">
							<option value="active">Active</option>
							<option value="inactive">Inactive</option>
						</select>
					</div>
	      		</div>
	      		<input hidden type="text" name="btn_action" id="btn_action" value="Add" />
	      		<div class="modal-footer">
	      			<input type="hidden" name="category_id" id="category_id" />
	      			<input type="submit" name="action" id="action" class="btn btn-info" value="Submit" />
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	      	</form>
    	</div>
  	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#add_button').click(function(){  
           $('#category_form')[0].reset(); 
           $('#action').val("Add");
      	}) 

		var categorydataTable = $('#category_data').DataTable({
			'processing':true,
			'serverSide':true,
			'order':[],
			'ajax':{
				url:'<?php echo base_url(); ?>category/fetch_data',
				type:'POST'
			},
			'columnDefs':[
				{
					'target':[3,4],
					'orderable':false,
				},
			],
			'pageLength':25
		});

		$(document).on('submit','#category_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled','disabled'); //disable the submit button when form has submitted
			var form_data = $(this).serialize();
			$.ajax({
				url:'<?php echo base_url(); ?>category/action',
				method:'POST',
				data:form_data,
				success:function(data)
				{
					$('#category_form')[0].reset();
					$('#categoryModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled',false);
					categorydataTable.ajax.reload();
				}
			});
		});

		$(document).on('click', '.update', function(){
			var category_id = $(this).attr('id');
			var btn_action = 'fetch_single';
			$.ajax({
				url:'<?php echo base_url(); ?>category/fetch_single',
				method:'POST',
				data:{category_id:category_id, btn_action:btn_action},
				dataType:'json',
				success:function(data)
				{
					$('#categoryModal').modal('show');
					$('#category_name').val(data.category_name);
					$('#category_status').val(data.category_status);
					$('.modal-title').html('<i class="glyphicon glyphicon-edit"></i> Edit Category');
					$('#category_id').val(category_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			});
		});

		$(document).on('click', '.change_status', function(){
			var category_id = $(this).attr('id');
			var status = $(this).data('status');
			var btn_action = 'delete';

			if(confirm('Are you sure you want to change status?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>category/change_status',
					method:'POST',
					data:{category_id:category_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						categorydataTable.ajax.reload();
					}
				});
			}
			else
			{
				return false;
			}
		});

		$(document).on('click', '.delete', function(){
			var category_id = $(this).attr('id');
			if(confirm('Are you sure you want to delete?'))
			{
				$.ajax({
					url:'<?php echo base_url(); ?>category/delete',
					method:'POST',
					data:{category_id:category_id},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						categorydataTable.ajax.reload();
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