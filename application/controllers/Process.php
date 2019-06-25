<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
			parent::__construct();
			$this->load->model('Load_file_model');
			$this->load->helper('functions');
			$this->load->helper(array('form', 'url'));
	}
	public function index()
	{
		redirect('/', 'location', 301);
		
	}
	public function get_status_process()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$status_process_load = $this->Load_file_model->get_status_process($id_load_file);

			$success = array(
				'success' => 'success',
				'summary' => $status_process_load['summary'],
				'status' => $status_process_load['status']
			);				
			echo json_encode($success);
		}
		else
		{
			$this->session->unset_userdata("id_load_file");
			$error = 'No existe identificador de carga';
			echo json_encode($error);
		}
	}
}
