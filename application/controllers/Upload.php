<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

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
			$this->load->library('spreadsheet_lib');				
			$this->load->model('Excel_modelo');
			$this->load->helper('functions');
	}
	public function index()
	{
		//$this->output->enable_profiler(TRUE);
		//$this->generate_file();
		//$this->download();
		
	}
	public function do_upload()
        {
		$this->form_validation->set_rules('userfile', 'File', 'trim|xss_clean|callback_file_selected');
		if ($this->form_validation->run()==FALSE) 
		{
			$error = 'Error al cargar archivo';
                        $this->session->set_flashdata('error', $error);
                        //redirect(base_url());

		}
		else
		{
			//$config['file_name']        = 'example1';
			$path = ROOT_UPLOAD_IMPORT_PATH;
			$config['upload_path']          = $path;
			$config['allowed_types']        = 'xlsx|xls';
			$config['remove_spaces'] = TRUE;
			$config['file_ext_tolower'] = TRUE;
					
			$this->load->library('upload', $config);
			$upload_file_name = $this->upload->do_upload('userfile');
			if ( ! $upload_file_name)
			{
				$error = array('error' => $this->upload->display_errors());
				$this->session->set_flashdata('error', $error);
				//redirect(base_url());
			}
			else
			{
				$tables = $this->Excel_modelo->list_tables();
				$tables_columns = array();
				foreach ($tables as $table)
				{
						$columns = $this->Excel_modelo->list_fields($table);
						$tables_columns[$table] = $columns;
				}
				$data['tables_columns'] = $tables_columns;
				
				$data['upload_file_name'] = $this->upload->data();
				
				$full_path = $data['upload_file_name']['full_path'];
				
				$obj_spreadsheet = new Spreadsheet_lib();
				$obj_spreadsheet->init($full_path);
				$worksheet_names = $obj_spreadsheet->get_list_worksheet_names();
				$data['sheet_names'] = $worksheet_names;
				$cols_name = $obj_spreadsheet->get_list_cols_name();
				$data['cols_name'] = $cols_name;
				$topfive_elements = $obj_spreadsheet->get_topfive_elements();
				$data['topfive_elements'] = $topfive_elements;
				$this->load->view('templates/header');
				$this->load->view('spreadsheet/upload_success', $data);
				$this->load->view('templates/footer');
			}
		}
        }
	function file_selected(){

		if (empty($_FILES['userfile']['name'])) {
			$this->form_validation->set_message('file_selected', 'Please select file.');
			return false;
		}else{
			$arr_file = explode('.', $_FILES['userfile']['name']);
			$extension = end($arr_file);
			if($extension == 'xlsx' || $extension == 'xls'){
				return true;
			}else{
				$this->form_validation->set_message('file_selected', 'Please choose correct file.');
				return false;
			}
		}
	}
}
