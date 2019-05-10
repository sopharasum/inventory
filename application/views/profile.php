<?php $this->load->view('header'); ?>
<div class="panel panel-default">
	<div class="panel-heading">Edit Profile</div>
	<div class="panel-body">
		<form method="POST" id="edit_profile_form">
			<span id="message"></span>
			<div class="form-group">
				<label>Name</label>
				<input type="text" name="user_name" id="user_name" class="form-control" value="<?php echo $user_name; ?>" required />
			</div>
			<div class="form-group">
				<label>Email</label>
				<input type="text" name="user_email" id="user_email" class="form-control" value="<?php echo $user_email; ?>" required />
			</div>
			<hr>
			<label>Leave Password blank if you do not want to change</label>
			<div class="form-group">
				<label>New Password</label>
				<input type="password" name="user_new_password" id="user_new_password" class="form-control" />
			</div>
			<div class="form-group">
				<label>Re-enter New Password</label>
				<input type="password" name="user_re_enter_password" id="user_re_enter_password" class="form-control" />
				<span id="error_password"></span>
			</div>
			<div class="form-group">
				<input type="submit" name="edit_profile" id="edit_profile" value="Edit" class="btn btn-info" />
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#edit_profile_form').on('submit', function(event){
			event.preventDefault();
			if($('#user_new_password').val() != '')
			{
				if($('#user_new_password').val() != $('#user_re_enter_password').val())
				{
					$('#error_password').html('<label class="text-danger">Password Not Match</label>');
					return false;
				}
				else
				{
					$('#error_password').html('');
				}
			}
			$('#edit_profile').attr('disabled','disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:'<?php echo base_url(); ?>account/edit_profile',
				method:'POST',
				data:form_data,
				success:function(data)
				{
					$('#edit_profile').attr('disabled',false);
					$('#user_new_password').val('');
					$('#user_re_enter_password').val('');
					$('#message').html(data);
				}
			});
		});
	});
</script>

<?php $this->load->view('footer'); ?>