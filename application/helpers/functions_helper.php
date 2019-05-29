<?php
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
