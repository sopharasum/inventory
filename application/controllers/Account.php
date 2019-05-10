<?php
	class Account extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->library('encrypt');
		}

		public function index()
		{
			if($this->session->userdata('user_name') != '')
			{
				redirect(base_url().'home');
			}
			else
			{
				$this->load->view('login');
			}
			
		}

		public function login()
		{
			if($this->input->post('login'))
			{
				$this->load->library('form_validation');
				$this->form_validation->set_rules('user_email','User Email','required|trim');
				$this->form_validation->set_rules('user_password','User Password','required|trim');
				if($this->form_validation->run())
				{
					$user_email = $this->input->post('user_email');
					$user_password = $this->input->post('user_password');
					$this->load->model('account_model');
					$result = $this->account_model->can_login($user_email, $user_password);

					if($result->num_rows() > 0)
					{
						foreach($result->result_array() as $row)
						{
							$user_current_password = $this->encrypt->decode($row['user_password']);
							if($user_password == $user_current_password)
							{
								if($row['user_status'] == 'active')
								{
									$session_data = array(
										'user_type'	=> $row['user_type'],
										'user_name'	=> $row['user_name'],
										'user_email' => $row['user_email'],
										'user_id' => $row['user_id']
									);
									$this->session->set_userdata($session_data);
									redirect(base_url().'account/enter');
								}
								else
								{
									$this->session->set_flashdata('error','Your account is disabled, Please contact master');
									redirect(base_url().'account');
								}
							}
							else
							{
								$this->session->set_flashdata('error','Wrong Password');
								redirect(base_url().'account');
							}
						}
					}
					else
					{
						$this->session->set_flashdata('error','Invalid User Email');
						redirect(base_url().'account');
					}
				}
				else
				{
					$this->index();
				}
			}
		}

		public function enter()
		{
			if($this->session->userdata('user_name') != '')
			{
				redirect(base_url().'home');
			}
			else
			{
				redirect(base_url().'home');
			}
		}

		public function logout()
		{
			$data = $this->session->all_userdata();
			foreach($data as $row => $rows_value)
			{
				$this->session->unset_userdata($row);
				$this->session->sess_destroy();
				redirect(base_url().'home');
			}
		}

		public function profile()
		{
			if($this->session->userdata('user_name') != '')
			{
				$data = array();
				$data['user_type'] = $this->session->userdata('user_type');
				$data['user_name'] = $this->session->userdata('user_name');
				$data['user_id'] = $this->session->userdata('user_id');
				$data['user_email'] = $this->session->userdata('user_email');

				$this->load->model('account_model');
				$query = $this->account_model->fetch_single_profile($this->session->userdata('user_id'));
				foreach($query->result() as $row)
				{
					$data['user_name'] = $row->user_name;
					$data['user_email'] = $row->user_email;
				}

				$this->load->view('profile', $data);
			}
			else
			{
				redirect(base_url().'account');
			}
		}

		public function edit_profile()
		{
			if($this->input->post('user_name'))
			{
				$this->load->model('account_model');

				if($this->input->post('user_new_password') != '')
				{
					$edit_profile_data = array(
						'user_name'		=> $this->input->post('user_name'),
						'user_email'	=> $this->input->post('user_email'),
						'user_password'	=> $this->encrypt->encode($this->input->post('user_new_password'))
					);
					
					$this->account_model->edit_profile($this->session->userdata('user_id'), $edit_profile_data);
				}
				else
				{
					$edit_profile_data = array(
						'user_name'		=> $this->input->post('user_name'),
						'user_email'	=> $this->input->post('user_email')
					);

					$this->account_model->edit_profile($this->session->userdata('user_id'), $edit_profile_data);
				}
				echo '<div class="alert alert-success">Profile Updated</div>';
			}
		}
	}
?>