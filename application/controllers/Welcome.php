<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Welcome extends CI_Controller {

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
	public function index()
	{
		$this->output->enable_profiler(TRUE);
		$this->generate_file();
		$this->download();
		echo "archivo cargado";
	}
	public function generate_file(){
		$spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', 'Hello World !');

                $writer = new Xlsx($spreadsheet);

                $filename = '/tmp/name-of-the-generated-file.xlsx';

                $writer->save($filename);
	}
	public function download()
	{
		$this->output->enable_profiler(TRUE);
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Hello World !');
		
		$writer = new Xlsx($spreadsheet);
		$path = "/tmp/";
		$filename = 'name-of-the-generated-file';
		$path = "/tmp/".$filename.".xlsx";

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename .'.xlsx"');
		 header('Content-Length: ' . filesize($path));

		header('Cache-Control: max-age=0');
		
		$writer->save('php://output');

	}
}
