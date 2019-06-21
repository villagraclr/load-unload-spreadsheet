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
		redirect('/', 'location', 301);
		
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
					$sheets = $obj_spreadsheet->get_list_worksheet_names();
					
					$sheet_tables = array();
					foreach ($sheets as $worksheet)
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
					$this->session->set_userdata("worksheet_names", $sheets);
					$this->session->set_userdata("tables", $tables);
					
					$success = array(
						'success' => 'success',
						'sheets' => $sheets,
						'tables' => $tables
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
    }
	public function load_preview_data()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$full_path = $this->Load_file_model->get_full_path($id_load_file);
			$obj_spreadsheet = new Spreadsheet_lib();
			$obj_spreadsheet->init($full_path);
			//$cols_name = $obj_spreadsheet->get_list_cols_name();
			$top_elements = $obj_spreadsheet->get_top_elements($id_load_file);
			$this->session->set_userdata("top_elements", $top_elements);
			$success = array(
				'success' => 'success',
				'top_elements' => $top_elements
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
	public function step2()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$this->load->view('templates/header');
			$this->load->view('spreadsheet/step-two');
			$this->load->view('templates/footer');
		}
		else
		{
			redirect('/', 'location', 301);
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
	public function get_worksheets()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$sheets = $this->Load_file_model->get_sheet_not_asigned($id_load_file);
			$success = array(
				'success' => 'success',
				'sheets' => $sheets
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
			if(!empty($id_load_file) && $id_load_file > 0)
			{
				$tables_asigned = $this->Load_file_model->get_table_not_asigned($id_load_file);
				$tables = $this->session->userdata('tables');
				
				foreach($tables as $item):
					if (!in_array($item, $tables_asigned))
					{
						array_push($tables_available,$item);
					}
				endforeach;
				
				$success = array(
					'success' => 'success',
					'tables_available' => $tables_available
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
			if(!empty($id_load_file) && $id_load_file > 0)
			{
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
			else
			{
				$this->session->unset_userdata("id_load_file");
				$error = 'No existe identificador de carga';
				echo json_encode($error);
			}
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
			if(!empty($id_load_file) && $id_load_file > 0)
			{
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
			else
			{
				$this->session->unset_userdata("id_load_file");
				$error = 'No existe identificador de carga';
				echo json_encode($error);
			}
		}
	}
	public function get_associate_sheet_table()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$associate_sheet_table = $this->Load_file_model->get_sheet_table_asigned($id_load_file);
		
			$success = array(
				'success' => 'success',
				'associate_sheet_table' => $associate_sheet_table
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
	public function step3()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$this->load->view('templates/header');
			$this->load->view('spreadsheet/step-three');
			$this->load->view('templates/footer');
		}
		else
		{
			redirect('/', 'location', 301);
		}
	}
	function get_associate_columns()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$top_elements = $this->session->userdata('top_elements');
			
			$sheet_columns = array();
			$row_one = array();
			foreach($top_elements as $key => $elements):
				if($this->Load_file_model->is_sheet_table_asigned($id_load_file, $key))
				{
					$column = $key;
					$row_one = $elements[0];
					$sheet_columns[$column] = $row_one;
				}
			endforeach;
			$this->session->set_userdata("sheet_columns", $sheet_columns);
			$sheet_table_asigned = $this->Load_file_model->get_sheet_table_asigned($id_load_file);
			$success = array(
				'success' => 'success',
				'sheet_table_asigned' => $sheet_table_asigned,
				'sheet_columns' => $sheet_columns
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
	public function set_associate_columns()
	{		
		$selected_id = $this->security->xss_clean($this->input->post('selected_id'));
		$selected_key = $this->security->xss_clean($this->input->post('selected_key'));
		$selected_value = $this->security->xss_clean($this->input->post('selected_value'));
		
		$this->form_validation->set_rules('selected_id', 'Id', 'trim|required');
		$this->form_validation->set_rules('selected_key', 'Key', 'trim|required');
		$this->form_validation->set_rules('selected_value', 'Value', 'trim|required');
		
		if ($this->form_validation->run()==FALSE)
		{
			$error = 'Error en validación';
            echo json_encode($error);
		}
		else
		{
			$id_load_file = $this->session->userdata('id_load_file');
			if(!empty($id_load_file) && $id_load_file > 0)
			{
				$res = $this->Load_file_model->update_relation_column_by_id($selected_id, $selected_key, $selected_value);
				$sheet_columns = $this->session->userdata("sheet_columns");
				$sheet_table_asigned = $this->Load_file_model->get_sheet_table_asigned($id_load_file);
				$success = array(
					'success' => 'success',
					'sheet_table_asigned' => $sheet_table_asigned,
					'sheet_columns' => $sheet_columns
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
	public function reverse_associate_columns()
	{		
		$selected_id = $this->security->xss_clean($this->input->post('selected_id'));
		$selected_sheet = $this->security->xss_clean($this->input->post('selected_sheet'));
		$selected_table_available = $this->security->xss_clean($this->input->post('selected_table_available'));
		$selected_key = $this->security->xss_clean($this->input->post('selected_key'));
		$selected_value = $this->security->xss_clean($this->input->post('selected_value'));
		
		$this->form_validation->set_rules('selected_id', 'Id', 'trim|required');
		$this->form_validation->set_rules('selected_sheet', 'Sheet', 'trim|required');
		$this->form_validation->set_rules('selected_table_available', 'Sheet', 'trim|required');
		$this->form_validation->set_rules('selected_key', 'Key', 'trim|required');
		$this->form_validation->set_rules('selected_value', 'Value', 'trim|required');
		
		if ($this->form_validation->run()==FALSE) 
		{
			$error = 'Error en validación';
            echo json_encode($error);
		}
		else
		{
			$id_load_file = $this->session->userdata('id_load_file');
			if(!empty($id_load_file) && $id_load_file > 0)
			{
				$res = $this->Load_file_model->update_relation_column_by_id($selected_id, $selected_key, '');
				
				$sheet_columns = $this->session->userdata("sheet_columns");
				$sheet_table_asigned = $this->Load_file_model->get_sheet_table_asigned($id_load_file);
				$success = array(
					'success' => 'success',
					'sheet_table_asigned' => $sheet_table_asigned,
					'sheet_columns' => $sheet_columns
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
	public function process()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$this->load->view('templates/header');
			$this->load->view('spreadsheet/process');
			$this->load->view('templates/footer');
		}
		else
		{
			redirect('/', 'location', 301);
		}
	}
	public function load_file_in_database()
	{
		$id_load_file = $this->session->userdata('id_load_file');
		if(!empty($id_load_file) && $id_load_file > 0)
		{
			$sheet_and_column = array();
			$sheet_table_asigned = $this->Load_file_model->get_final_relationship($id_load_file);
			$summary = array();
			if(!empty($sheet_table_asigned))
			{
				foreach($sheet_table_asigned as $item):
					$id = $item['id'];
					$id_load = $item['id_load'];
					$sheet = $item['sheet'];
					$last_column_letter = $item['last_column_letter'];
					$total_row = $item['total_row'];
					$processed_records = $item['processed_records'];					
					$tmp_table = $item['tmp_table'];
					$relation = json_decode($item['relation']);
					
					if(!empty($relation))
					{	
						$columns = array();
						foreach($relation as $key => $value) {
							if(!empty($value)){
								$columns[$key] = $value;
							}
						}
						if(!empty($columns))
						{
							asort($columns);
							array_push($sheet_and_column,(array(
								'id' => $id,
								'id_load' => $id_load,
								'sheet' => $sheet,
								'last_column_letter' => $last_column_letter,
								'total_row' => $total_row,
								'processed_records' => $processed_records,
								'columns' => $columns,
								'tmp_table' => $tmp_table
							)));							
						}
					}
				endforeach;
				if(!empty($sheet_and_column))
				{
					$full_path = $this->Load_file_model->get_full_path($id_load_file);
					$obj_spreadsheet = new Spreadsheet_lib();
					$obj_spreadsheet->init($full_path);
					
					foreach($sheet_and_column as $item):
						$id = $item['id'];
						$sheet = $item['sheet'];
						$last_column_letter = $item['last_column_letter'];
						$total_row = $item['total_row'];
						$processed_records = $item['processed_records'];
						$columns = $item['columns'];
						$tmp_table = $item['tmp_table'];
						
						$start_row = 2;
						if($processed_records == 0)
						{
							$this->Load_file_model->truncate_table($tmp_table);
							
						}
						else
						{
							$start_row = $processed_records;
						}
						
						if($total_row > MAX_ROW_LIMIT_LOAD)
						{
							$total_row = MAX_ROW_LIMIT_LOAD;
						}
						$processed_records = $total_row;
						$end_row = $total_row;
						$obj_spreadsheet->get_fulldata_by_sheet($id, $sheet, $last_column_letter, $start_row, $end_row, $columns, $tmp_table);
						
						array_push($summary, array(
							'sheet' => $sheet,
							'table' => $tmp_table,
							'records' => $total_row,
							'processed_records' => $processed_records
						));					
					endforeach;
				}
			}			
			$success = array(
				'success' => 'success',
				'summary' => $summary
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
