<?php

class ColumnBool extends ColumnInteger
{
	public function __construct($name, array $params = null)
	{
		parent::__construct($name, $params);
	}
	
	public function validateValue(&$value, $strict=true)
	{
		if(is_bool($value))
			return true;
		if($strict)
			return "Value must be boolean";
		$value = (bool) $value;
		return true;
	}

}