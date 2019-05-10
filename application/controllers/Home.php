<?php
	class Home extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('home_model');
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
				$data['total_user'] = $this->home_model->count_total_user();
				$data['total_category'] = $this->home_model->count_all_category();
				$data['total_brand'] = $this->home_model->count_all_brand();
				$data['total_product'] = $this->home_model->count_all_product();
				$data['total_order_value'] = $this->home_model->count_total_order();
				$data['total_cash_order_value'] = $this->home_model->count_cash_total_order();
				$data['total_credit_order_value'] = $this->home_model->count_credit_total_order();
				$data['get_user_wise_total_order'] = $this->home_model->get_user_wise_total_order();
				$this->load->view('index', $data);
			}
			else
			{
				redirect(base_url().'account');
			}
		}
	}
?>