<?php
	class Product extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('product_model');
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
				$data['category_list'] = $this->product_model->get_category_list();
				$this->load->view('product', $data);
			}
			else
			{
				redirect(base_url().'account');
			}
		}

		public function fetch_data()
		{
			$fetch_data = $this->product_model->make_datatables();
			$data = array();

			foreach($fetch_data as $row)
			{
				$status = '';
				if($row->product_status == 'active')
				{
					$status = '<span id="'.$row->product_id.'" class="label label-success change_status" data-status="'.$row->product_status.'">Active</span>';
				}
				else
				{
					$status = '<span id="'.$row->product_id.'" class="label label-danger change_status" data-status="'.$row->product_status.'">Inactive</span>';
				}

				$sub_array = array();
				$sub_array[] = $row->product_id;
				$sub_array[] = $row->category_name;
				$sub_array[] = $row->brand_name;
				$sub_array[] = $row->product_name;
				$sub_array[] = $row->product_quantity.' '.$row->product_unit;
				$sub_array[] = $row->user_name;
				$sub_array[] = $status;
				$sub_array[] = '<button type="button" name="view" id="'.$row->product_id.'" class="btn btn-info btn-xs view">View</button>';
				$sub_array[] = '<button type="button" name="update" id="'.$row->product_id.'" class="btn btn-warning btn-xs update">Update</button>';
				$sub_array[] = '<button type="button" name="delete" id="'.$row->product_id.'" class="btn btn-danger btn-xs delete" data-status="'.$row->brand_status.'">Delete</button>';
				$data[] = $sub_array; //store $sub_array array into $data array
			}

			$output = array(
				"draw" 				=> intval($_POST['draw']),
				"recordsTotal" 		=> $this->product_model->get_all_data(),
				"recordsFiltered" 	=> $this->product_model->get_filtered_data(),
				"data" 				=> $data
			);
			echo json_encode($output);
		}

		public function available_product_quantity()
		{
			$product_data = $this->order_model->fetch_product_details($product_id);
			$data = $this->product_model->available_product_quantity($product_id);
			foreach($data->result() as $row)
			{
				$total = $total + $row->quantity;
			}

			$available_quantity = intval($product_data['quantity']) - intval($total);
			if($available_quantity == 0)
			{
				$update_data = array(
					'product_status'	=> 'inactive'
				);
				$this->product_model->update_status($update_data, $product_id);
			}

			return $available_quantity;
		}

		public function action()
		{
			if($this->input->post('btn_action'))
			{
				if($this->input->post('btn_action') == 'Add')
				{ 
					$product_data = array(
						'category_id'			=> $this->input->post('category_id'),
						'brand_id'				=> $this->input->post('brand_id'),
						'product_name'			=> $this->input->post('product_name'),
						'product_description'	=> $this->input->post('product_description'),
						'product_quantity'		=> $this->input->post('product_quantity'),
						'product_unit'			=> $this->input->post('product_unit'),
						'product_base_price'	=> $this->input->post('product_base_price'),
						'product_tax'			=> $this->input->post('product_tax'),
						'product_enter_by'		=> $this->session->userdata('user_id'),
						'product_status'		=> $this->input->post('product_status'),
						'product_date'			=> date('Y-m-d')
					);

					$this->product_model->insert($product_data);
					echo 'Product has been added successfully';
				}
				elseif($this->input->post('btn_action') == 'Edit')
				{
					$product_data = array(
						'category_id'			=> $this->input->post('category_id'),
						'brand_id'				=> $this->input->post('brand_id'),
						'product_name'			=> $this->input->post('product_name'),
						'product_description'	=> $this->input->post('product_description'),
						'product_quantity'		=> $this->input->post('product_quantity'),
						'product_unit'			=> $this->input->post('product_unit'),
						'product_base_price'	=> $this->input->post('product_base_price'),
						'product_tax'			=> $this->input->post('product_tax'),
						'product_status'		=> $this->input->post('product_status')
					);

					$this->product_model->update($this->input->post('product_id'), $product_data);
					echo 'Product has been updated successfully';
				}
				elseif($this->input->post('btn_action') == 'product_details')
				{
					$output = '
						<div class="table-responsive">
   							<table class="table table-boredered">';
					$data = $this->product_model->view_product_detail($this->input->post('product_id'));
					foreach($data as $row)
					{
						$status = '';
						if($row->product_status == 'active')
						{
							$status = '<span class="label label-success">Active</span>';
						}
						else
						{
							$status = '<span class="label label-danger">Inactive</span>';
						}

						$output .= '
							<tr>
								<td>Product Name</td>
								<td>'.$row->product_name.'</td>
							</tr>
							<tr>
								<td width="30%">Product Description</td>
								<td width="70%">'.$row->product_description.'</td>
							</tr>
							<tr>
								<td>Category</td>
								<td>'.$row->category_name.'</td>
							</tr>
							<tr>
								<td>Brand Name</td>
								<td>'.$row->brand_name.'</td>
							</tr>
							<tr>
								<td>Available Quantity</td>
								<td>'.$row->product_quantity.' '.$row->product_unit.'</td>
							</tr>
							<tr>
								<td>Base Price</td>
								<td>'.$row->product_base_price.'</td>
							</tr>
							<tr>
								<td>Tax (%)</td>
								<td>'.$row->product_tax.'</td>
							</tr>
							<tr>
								<td>Enter By</td>
								<td>'.$row->user_name.'</td>
							</tr>
							<tr>
								<td>Status</td>
								<td>'.$status.'</td>
							</tr>
						';
					}

					$output .= '
						</table>
					</div>
					';

					echo $output;
				}
			}
		}

		public function fetch_single()
		{
			$output = array();
			$data = $this->product_model->fetch_single_product($this->input->post('product_id'));
			// echo '<pre>';
			// print_r($data);
			// exit;

			foreach($data as $row)
			{
				$output['category_id']				= $row['category_id'];
				$output['brand_id'] 				= $row['brand_id'];
				$output["brand_select_box"]			= $this->product_model->fetch_brand($row["category_id"]);
				$output['product_name'] 			= $row['product_name'];
				$output['product_description'] 		= $row['product_description'];
				$output['product_quantity'] 		= $row['product_quantity'];
				$output['product_unit'] 			= $row['product_unit'];
				$output['product_base_price'] 		= $row['product_base_price'];
				$output['product_tax'] 				= $row['product_tax'];
				$output['product_status'] 			= $row['product_status'];
			}
			echo json_encode($output);
		}

		public function fetch_brand()
		{
			if($this->input->post('category_id'))
			{
				echo $this->product_model->fetch_brand($this->input->post('category_id'));
			}
		}

		public function change_status()
		{
			$status = 'active';
			if($this->input->post('status') == 'active')
			{
				$status = 'inactive';
			}

			$delete_data = array(
				'product_status'			=> $status
			);

			$this->product_model->change_status($this->input->post('product_id'), $delete_data);
			echo 'Product status has been changed to '.$status;
		}

		public function delete()
		{
			$this->product_model->delete_product($this->input->post('product_id'));
			echo 'Product has been deleted successfully';
		}
	}
?>