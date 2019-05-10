<?php
	class Category_model extends CI_Model
	{
		var $table = 'category';
		var $select_column = array('category_id','category_name','category_status');
		var $order_column = array(null,'category_name','category_status');

		public function make_query()
		{
			$this->db->select($this->select_column);
			$this->db->from($this->table);

			if(isset($_POST['search']['value']))
			{
				$this->db->like('category_name', $_POST['search']['value']);
				$this->db->or_like('category_status', $_POST['search']['value']);
			}

			if(isset($_POST['order']))
			{
				$this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			}
			else
			{
				$this->db->order_by('category_id', 'DESC');
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

		public function insert($data)
		{
			$this->db->insert($this->table, $data);
		}

		public function fetch_single_category($category_id)
		{
			$this->db->where('category_id', $category_id);
			$query = $this->db->get($this->table);
			return $query->result_array();
		}

		public function update($category_id, $data)
		{
			$this->db->where('category_id', $category_id);
			$this->db->update($this->table, $data);
		}

		public function change_status($category_id, $data)
		{
			$this->db->where('category_id', $category_id);
			$this->db->update($this->table, $data);

		}

		public function delete_category($category_id)
		{
			$this->db->where('category_id', $category_id);
			$this->db->delete($this->table);
		}
	}
?>