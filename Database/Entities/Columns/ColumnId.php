<?php

class ColumnId extends ColumnInteger
{
	public static $default_display_name = 'ID';
	public static $default_display_hint = 'Primary Key';
	
	public function __construct()
	{
		parent::__construct('ID', array(
			'db_column'=>'id',
			'display_name'=>self::$default_display_name,
			'display_hint'=>self::$default_display_hint,
			'delay_load'=>false,
			'nullable'=>false,
			'required'=>false,
			'default_value'=>new PrimaryKeyValueProxy()
		));
	}
	
	public function validate(&$value)
	{
		if($value instanceof PrimaryKeyValueProxy)
			return true;
		return parent::validate($value);
	}

	public function toDbFormat($value)
	{
		if($value instanceof PrimaryKeyValueProxy)
			return $value->toSql();
	}

}