<?php
	function get_data_column($row, $columns, $last_column_letter)
	{	
		$new_array = array();
		foreach($row as $key => $value):
			if (in_array($key, $columns)) {
				$new_array[$key] = $value;
			}
			if($key > $last_column_letter){
				next($row);
			}
		endforeach;
		return $new_array;
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
