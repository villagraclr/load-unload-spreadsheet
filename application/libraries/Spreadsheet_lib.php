<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Spreadsheet_lib {
	
	var $full_path, $file_type, $reader, $sheet_names, $columns, $topten_elements;
	private $ci;
	
	function __construct() {
		require_once( 'MyReadFilter.php' );
	}
	public function init($full_path = '')
	{
		$this->full_path = $full_path;
		$arr_file = explode('.', $full_path);
		$ext = ucwords(strtolower(end($arr_file)));
		$this->file_type = $ext;
		$this->reader = IOFactory::createReader($ext);
		$this->ci =& get_instance();

		$this->ci->load->model('Load_file_model');
	}
	public function get_list_worksheet_names()
	{
		$worksheet_names = $this->reader->listWorksheetNames($this->full_path);
		return $worksheet_names;
	}
	public function get_list_cols_name()
	{		
		$cols_name = array();
		$worksheet_data = $this->reader->listWorksheetInfo($this->full_path);
		foreach ($worksheet_data as $worksheet)
		{
			$last_column_letter = $worksheet['lastColumnLetter'];
			$worksheet_name = $worksheet['worksheetName'];
			$filterSubset = new MyReadFilter(1, 1, range('A', $last_column_letter));
			$this->reader->setLoadSheetsOnly($worksheet_name);
			$this->reader->setReadFilter($filterSubset);
			$spreadsheet = $this->reader->load($this->full_path);
			
			$worksheet = $spreadsheet->getActiveSheet();
			$highest_column = $worksheet->getHighestColumn();
			// Increment the highest column letter
			$highest_column++;
			$row = 1;
			$col_element = array();
			for ($col = 'A'; $col != $highest_column; ++$col) {
				$col_element[$col] = $worksheet->getCell($col . $row)->getValue();
			}
			$cols_name[$worksheet_name] = $col_element;
		}
		return $cols_name;
	}
	public function list_worksheet_info($sheet)
	{		
		$worksheet_info = array();
		$worksheet_data = $this->reader->listWorksheetInfo($this->full_path);
		foreach ($worksheet_data as $worksheet)
		{
			if($sheet == $worksheet['worksheetName'] )
			{
				$last_column_letter = $worksheet['lastColumnLetter'];
				$total_row = $worksheet['totalRows'];
				array_push($worksheet_info,array(
					'last_column_letter' => $last_column_letter,
					'total_row' => $total_row
				));
				return $worksheet_info;
			}
		}
		return null;
	}
	public function get_top_elements($id_load)
	{	
		$elements = array();
		$worksheet_data = $this->reader->listWorksheetInfo($this->full_path);
		foreach ($worksheet_data as $worksheet)
		{
			$last_column_letter = $worksheet['lastColumnLetter'];
			$worksheet_name = $worksheet['worksheetName'];
			$total_row = $worksheet['totalRows'];
			
			$sheet_table = array(
					'last_column_letter' => $last_column_letter,
					'total_row' => $total_row
			);
			
			$rs = $this->ci->Load_file_model->update_sheet_table_complement($id_load, $worksheet_name, $sheet_table);
			
			$limit_preview = MAX_ROW_LIMIT_PREVIEW+1;
			$filterSubset = new MyReadFilter(1, $limit_preview, range('A', $last_column_letter));
			$this->reader->setLoadSheetsOnly($worksheet_name);
			$this->reader->setReadFilter($filterSubset);
			$spreadsheet = $this->reader->load($this->full_path);
			
			$worksheet_full = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

			$full_elements = array();
			foreach ($worksheet_full as $key => $value):
				if(valid_row($value))
				{
					array_push($full_elements, $value);
				}
			endforeach;
			$elements[$worksheet_name] =  $full_elements;
		}
		return $elements;
	}
	public function get_fulldata_by_sheet($id, $sheet, $last_column_letter, $start_row, $total_row, $columns, $tmp_table)
	{
		$elements = array();
		$first_column_letter = 'A';
		if(reset( $columns ) > 'A')
		{
			$first_column_letter = reset( $columns );
		}
		if(end( $columns ) < $last_column_letter)
		{
			$last_column_letter = end( $columns );
		}
		$end_row = $total_row;
		$filterSubset = new MyReadFilter($start_row, $end_row , range($first_column_letter, $last_column_letter));
		$this->reader->setLoadSheetsOnly($sheet);
		$this->reader->setReadFilter($filterSubset);
		$spreadsheet = $this->reader->load($this->full_path);
		$worksheet_full = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

		$full_elements = array();
		foreach ($worksheet_full as $key => $value):
			$new_row = get_data_to_load_in_database($value, $columns, $last_column_letter);
			if(!empty($new_row))
			{
				array_push($full_elements, $new_row);
			}
		endforeach;
		if(!empty($full_elements))
		{
			//
			$round = round($total_row/16000,0,PHP_ROUND_HALF_DOWN);
			$mod = $total_row % 16000;
			
			if($mod > 0 )
			{
				$round++;
			}
			if(count($columns) > 10 && $total_row > 16000 && $round > 1)
			{
				$data = array_chunk($full_elements, $round, true);
				foreach ( $data as $row ):
					$rs = $this->ci->Load_file_model->insert_data_in_tmp_table($tmp_table, $row);
				endforeach;
			}
			else
			{
				$rs = $this->ci->Load_file_model->insert_data_in_tmp_table($tmp_table, $full_elements);
			}
			
			$sheet_table = array(
					'processed_records' => $total_row
			);
			
			$rs = $this->ci->Load_file_model->update_sheet_table($id, $sheet_table);
		}
	}
	public function get_top_elements_by_worksheet($worksheet_name)
	{	
		$elements = array();
		$worksheet_data = $this->reader->listWorksheetInfo($this->full_path);
		foreach ($worksheet_data as $worksheet)
		{
			if($worksheet['worksheetName'] === $worksheet_name)
			{
				$last_column_letter = $worksheet['lastColumnLetter'];
				$worksheet_name = $worksheet['worksheetName'];
				$limit_preview = MAX_ROW_LIMIT_PREVIEW+1;
				$filterSubset = new MyReadFilter(1, $limit_preview, range('A', $last_column_letter));
				$this->reader->setLoadSheetsOnly($worksheet_name);
				$this->reader->setReadFilter($filterSubset);
				//$reader->setReadDataOnly(true);
				$spreadsheet = $this->reader->load($this->full_path);
				
				$worksheet_full = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

				$row_for_delete = array();
				foreach ($worksheet_full as $key => $value):
					if(!valid_row($value))
					{
						array_push($row_for_delete, $key);
					}
				endforeach;
				foreach ($row_for_delete as $key):
					unset($worksheet_full[$key]);
				endforeach;
				$worksheet_full = array_values($worksheet_full);
				$elements[$worksheet_name] =  $worksheet_full;
			}
		}
		return $elements;
	}
	
}
