<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Spreadsheet_lib {

	function __construct() {
	}
    	public function get_document($full_path = '')
    	{
		$document = IOFactory::load($full_path);
		//$inputFileType = end(explode('.', $full_path));
		//$reader = IOFactory::createReader($inputFileType);
                //$spreadsheet = $reader->load($inputFileName);
		return $document;
    	}
}
