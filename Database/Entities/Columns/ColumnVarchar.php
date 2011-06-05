<?php

class ColumnVarchar extends ColumnText
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
	
	public function correctValue(&$value, &$message = null, $strict = false)
	{
		$result = parent::correctValue($value, $message, $strict);
		if($result !== true)
			return $result;
			
		$val = (string) $value;
		if($this->length !== null && strlen($val) > $this->length)
		{
			$value = substr($value, 0, $this->length);
			$message = "String is too long";
			if($strict)
				return false;
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