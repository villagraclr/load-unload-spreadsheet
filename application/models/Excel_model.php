<?php

class Excel_model extends CI_Model
{
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
	function list_tables(){
		$result = $this->db->list_tables();
		$tables = array();
		foreach ($result as $table)
		{
			if (strpos($table, 'tmp_') === 0 ) {
				array_push($tables, $table);
			}
		}
		return $tables;
	}
	function list_fields($table_name){
		$fields = $this->db->list_fields($table_name);
		return $fields;
	}
	
}
?>