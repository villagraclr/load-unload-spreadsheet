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
				$data['upload_file_name'] = $this->upload->data();
				$full_path = $data['upload_file_name']['full_path'];
				$spreadsheet_ins = new Spreadsheet_lib();
				$document = $spreadsheet_ins->get_document($full_path);
				//$documento = IOFactory::load($full_path);
				# Recuerda que un documento puede tener múltiples hojas
				# obtener conteo e iterar
				//$totalDeHojas = $documento->getSheetCount();
				//$data['totalDeHojas'] = $totalDeHojas;
				$this->load->view('spreadsheet/upload_success', $data);
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
