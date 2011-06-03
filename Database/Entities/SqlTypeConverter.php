<?php

class SqlTypeConverter extends Singleton
{
	/**
	 * array of type_name=>callback
	 */
	private $conversions = array();
	
	protected function __construct()
	{
		parent::__construct();
		$this->addConversion
	}
	
	public function addConversion($type, $converter)
	{
		$this->conversions[$type] = $converter;
	}
	
	public function toSql($value)
	{
		if(is_scalar($value))
			return $value;
		if($value instanceof ISqlValue)
			return $value->toSql();
		//if($value instanceof DateTime)
		//	return ...
		return $value;
	}
	
	public function transient_convert($value)
	{
		return $value;
	}
}
