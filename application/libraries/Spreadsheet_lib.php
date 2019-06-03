<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Spreadsheet_lib {
	
	var $full_path, $file_type, $reader, $sheet_names, $columns, $topten_elements;
	
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
	public function get_topfive_elements()
	{	
		$elements = array();
		$worksheet_data = $this->reader->listWorksheetInfo($this->full_path);
		foreach ($worksheet_data as $worksheet)
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
		return $elements;
	}
	
	public function get_topfive_elements_by_worksheet($worksheet_name, $last_column_letter)
	{	
		$elements = array();
		$filterSubset = new MyReadFilter(2, 7, range('A', $last_column_letter));
		$this->reader->setLoadSheetsOnly($worksheet_name);
		$this->reader->setReadFilter($filterSubset);
		$spreadsheet = $this->reader->load($this->full_path);
		
		//$worksheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
		
		
		$worksheet_full = $spreadsheet->getActiveSheet();
		
		$highest_row = $worksheet_full->getHighestRow(); // e.g. 10
		$highest_column = $worksheet_full->getHighestColumn();
		// Increment the highest column letter
		$highest_column++;
		
		if($highest_row > 6)
		{
			$highest_row = 6;
		}
		$rows_elements = array();
		for ($row = 2; $row <= $highest_row; ++$row) {
			$cols_element = array();
			for ($col = 'A'; $col != $highest_column; ++$col) {
				$cols_element[$col] = $worksheet_full->getCell($col . $row)->getValue();
			}
			$rows_elements[] = $cols_element;
		}
		$elements[$worksheet_name] = $rows_elements;
		
		return $elements;
	}
}
