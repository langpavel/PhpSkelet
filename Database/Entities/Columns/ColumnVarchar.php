<?php

class ColumnVarchar extends TableColumn
{
	private $length;
	
	public function __construct($name, $params = null)
	{
		if($params !== null)
		{
			if(is_int($params))
				$params = array('length'=>$params);
		}
		
		$this->consume_param($params, 
			'length', array($this, 'setLength'));
		
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

	public function validateValue(&$value, $strict=true)
	{
		$val = (string) $value;
		if($this->length !== null && strlen($val) > $this->length)
		{
			if($strict)
				return "String is too long";
			else
				$value = substr($value, 0, $this->length);
		}
		return true;
	}

	/**
	 * Get value of length
	 * @return mixed length
	 */
	public function getLength() { return $this->length; }

	/**
	 * Set value of length
	 * @param mixed $value length
	 * @return ColumnVarchar self
	 */
	public function setLength($value) { $this->length = $value; return $this; }

}