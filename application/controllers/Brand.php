<?php
	class Brand extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('brand_model');
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
				$data['category_list'] = $this->brand_model->fetch_category_list();
				$this->load->view('brand', $data);
			}
			else
			{
				redirect(base_url().'account');
			}
		}

		public function fetch_data()
		{
			$fetch_data = $this->brand_model->make_datatables();
			$data = array();

			foreach($fetch_data as $row)
			{
				$status = '';
				if($row->brand_status == 'active')
				{
					$status = '<span id="'.$row->brand_id.'" class="label label-success change_status" data-status="'.$row->brand_status.'">Active</span>';
				}
				else
				{
					$status = '<span id="'.$row->brand_id.'" class="label label-danger change_status" data-status="'.$row->brand_status.'">Inactive</span>';
				}

				$sub_array = array();
				$sub_array[] = $row->brand_id;
				$sub_array[] = $row->category_name;
				$sub_array[] = $row->brand_name;
				$sub_array[] = $status;
				$sub_array[] = '<button type="button" name="update" id="'.$row->brand_id.'" class="btn btn-warning btn-xs update">Update</button>';
				$sub_array[] = '<button type="button" name="delete" id="'.$row->brand_id.'" class="btn btn-danger btn-xs delete" data-status="'.$row->brand_status.'">Delete</button>';
				$data[] = $sub_array; //store $sub_array array into $data array
			}

			$output = array(
				"draw" 				=> intval($_POST['draw']),
				"recordsTotal" 		=> $this->brand_model->get_all_data(),
				"recordsFiltered" 	=> $this->brand_model->get_filtered_data(),
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
					$brand_data = array(
						'category_id'			=> $this->input->post('category_id'),
						'brand_name'			=> $this->input->post('brand_name'),
						'brand_status'			=> $this->input->post('brand_status'),
						'created_by'			=> $this->session->userdata('user_id'),
						'created_datetime'		=> date('Y-m-d H:i:s')
					);

					$this->brand_model->insert($brand_data);
					echo 'Brand has been added successfully';
				}
				elseif($this->input->post('btn_action') == 'Edit')
				{
					$brand_data = array(
						'category_id'			=> $this->input->post('category_id'),
						'brand_name'			=> $this->input->post('brand_name'),
						'brand_status'			=> $this->input->post('brand_status'),
						'modified_by'			=> $this->session->userdata('user_id'),
						'modified_datetime'		=> date('Y-m-d H:i:s')
					);

					$this->brand_model->update($this->input->post('brand_id'), $brand_data);
					echo 'Brand has been updated successfully';
				}
			}
		}

		public function fetch_single()
		{
			$output = array();
			$data = $this->brand_model->fetch_single_brand($this->input->post('brand_id'));

			foreach($data as $row)
			{
				$output['category_id']		= $row['category_id'];
				$output['brand_name'] 		= $row['brand_name'];
				$output['brand_status'] 	= $row['brand_status'];
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
				'brand_status'			=> $status,
				'modified_by'			=> $this->session->userdata('user_id'),
				'modified_datetime'		=> date('Y-m-d H:i:s')
			);

			$this->brand_model->change_status($this->input->post('brand_id'), $delete_data);
			echo 'Brand status has been changed to '.$status;
		}

		public function delete()
		{
			$this->brand_model->delete_brand($this->input->post('brand_id'));
			echo 'Brand has been deleted successfully';
		}
	}
?>