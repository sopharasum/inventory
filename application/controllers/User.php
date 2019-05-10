<?php
	class User extends CI_Controller
	{

		public function __construct()
		{
			parent::__construct();
			$this->load->library('encrypt');
			$this->load->model('user_model');
		}

		public function index()
		{
			if($this->session->userdata('user_name') != '')
			{
				$data = array();
				$data['user_type'] = $this->session->userdata('user_type');
				$data['user_name'] = $this->session->userdata('user_name');
				$data['user_id'] = $this->session->userdata('user_id');
				$data['user_email'] = $this->session->userdata('user_email');
				$this->load->view('user', $data);
			}
			else
			{
				redirect(base_url().'account');
			}
			
		}

		public function fetch_data()
		{
			$fetch_data = $this->user_model->make_datatables();
			$data = array();

			foreach($fetch_data as $row)
			{
				$type = '';
				if($row->user_type == 'master')
				{
					$type = 'Master';
				}
				else
				{
					$type = 'User';
				}

				$status = '';
				if($row->user_status == 'active')
				{
					$status = '<span id="'.$row->user_id.'" class="label label-success change_status" data-status="'.$row->user_status.'">Active</span>';
				}
				else
				{
					$status = '<span id="'.$row->user_id.'" class="label label-danger change_status" data-status="'.$row->user_status.'">Inactive</span>';
				}

				$sub_array = array();
				$sub_array[] = $row->user_id;
				$sub_array[] = $row->user_email;
				$sub_array[] = $row->user_name;
				$sub_array[] = $type;
				$sub_array[] = $status;
				$sub_array[] = '<button type="button" name="update" id="'.$row->user_id.'" class="btn btn-warning btn-xs update">Update</button>';
				$sub_array[] = '<button type="button" name="delete" id="'.$row->user_id.'" class="btn btn-danger btn-xs delete" data-status="'.$row->user_status.'">Delete</button>';
				$data[] = $sub_array; //store $sub_array array into $data array
			}

			$output = array(
				"draw" 				=> intval($_POST['draw']),
				"recordsTotal" 		=> $this->user_model->get_all_data(),
				"recordsFiltered" 	=> $this->user_model->get_filtered_data(),
				"data" 				=> $data
			);
			echo json_encode($output);
		}

		public function action()
		{
			if($this->input->post('btn_action'))
			{
				if($this->input->post('btn_action') == 'Add')
				{
					$user_data = array(
						'user_name'			=> $this->input->post('user_name'),
						'user_email'		=> $this->input->post('user_email'),
						'user_password'		=> $this->encrypt->encode($this->input->post('user_password')),
						'user_type'			=> $this->input->post('user_type'),
						'user_status'		=> $this->input->post('user_status'),
						'created_by'		=> $this->session->userdata('user_id'),
						'created_datetime'	=> date('Y-m-d H:i:s')
					);

					$this->user_model->insert($user_data);
					echo 'User has been added successfully';
				}
				elseif($this->input->post('btn_action') == 'Edit')
				{
					if($this->input->post('user_password') != '')
					{
						$user_data = array(
							'user_name'			=> $this->input->post('user_name'),
							'user_email'		=> $this->input->post('user_email'),
							'user_password'		=> $this->encrypt->encode($this->input->post('user_password')),
							'user_type'			=> $this->input->post('user_type'),
							'user_status'		=> $this->input->post('user_status'),
							'modified_by'		=> $this->session->userdata('user_id'),
							'modified_datetime'	=> date('Y-m-d H:i:s')
						);

						$this->user_model->update($this->input->post('user_id'), $user_data);
					}
					else
					{
						$user_data = array(
							'user_name'			=> $this->input->post('user_name'),
							'user_email'		=> $this->input->post('user_email'),
							'user_type'			=> $this->input->post('user_type'),
							'user_status'		=> $this->input->post('user_status'),
							'modified_by'		=> $this->session->userdata('user_id'),
							'modified_datetime'	=> date('Y-m-d H:i:s')
						);

						$this->user_model->update($this->input->post('user_id'), $user_data);
					}

					echo 'User has been updated successfully';
				}
			}
		}

		public function fetch_single()
		{
			$output = array();
			$data = $this->user_model->fetch_single_user($this->input->post('user_id'));

			foreach($data as $row)
			{
				$output['user_email'] = $row['user_email'];
				$output['user_name'] = $row['user_name'];
				$output['user_type'] = $row['user_type'];
				$output['user_status'] = $row['user_status'];
			}
			echo json_encode($output);
		}

		public function change_status()
		{
			$status = 'active';
			if($this->input->post('status') == 'active')
			{
				$status = 'inactive';
			}

			$update_data = array(
				'user_status'		=> $status,
				'modified_by'		=> $this->session->userdata('user_id'),
				'modified_datetime'	=> date('Y-m-d H:i:s')
			);

			$this->user_model->change_status($this->input->post('user_id'), $update_data);
			echo 'User status has been changed to '.$status;
		}

		public function delete()
		{
			$this->user_model->delete_user($this->input->post('user_id'));
			echo 'User has been deleted successfully';
		}
	}
?>