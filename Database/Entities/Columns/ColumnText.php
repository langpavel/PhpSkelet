<?php

class ColumnText extends TableColumn
{
	public function correctValue(&$value, &$message = null, $strict = false)
	{
		if(is_string($value))
			return true;
		
		$message = "Value is not string";
		
		if($strict)
			return false;
	}
}