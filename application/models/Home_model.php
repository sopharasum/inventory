<?php
	class Home_model extends CI_Model
	{
		public function count_total_user()
		{
			$this->db->where('user_status', 'active');
			return $this->db->count_all_results('user_details');
		}

		public function count_all_category()
		{
			$this->db->where('category_status', 'active');
			return $this->db->count_all_results('category');
		}

		public function count_all_brand()
		{
			$this->db->where('brand_status', 'active');
			return $this->db->count_all_results('brand');
		}

		public function count_all_product()
		{
			$this->db->where('product_status', 'active');
			return $this->db->count_all_results('product');
		}

		public function count_total_order()
		{
			$this->db->select_sum('inventory_order_total');
			$this->db->from('inventory_order');
			$this->db->where('inventory_order_status', 'active');
			if($this->session->userdata('user_type') == 'user');
			{
				$this->db->where('user_id', $this->session->userdata('user_id'));
			}
			$query = $this->db->get();
			foreach($query->result() as $row)
			{
				return number_format($row->inventory_order_total, 2);
			}
		}

		public function count_cash_total_order()
		{
			$this->db->select_sum('inventory_order_total');
			$this->db->from('inventory_order');
			$this->db->where('payment_status', 'cash');
			$this->db->where('inventory_order_status', 'active');
			if($this->session->userdata('user_type') == 'user')
			{
				$this->db->where('user_id', $this->session->userdata('user_id'));
			}
			$query = $this->db->get();
			foreach($query->result() as $row)
			{
				return number_format($row->inventory_order_total, 2);
			}
		}

		public function count_credit_total_order()
		{
			$this->db->select_sum('inventory_order_total');
			$this->db->from('inventory_order');
			$this->db->where('payment_status', 'credit');
			$this->db->where('inventory_order_status', 'active');
			if($this->session->userdata('user_type') == 'user')
			{
				$this->db->where('user_id', $this->session->userdata('user_id'));
			}
			$query = $this->db->get();
			foreach($query->result() as $row)
			{
				return number_format($row->inventory_order_total, 2);
			}
		}

		public function get_user_wise_total_order()
		{
			$query =  $this->db->query('SELECT sum(inventory_order.inventory_order_total) as order_total, sum(CASE WHEN inventory_order.payment_status = "cash" THEN inventory_order.inventory_order_total ELSE 0 END) AS cash_order_total, sum(CASE WHEN inventory_order.payment_status = "credit" THEN inventory_order.inventory_order_total ELSE 0 END) AS credit_order_total, user_details.user_name FROM inventory_order INNER JOIN user_details ON user_details.user_id=inventory_order.user_id WHERE inventory_order.inventory_order_status="active" GROUP BY inventory_order.user_id');
			$output = '
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<tr>
							<th>User Name</th>
							<th>Total Order Value</th>
							<th>Total Cash Order</th>
							<th>Total Credit Order</th>
						</tr>
			';

			$total_order = 0;
			$total_cash_order = 0;
			$total_credit_order = 0;

			foreach($query->result() as $row)
			{
				$output .= '
					<tr>
						<td>'.$row->user_name.'</td>
						<td align="right">$ '.$row->order_total.'</td>
						<td align="right">$ '.$row->cash_order_total.'</td>
						<td align="right">$ '.$row->credit_order_total.'</td>
					</tr>
				';

				$total_order = $total_order + $row->order_total;
				$total_cash_order = $total_cash_order + $row->cash_order_total;
				$total_credit_order = $total_credit_order + $row->credit_order_total;
			}

			$output .= '
				<tr>
					<td align="right"><b>Total</b></td>
					<td align="right"><b>$ '.$total_order.'</b></td>
					<td align="right"><b>$ '.$total_cash_order.'</b></td>
					<td align="right"><b>$ '.$total_credit_order.'</b></td>
				</tr></table></div>
			';

			return $output;
		}
	}
?>