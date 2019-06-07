<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	
	public function index()
	{
		$this->session->unset_userdata('id_load_file');
		$this->session->unset_userdata('worksheet_names');
		$this->session->unset_userdata('tables');
		$this->session->unset_userdata('top_elements');
		$this->session->unset_userdata('sheet_columns');
		$this->load->view('templates/header');
		$this->load->view('spreadsheet/index');
		$this->load->view('templates/footer');
	}
}
