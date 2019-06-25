<?php
	function get_data_to_load_in_database($row, $columns, $last_column_letter)
	{	
		$new_array = array();
		$count_no_empty = 0;
		foreach($row as $key => $value):
			if (in_array($key, $columns))
			{
				$index = array_keys($columns, $key);
				$new_array[$index[0]] = $value;
			}
			if(!empty($value)) $count_no_empty++;
			if($key > $last_column_letter)
			{
				if($count_no_empty > 0) return $new_array;
			}
		endforeach;
		
		if($count_no_empty > 0) return $new_array;
		else return;
	}
	function get_data_column($row, $columns, $last_column_letter)
	{	
		$new_array = array();
		$count_no_empty = 0;
		
		foreach($row as $key => $value):
			if (in_array($key, $columns)) $new_array[$key] = $value;
			if(!empty($value)) $count_no_empty++;
			if($key > $last_column_letter)
			{
				if($count_no_empty > 0) return $new_array;
			}
		endforeach;
		
		if($count_no_empty > 0) return $new_array;
		else return;
	}
	function valid_row($row)
	{	
		foreach($row as $element):
			if(!empty($element))
			{
				return true;
			}
		endforeach;
		return false;
	}
?>
