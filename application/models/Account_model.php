<?php
	class Account_model extends CI_Model
	{
		public function can_login($user_email, $user_password)
		{
			$this->db->where('user_email', $user_email);
			return $this->db->get('user_details');
		}

		public function fetch_single_profile($user_id)
		{
			$this->db->where('user_id', $user_id);
			return $this->db->get('user_details');
		}

		public function edit_profile($user_id, $data)
		{
			$this->db->where('user_id', $user_id);
			$this->db->update('user_details', $data);
		}
	}
?>