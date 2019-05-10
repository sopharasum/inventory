<?php
	class Order_model extends CI_Model
	{
		var $table = 'inventory_order';
		var $order_column = array(null,'inventory_order_name','inventory_order_total','payment_status','inventory_order_status','inventory_order_date');

		public function make_query()
		{
			$this->db->select('*');
			$this->db->from($this->table);
			if($this->session->userdata('user_type') == 'user')
			{
				$this->db->where('user_id', $this->session->userdata('user_id'));
			}
			//$this->db->order_by('inventory_order_id', 'DESC');			

			if(isset($_POST['search']['value']))
			{
				$this->db->like('inventory_order_name', $_POST['search']['value']);
				// $this->db->or_like('inventory_order_id', $_POST['search']['value']);
				// $this->db->or_like('inventory_order_name', $_POST['search']['value']);
				// $this->db->or_like('inventory_order_total', $_POST['search']['value']);
				// $this->db->or_like('inventory_order_status', $_POST['search']['value']);
				// $this->db->or_like('inventory_order_date', $_POST['search']['value']);
				// $this->db->or_like('payment_status', $_POST['search']['value']);
			}

			if(isset($_POST['order']))
			{
				$this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			}
			else
			{
				$this->db->order_by('inventory_order_id', 'DESC');
			}
		}

		public function make_datatables()
		{
			$this->make_query();

			if($_POST['length'] != -1)
			{
				$this->db->limit($_POST['length'], $_POST['start']);
			}
			$query = $this->db->get();
			return $query->result();
		}

		public function get_filtered_data()
		{
			$this->make_query();
			$query = $this->db->get();
			return $query->num_rows();
		}

		public function get_all_data()
		{
			$this->db->select('*');
			$this->db->from($this->table);
			if($this->session->userdata('user_type') == 'user')
			{
				$this->db->where('user_id', $this->session->userdata('user_id'));
			}
			return $this->db->count_all_results();
		}

		public function get_user_name($user_id)
		{			
			$this->db->where('user_id', $user_id);
			$query = $this->db->get('user_details');
			foreach($query->result() as $row)
			{
				return $row->user_name;
			}
		}

		public function product_list()
		{
			$output = '';
			$this->db->order_by('product_id', 'ASC');
			$this->db->where('product_status', 'active');
			$query = $this->db->get('product');
			foreach($query->result() as $row)
			{
				$output .= '<option value="'.$row->product_id.'">'.$row->product_name.'</option>';
			}
			return $output;
		}

		public function insert($data)
		{
			$query = $this->db->insert($this->table, $data);
			if($query)
			{
				return $this->db->insert_id();
			}
		}

		public function fetch_product_details($product_id)
		{
			$this->db->where('product_id', $product_id);
			$query = $this->db->get('product');
			foreach($query->result() as $row)
			{
				$output['product_name'] = $row->product_name;
				$output['quantity'] = $row->product_quantity;
				$output['price'] = $row->product_base_price;
				$output['tax'] = $row->product_tax;
			}
			return $output;
		}

		public function insert_order_product($data)
		{
			$this->db->insert('inventory_order_product', $data);
		}

		public function update_order($inventory_order_id, $data)
		{
			$this->db->where('inventory_order_id', $inventory_order_id);
			$this->db->update($this->table, $data);
		}

		public function fetch_single_order($inventory_order_id)
		{
			$this->db->where('inventory_order_id', $inventory_order_id);
			return $this->db->get($this->table);
		}

		public function fetch_order_product($inventory_order_id)
		{
			$this->db->where('inventory_order_id', $inventory_order_id);
			return $this->db->get('inventory_order_product');
		}

		public function delete_order_product($inventory_order_id)
		{
			$this->db->where('inventory_order_id', $inventory_order_id);
			$query = $this->db->delete('inventory_order_product');
			if($query)
			{
				return true;
			}
		}

		public function view_order($order_id)
		{
			$this->db->limit(1);
			$this->db->where('inventory_order_id', $order_id);
			return $this->db->get($this->table);
		}

		public function change_status($order_id, $data)
		{
			$this->db->where('inventory_order_id', $order_id);
			$this->db->update($this->table, $data);

		}

		public function delete_order($order_id)
		{
			$this->db->where('inventory_order_id', $order_id);
			$this->db->delete($this->table);
		}

	}
?>