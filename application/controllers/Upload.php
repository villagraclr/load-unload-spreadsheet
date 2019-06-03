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
			$this->load->model('Excel_model');
			$this->load->model('Load_file_model');
			$this->load->helper('functions');
			$this->load->helper(array('form', 'url'));
			

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
            echo json_encode($error);
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
				echo json_encode($error);
			}
			else
			{
				$upload_data = $this->upload->data();
				$file_name = $upload_data['file_name'];
				$full_path = $upload_data['full_path'];
				$load_file = array(
					'file' => $file_name,
					'path' => $full_path
				);
				$id_load_file = $this->Load_file_model->insert_file_uploaded($load_file);
				
				if($id_load_file > 0 )
				{
					$this->session->set_userdata("id_load_file", $id_load_file);					
					
					$tables = $this->Excel_model->list_tables();
					
					$obj_spreadsheet = new Spreadsheet_lib();
					$obj_spreadsheet->init($full_path);
					$worksheet_names = $obj_spreadsheet->get_list_worksheet_names();
					
					$sheet_tables = array();
					foreach ($worksheet_names as $worksheet)
					{
						$item_data = array(
							'id_load' => $id_load_file,
							'sheet' => $worksheet,
							'tmp_table' => '-',
							'relation' => '{}'
						);
						$sheet_tables[] = $item_data;
					}
					$res = $this->Load_file_model->insert_sheet_table($sheet_tables);
					$this->session->set_userdata("worksheet_names", $worksheet_names);
					$this->session->set_userdata("tables", $tables);
				}
				else
				{
					$this->session->unset_userdata("id_load_file");
				}			
				
				$success = array(
					'success' => 'success'
				);
				
				echo json_encode($success);
			}
		}
    }
	public function load_preview_data()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		$full_path = $this->Load_file_model->get_full_path($id_load_file);
		$obj_spreadsheet = new Spreadsheet_lib();
		$obj_spreadsheet->init($full_path);
		//$cols_name = $obj_spreadsheet->get_list_cols_name();
		$topfive_elements = $obj_spreadsheet->get_topfive_elements();
		$this->session->set_userdata("topfive_elements", $topfive_elements);
		$success = array(
			'success' => 'success',
			'topfive_elements' => $topfive_elements
		);
		echo json_encode($success);
	}
	public function step2()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		$worksheet_names = $this->session->userdata('worksheet_names');
		$tables_columns = $this->session->userdata('tables_columns');
		
		$data['id_load_file'] = $id_load_file;
		$data['worksheet_names'] = $worksheet_names;
		$data['tables_columns'] = $tables_columns;
		
		$this->load->view('templates/header');
		$this->load->view('spreadsheet/step-two', $data);
		$this->load->view('templates/footer');
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
	public function get_worksheets()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		$sheets = $this->Load_file_model->get_sheet_not_asigned($id_load_file);
		
		$success = array(
			'success' => 'success',
			'sheets' => $sheets
		);
		echo json_encode($success);
	}
	
	public function get_tables_available()
	{
		$tables_available = array();
		$selected_sheet = $this->security->xss_clean($this->input->post('selected_sheet'));
		$this->form_validation->set_rules('selected_sheet', 'Sheet', 'trim|required');
		if ($this->form_validation->run()==FALSE) 
		{
			$error = 'Error en validación';
            echo json_encode($error);
		}
		else
		{
			$id_load_file = $this->session->userdata('id_load_file');
			$tables_asigned = $this->Load_file_model->get_table_not_asigned($id_load_file);
			$tables = $this->session->userdata('tables');
			
			foreach($tables as $item):
				if (!in_array($item, $tables_asigned))
				{
					array_push($tables_available,$item);
				}
			endforeach;
		}
		$success = array(
			'success' => 'success',
			'tables_available' => $tables_available
		);
		echo json_encode($success);
	}
	public function set_associate_sheet_table()
	{
		$selected_sheet = $this->security->xss_clean($this->input->post('selected_sheet'));
		$selected_table_available = $this->security->xss_clean($this->input->post('selected_table_available'));
		$this->form_validation->set_rules('selected_sheet', 'Sheet', 'trim|required');
		$this->form_validation->set_rules('selected_table_available', 'Sheet', 'trim|required');
		if ($this->form_validation->run()==FALSE) 
		{
			$error = 'Error en validación';
            echo json_encode($error);
		}
		else
		{
			$id_load_file = $this->session->userdata('id_load_file');
			$columns = $this->Excel_model->list_fields($selected_table_available);
			
			$columns_to_asign = array();
			foreach ($columns as $column)
			{
					$columns_to_asign[$column] = '';
			}
			$sheet_table = array(
				'tmp_table' => $selected_table_available,
				'relation' => json_encode($columns_to_asign)
			);
			$res = $this->Load_file_model->update_sheet_table_by_id_load($id_load_file, $selected_sheet, $sheet_table);
			
			$sheets = $this->Load_file_model->get_sheet_not_asigned($id_load_file);
		
			$success = array(
				'success' => 'success',
				'sheets' => $sheets
			);
			echo json_encode($success);	
		}
	}
	public function reverse_associate_sheet_table()
	{
		$selected_sheet = $this->security->xss_clean($this->input->post('selected_sheet'));
		$selected_table_available = $this->security->xss_clean($this->input->post('selected_table_available'));
		$this->form_validation->set_rules('selected_sheet', 'Sheet', 'trim|required');
		$this->form_validation->set_rules('selected_table_available', 'Sheet', 'trim|required');
		if ($this->form_validation->run()==FALSE) 
		{
			$error = 'Error en validación';
            echo json_encode($error);
		}
		else
		{
			$id_load_file = $this->session->userdata('id_load_file');
			$sheet_table = array(
				'tmp_table' => '-'
			);
			$res = $this->Load_file_model->update_sheet_table_by_id_load($id_load_file, $selected_sheet, $sheet_table);
			
			$sheets = $this->Load_file_model->get_sheet_not_asigned($id_load_file);
		
			$success = array(
				'success' => 'success',
				'sheets' => $sheets
			);
			echo json_encode($success);	
		}
	}
	public function get_associate_sheet_table()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		$associate_sheet_table = $this->Load_file_model->get_sheet_table_asigned($id_load_file);
	
		$success = array(
			'success' => 'success',
			'associate_sheet_table' => $associate_sheet_table
		);
		echo json_encode($success);
	}
	public function step3()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		$data['algo'] = '';
		$this->load->view('templates/header');
		$this->load->view('spreadsheet/step-three', $data);
		$this->load->view('templates/footer');
	}
}
