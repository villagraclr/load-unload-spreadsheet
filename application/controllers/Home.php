<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	
	public function index()
	{
		$error = $this->session->flashdata('error');
		$this->load->view('templates/header');
		$this->load->view('spreadsheet/index',$error);
		$this->load->view('templates/footer');
	}
}
