<?php

class PrimaryKeyValueProxy extends SafeObject implements ISqlValue
{
	private $value;

	public function __construct($value = null)
	{
		$this->value = $value;
	}

	public function toSql()
	{
		return $this->value;
	}

	function __toString()
	{
		return (string) $this->value;
	}
}
