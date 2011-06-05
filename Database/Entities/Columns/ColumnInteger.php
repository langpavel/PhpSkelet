<?php

class ColumnInteger extends TableColumn
{
	private $display_format;
	
	public function __construct($name, array $params = null)
	{
		if($params !== null)
		{
			$this->consume_param($params, 
				'display_format', array($this, 'setDisplayFormat'));
		}
		
		parent::__construct($name, $params);
	}
	
	/**
	 * Get value of display_format
	 * @return mixed display_format
	 */
	public function getDisplayFormat() { return $this->display_format; }

	/**
	 * Set value of display_format - as of sprintf()
	 * @param mixed $value display_format
	 * @return ColumnInteger self
	 */
	public function setDisplayFormat($value) { $this->display_format = $value; return $this; }

	public function correctValue(&$value, &$message = null, $strict = false)
	{
		if(is_int($value))
			return true;

		$message='Value is not integer';

		if($strict)
			return false;
		
		if(is_numeric($value))
		{
			$value = (int)$value;
			return true;
		}

		$message='Value is not a valid integer';
		return false;
	}

}