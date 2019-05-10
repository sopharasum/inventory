<?php 
	class Brand_model extends CI_Model
	{
		var $table = 'brand';
		var $order_column = array(null,'category_id','brand_name','brand_status');

		public function make_query()
		{
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->join('category','category.category_id=brand.category_id');

			if(isset($_POST['search']['value']))
			{
				$this->db->like('category_name', $_POST['search']['value']);
				$this->db->or_like('brand_name', $_POST['search']['value']);
				$this->db->or_like('brand_status', $_POST['search']['value']);
			}

			if(isset($_POST['order']))
			{
				$this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			}
			else
			{
				$this->db->order_by('brand_id', 'DESC');
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

		public function fetch_category_list()
		{
			$this->db->where('category_status', 'active');
			return $this->db->get('category');
		}

		public function insert($data)
		{
			$this->db->insert($this->table, $data);
		}

		public function fetch_single_brand($brand_id)
		{
			$this->db->where('brand_id', $brand_id);
			$query = $this->db->get($this->table);
			return $query->result_array();
		}

		public function update($brand_id, $data)
		{
			$this->db->where('brand_id', $brand_id);
			$this->db->update($this->table, $data);
		}

		public function change_status($brand_id, $data)
		{
			$this->db->where('brand_id', $brand_id);
			$this->db->update($this->table, $data);

		}

		public function delete_brand($brand_id)
		{
			$this->db->where('brand_id', $brand_id);
			$this->db->delete($this->table);
		}
	}
?>