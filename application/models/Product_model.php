<?php
	class Product_model extends CI_Model
	{
		var $table = 'product';
		var $order_column = array(null,'category_id','brand_id','product_name','product_quantity','product_enter_by','product_status');

		public function make_query()
		{
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->join('category','category.category_id=product.category_id');
			$this->db->join('brand','brand.brand_id=product.brand_id');
			$this->db->join('user_details','user_details.user_id=product.product_enter_by');

			if(isset($_POST['search']['value']))
			{
				$this->db->like('category_name', $_POST['search']['value']);
				$this->db->or_like('brand_name', $_POST['search']['value']);
				$this->db->or_like('product_name', $_POST['search']['value']);
				$this->db->or_like('product_quantity', $_POST['search']['value']);
				$this->db->or_like('user_name', $_POST['search']['value']);
				$this->db->or_like('product_id', $_POST['search']['value']);
			}

			if(isset($_POST['order']))
			{
				$this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			}
			else
			{
				$this->db->order_by('product_id', 'DESC');
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
			return $this->db->count_all_results();
		}

		public function get_category_list()
		{
			$this->db->where('category_status', 'active');
			return $this->db->get('category');
		}

		public function fetch_brand($category_id)
		{
			$this->db->where('category_id', $category_id);
			$this->db->where('brand_status', 'active');
			$this->db->order_by('brand_name', 'ASC');
			$query = $this->db->get('brand');

			$output = '<option value="">-- Select Brand --</option>';
			foreach ($query->result() as $row)
			{
				$output .= '<option value="'.$row->brand_id.'">'.$row->brand_name.'</option>';
			}
			return $output;
		}

		public function insert($data)
		{
			$this->db->insert($this->table, $data);
		}

		public function fetch_single_product($product_id)
		{
			$this->db->where('product_id', $product_id);
			$query = $this->db->get($this->table);
			return $query->result_array();
		}

		public function view_product_detail($product_id)
		{
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->join('category','category.category_id=product.category_id');
			$this->db->join('brand','brand.brand_id=product.brand_id');
			$this->db->join('user_details','user_details.user_id=product.product_enter_by');
			$this->db->where('product_id', $product_id);
			$query = $this->db->get();
			return $query->result();
		}

		public function update($product_id, $data)
		{
			$this->db->where('product_id', $product_id);
			$this->db->update($this->table, $data);
		}

		public function change_status($product_id, $data)
		{
			$this->db->where('product_id', $product_id);
			$this->db->update($this->table, $data);
		}

		public function delete_product($product_id)
		{
			$this->db->where('product_id', $product_id);
			$this->db->delete($this->table);
		}

		public function available_product_quantity($product_id)
		{
			$query = $this->db->query("SELECT inventory_order_product.quantity FROM inventory_order_product 
								INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
								WHERE inventory_order_product.product_id = '".$product_id."' AND
							inventory_order.inventory_order_status = 'active'");
			return $query;
		}

		public function update_status($product_id, $data)
		{
			$this->db->where('product_id', $product_id);
			$this->db->update($this->table, $data);
		}
	}
?>