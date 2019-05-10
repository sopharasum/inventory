<?php
	class Order extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('order_model');
			$this->load->library('pdf');
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
				$data['product_list'] = $this->order_model->product_list();
				$this->load->view('order', $data);
			}
			else
			{
				redirect(base_url().'account');
			}
		}

		public function fetch_data()
		{
			$fetch_data = $this->order_model->make_datatables();
			// echo '<pre>';
			// print_r($fetch_data);
			// exit;
			$data = array();

			foreach($fetch_data as $row)
			{
				$payment_status = '';
				if($row->payment_status == 'cash')
				{
					$payment_status = '<span class="label label-primary">Cash</span>';
				}
				else
				{
					$payment_status = '<span class="label label-warning">Credit</span>';
				}

				$status = '';
				if($row->inventory_order_status == 'active')
				{
					$status = '<span id="'.$row->inventory_order_id.'" class="label label-success change_status" data-status="'.$row->inventory_order_status.'">Active</span>';
				}
				else
				{
					$status = '<span id="'.$row->inventory_order_id.'" class="label label-danger change_status" data-status="'.$row->inventory_order_status.'">Inactive</span>';
				}

				$sub_array = array();
				$sub_array[] = $row->inventory_order_id;
				$sub_array[] = $row->inventory_order_name;
				$sub_array[] = $row->inventory_order_total;
				$sub_array[] = $payment_status;
				$sub_array[] = $status;
				$sub_array[] = $row->inventory_order_date;
				if($this->session->userdata('user_type') == 'master')
				{
					$sub_array[] = $this->order_model->get_user_name($row->user_id);
				}
				$sub_array[] = '<a href="'.base_url().'order/view_order?pdf=1&order_id='.$row->inventory_order_id.'" class="btn btn-info btn-xs">View PDF</a>';
				$sub_array[] = '<button type="button" name="update" id="'.$row->inventory_order_id.'" class="btn btn-warning btn-xs update">Update</button>';
				$sub_array[] = '<button type="button" name="delete" id="'.$row->inventory_order_id.'" class="btn btn-danger btn-xs delete" data-status="'.$row->inventory_order_status.'">Delete</button>';
				$data[] = $sub_array; //store $sub_array array into $data array
			}

			$output = array(
				"draw" 				=> intval($_POST['draw']),
				"recordsTotal" 		=> $this->order_model->get_all_data(),
				"recordsFiltered" 	=> $this->order_model->get_filtered_data(),
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
					$order_data = array(
						'user_id'						=> $this->session->userdata('user_id'),
						'inventory_order_total'			=> 0,
						'inventory_order_date'			=> $this->input->post('inventory_order_date'),
						'inventory_order_name'			=> $this->input->post('inventory_order_name'),
						'inventory_order_address'		=> $this->input->post('inventory_order_address'),
						'payment_status'				=> $this->input->post('payment_status'),
						'inventory_order_status'		=> 'active',
						'inventory_order_created_date'	=> date('Y-m-d')
					);
					$inventory_order_id= $this->order_model->insert($order_data);
					if(isset($inventory_order_id))
					{
						$total_amount = 0;
						for($count=0; $count<count($_POST['product_id']); $count++)
						{
							$product_details = $this->order_model->fetch_product_details($_POST['product_id'][$count]);
							$product_details_data = array(
								'inventory_order_id'	=> $inventory_order_id,
								'product_id'			=> $_POST['product_id'][$count],
								'quantity'				=> $_POST['quantity'][$count],
								'price'					=> $product_details['price'],
								'tax'					=> $product_details['tax']
							);
							$this->order_model->insert_order_product($product_details_data);

							$base_price = $product_details['price']*$_POST['quantity'][$count];
							$tax = ($base_price/100)*$product_details['tax'];
							$total_amount = $total_amount + ($base_price + $tax);
						}
						$update_order_data = array(
							'inventory_order_total'	=> $total_amount
						);						
						$this->order_model->update_order($inventory_order_id, $update_order_data);
					}
					echo 'Order has been created successfully';
				}
				elseif($this->input->post('btn_action') == 'Edit')
				{
					$deleted_result = $this->order_model->delete_order_product($this->input->post('inventory_order_id'));
					if(isset($deleted_result))
					{
						$total_amount = 0;
						for($count=0; $count<count($_POST['product_id']); $count++)
						{
							$product_details = $this->order_model->fetch_product_details($_POST['product_id'][$count]);
							$product_details_data = array(
								'inventory_order_id'	=> $_POST['inventory_order_id'],
								'product_id'			=> $_POST['product_id'][$count],
								'quantity'				=> $_POST['quantity'][$count],
								'price'					=> $product_details['price'],
								'tax'					=> $product_details['tax']
							);
							$this->order_model->insert_order_product($product_details_data);

							$base_price = $product_details['price']*$_POST['quantity'][$count];
							$tax = ($base_price/100)*$product_details['tax'];
							$total_amount = $total_amount + ($base_price + $tax);
						}
						$update_order_data = array(
							'inventory_order_name'		=> $this->input->post('inventory_order_name'),
							'inventory_order_date'		=> $this->input->post('inventory_order_date'),
							'inventory_order_address'	=> $this->input->post('inventory_order_address'),
							'inventory_order_total'		=> $total_amount,
							'payment_status'			=> $this->input->post('payment_status')
						);						
						$this->order_model->update_order($this->input->post('inventory_order_id'), $update_order_data);
					}
					echo 'Order has been updated successfully';
				}
			}
		}

		public function fetch_single()
		{
			$output = array();
			$data = $this->order_model->fetch_single_order($this->input->post('inventory_order_id'));
			foreach($data->result() as $row)
			{
				$output['inventory_order_name'] = $row->inventory_order_name;
				$output['inventory_order_date'] = $row->inventory_order_date;
				$output['inventory_order_address'] = $row->inventory_order_address;
				$output['payment_status'] = $row->payment_status;
			}
			$sub_query = $this->order_model->fetch_order_product($this->input->post('inventory_order_id'));
			$product_details = '';
			$count = '';
			foreach($sub_query->result() as $sub_row)
			{
				$product_details .= '
					<script>
						$(document).ready(function(){
							$("#product_id'.$count.'").selectpicker("val", '.$sub_row->product_id.');
							$(".selectpicker").selectpicker();
						});
					</script>
					<span id="row'.$count.'">
						<div class="row">
							<div class="col-md-8">
								<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" data-live-search="true" required>
								'.$this->order_model->product_list().'
								</select>
								<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$sub_row->product_id.'" />
							</div>
							<div class="col-md-3">
								<input type="text" name="quantity[]" class="form-control" value="'.$sub_row->quantity.'" required />
							</div>
							<div class="col-md-1">
				';
				if($count == '')
				{
					$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
				}	
				else
				{
					$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
				}
				$product_details .='
							</div>
						</div><br/>
					</span>
				';
				$count = $count + 1;
			}
			$output['product_details'] = $product_details;
			echo json_encode($output);
		}

		public function view_order()
		{
			if($this->session->userdata('user_name') != '')
			{
				if($this->input->get('pdf') && $this->input->get('order_id'))
				{
					$output = '';
					$data = $this->order_model->view_order($this->input->get('order_id'));
					foreach($data->result() as $row)
					{
						$output .= '
							<table width="100%" border="1" cellpadding="5" cellspacing="0">
								<tr>
									<td colspan="2" align="center" style="font-size: 18px"><b>Invoice</b></td>
								</tr>
								<tr>
									<td colspan="2">
										<table width="100%"	 cellpadding="5">
											<tr>
												<td width="65%">
													To,<br/>
													<b>RECEIVER (BILL TO)</b><br/>
													Name : '.$row->inventory_order_name.'<br/>
													Billing Address : '.$row->inventory_order_address.'<br/>
												</td>
												<td width="35%">
													Reverse Charge<br/>
													Invoice No. : '.$row->inventory_order_id.'<br/>
													Invoice Date : '.$row->inventory_order_date.'<br/>
												</td>
											</tr>
										</table>
										<br/>
										<table width="100%" border="1" cellpadding="5" cellspacing="0">
											<tr>
												<th rowspan="2">Sr No.</th>
												<th rowspan="2">Product</th>
												<th rowspan="2">Quantity</th>
												<th rowspan="2">Price</th>
												<th rowspan="2">Actual Amt.</th>
												<th colspan="2">Tax (%)</th>
												<th rowspan="2">Total</th>
											</tr>
											<tr>
												<th>Rate</th>
												<th>Amt.</th>
											</tr>
						';
						$product_data = $this->order_model->fetch_order_product($this->input->get('order_id'));
						$count = 0;
						$total = 0;
						$total_actual_amount = 0;
						$total_tax_amount = 0;
						foreach($product_data->result() as $sub_row)
						{
							$count = $count + 1;
							$product_data = $this->order_model->fetch_product_details($sub_row->product_id);
							$actual_amount = $sub_row->quantity*$sub_row->price;
							$tax_amount = ($actual_amount * $sub_row->tax)/100;
							$total_product_amount = $actual_amount + $tax_amount;
							$total_actual_amount = $total_actual_amount + $actual_amount;
							$total_tax_amount = $total_tax_amount + $tax_amount;
							$total = $total + $total_product_amount;

							$output .= '
								<tr>
									<td>'.$count.'</td>
									<td>'.$product_data['product_name'].'</td>
									<td>'.$sub_row->quantity.'</td>
									<td align="right">'.$sub_row->price.'</td>
									<td align="right">'.number_format($actual_amount, 2).'</td>
									<td>'.$sub_row->tax.' %</td>
									<td align="right">'.number_format($tax_amount, 2).'</td>
									<td align="right">'.number_format($total_product_amount, 2).'</td>
								</tr>
							';
						}
						$output .= '
							<tr>
								<td align="right" colspan="4"><b>Total</b></td>
								<td align="right"><b>'.number_format($total_actual_amount, 2).'</b></td>
								<td>&nbsp;</td>
								<td align="right"><b>'.number_format($total_tax_amount, 2).'</b></td>
								<td align="right"><b>'.number_format($total, 2).'</b></td>
							</tr>
						';
						$output .= '
										</table>
										<br />
										<br />
										<br />
										<br />
										<br />
										<br />
										<p align="right">----------------------------------------<br />Receiver Signature</p>
										<br />
										<br />
										<br />
									</td>
								</tr>
							</table>
						';
					}

					$file_name = 'Order-'.$row->inventory_order_id.'.pdf';
					$this->pdf->loadHtml($output);
					$this->pdf->render();
					$this->pdf->stream($file_name, array("Attachment" => false));
				}
			}
			else
			{
				redirect(base_url().'account');
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
				'inventory_order_status'	=> $status
			);

			$this->order_model->change_status($this->input->post('order_id'), $delete_data);
			echo 'Order status has been changed to '.$status;
		}

		public function delete()
		{
			$this->order_model->delete_order($this->input->post('order_id'));
			echo 'Order has been deleted successfully';
		}
	}
?>