<?php
	class Category extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('category_model');
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
				$this->load->view('category', $data);
			}
			else
			{
				redirect(base_url().'account');
			}
		}

		public function fetch_data()
		{
			$fetch_data = $this->category_model->make_datatables();
			$data = array();

			foreach($fetch_data as $row)
			{
				$status = '';
				if($row->category_status == 'active')
				{
					$status = '<span id="'.$row->category_id.'" class="label label-success change_status" data-status="'.$row->category_status.'">Active</span>';
				}
				else
				{
					$status = '<span id="'.$row->category_id.'" class="label label-danger change_status" data-status="'.$row->category_status.'">Inactive</span>';
				}

				$sub_array = array();
				$sub_array[] = $row->category_id;
				$sub_array[] = $row->category_name;
				$sub_array[] = $status;
				$sub_array[] = '<button type="button" name="update" id="'.$row->category_id.'" class="btn btn-warning btn-xs update">Update</button>';
				$sub_array[] = '<button type="button" name="delete" id="'.$row->category_id.'" class="btn btn-danger btn-xs delete" data-status="'.$row->category_status.'">Delete</button>';
				$data[] = $sub_array; //store $sub_array array into $data array
			}

			$output = array(
				"draw" 				=> intval($_POST['draw']),
				"recordsTotal" 		=> $this->category_model->get_all_data(),
				"recordsFiltered" 	=> $this->category_model->get_filtered_data(),
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
					$category_data = array(
						'category_name'			=> $this->input->post('category_name'),
						'category_status'		=> $this->input->post('category_status'),
						'created_by'			=> $this->session->userdata('user_id'),
						'created_datetime'		=> date('Y-m-d H:i:s')
					);

					$this->category_model->insert($category_data);
					echo 'Category has been added successfully';
				}
				elseif($this->input->post('btn_action') == 'Edit')
				{
					$category_data = array(
						'category_name'			=> $this->input->post('category_name'),
						'category_status'		=> $this->input->post('category_status'),
						'modified_by'			=> $this->session->userdata('user_id'),
						'modified_datetime'		=> date('Y-m-d H:i:s')
					);

					$this->category_model->update($this->input->post('category_id'), $category_data);
					echo 'Category has been updated successfully';
				}
			}
		}

		public function fetch_single()
		{
			$output = array();
			$data = $this->category_model->fetch_single_category($this->input->post('category_id'));

			foreach($data as $row)
			{
				$output['category_name'] = $row['category_name'];
				$output['category_status'] = $row['category_status'];
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

			$delete_data = array(
				'category_status'		=> $status,
				'modified_by'			=> $this->session->userdata('user_id'),
				'modified_datetime'		=> date('Y-m-d H:i:s')
			);

			$this->category_model->change_status($this->input->post('category_id'), $delete_data);
			echo 'Category status has been changed to '.$status;
		}

		public function delete()
		{
			$this->category_model->delete_category($this->input->post('category_id'));
			echo 'Category has been deleted successfully';
		}
	}
?>