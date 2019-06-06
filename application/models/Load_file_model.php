<?php
class Load_file_model extends CI_Model
{
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
	function insert_file_uploaded($load_file)
	{
		$result = 0;
		$this->db->trans_begin();
		
		$id = $this->db->insert('load_file', $load_file);
		$id_load_file = $this->db->insert_id();
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = $id_load_file;
		}
		return $result;
	}
	function insert_sheet_table($sheet_tables)
	{
		$result = 0;
		$this->db->trans_begin();
		
		$this->db->insert_batch('sheet_table', $sheet_tables);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = 1;
		}
		return $result;
	}
	function insert_data_in_tmp_table($table, $data)
	{
		$result = 0;
		$this->db->trans_begin();
		
		$this->db->insert_batch($table, $data);
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = 1;
		}
		return $result;
	}
	function get_sheet_not_asigned($id_load){
		$sheets = array();
		$this->db->select('sheet');
		$this->db->where('id_load',$id_load);
		$this->db->where('tmp_table', '-');
		$this->db->order_by('id', 'DESC');
		$result = $this->db->get('sheet_table')->result_array();
		
		if(!empty($result)){
				foreach($result as $item):
					array_push($sheets,$item['sheet']);
				endforeach;
		}
		return $sheets;
	}
	function get_table_not_asigned($id_load){
		$tables = array();
		$this->db->select('tmp_table');
		$this->db->where('id_load',$id_load);
		$this->db->where('tmp_table !=', '-');
		$this->db->order_by('id', 'DESC');
		$result = $this->db->get('sheet_table')->result_array();
		
		if(!empty($result)){
				foreach($result as $item):
					array_push($tables,$item['tmp_table']);
				endforeach;
		}
		return $tables;
	}
	function get_sheet_table_asigned($id_load){
		$this->db->select('id,id_load,sheet,tmp_table,relation');
		$this->db->where('id_load',$id_load);
		$this->db->where('tmp_table !=', '-');
		$this->db->order_by('id', 'DESC');
		$result = $this->db->get('sheet_table')->result_array();
		$sheet_table_asigned = array();
		if(!empty($result)){
			foreach($result as $item):
				$tmp = array(
					'id' => $item['id'],
					'sheet' => $item['sheet'],
					'tmp_table' => $item['tmp_table'],
					'relation' => $item['relation']
				);
				array_push($sheet_table_asigned,$tmp);
			endforeach;
		}
		return $sheet_table_asigned;
	}
	function get_final_relationship($id_load){
		$this->db->select('id,id_load,sheet,last_column_letter,total_row,tmp_table,relation');
		$this->db->where('id_load',$id_load);
		$this->db->where('tmp_table !=', '-');
		$this->db->order_by('id', 'DESC');
		$result = $this->db->get('sheet_table')->result_array();
		return $result;
	}
	function get_sheet_table($id_load){
		$this->db->select('id,id_load,sheet,tmp_table,relation');
		$this->db->where('id_load',$id_load);
		$this->db->order_by('id', 'DESC');
		$result = $this->db->get('sheet_table')->result_array();
		return $result;
	}
	function update_sheet_table($id, $sheet_table)
	{
		$result = 0;
		$this->db->trans_begin();
		
		$this->db->where('id', $id);
		$this->db->update('sheet_table', $sheet_table);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = 1;
		}
		return $result;
	}
	function update_sheet_table_by_id_load($id_load, $selected_sheet, $sheet_table)
	{
		$result = 0;
		$this->db->trans_begin();
		
		$this->db->where('id_load', $id_load);
		$this->db->where('sheet', $selected_sheet);
		$this->db->update('sheet_table', $sheet_table);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = 1;
		}
		return $result;
	}
	function update_sheet_table_complement($id_load, $sheet, $sheet_table)
	{
		$result = 0;
		$this->db->trans_begin();
		
		$this->db->where('id_load', $id_load);
		$this->db->where('sheet', $sheet);
		$this->db->update('sheet_table', $sheet_table);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = 1;
		}
		return $result;
	}
	function get_full_path($id)
	{
		$path = '';
		$this->db->select('path');
		$this->db->where('id',$id);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$result = $this->db->get('load_file')->result_array();
		
		if(!empty($result)){
				foreach($result as $item):
					$path = $item['path'];
				endforeach;
		}
		return $path;
	}
	function update_relation_column_by_id($id, $key, $value)
	{
		$result = 0;
		$this->db->trans_begin();
		
		$this->db->set('relation', "JSON_REPLACE(relation, '$.$key', '$value')", FALSE);
		$this->db->where('id', $id);
		$this->db->update('sheet_table');
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = 1;
		}
		return $result;
	}
	function is_sheet_table_asigned($id_load, $sheet){
		$this->db->select('id');
		$this->db->where('id_load',$id_load);
		$this->db->where('sheet', $sheet);
		$this->db->where('tmp_table !=', '-');
		$this->db->order_by('id', 'DESC');
		$cantidad = $this->db->get('sheet_table')->num_rows();
		if($cantidad > 0) return TRUE;
		else return FALSE;
	}
}
?>