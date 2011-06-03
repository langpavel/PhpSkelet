<?php

class PrimaryKeyValueProxy extends SafeObject implements ISqlValue
{
	private $value;
	
	public function __construct($entity = null, $value = null)
	{
		$this->value = $value;
	}
	
	public function toSql()
	{
		return $this->value;
	}
}
