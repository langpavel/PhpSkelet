<?php

class ColumnId extends TableColumn
{
	public static $default_display_name = 'ID';
	public static $default_display_hint = 'Primary Key';

	public function __construct($name = 'id')
	{
		parent::__construct($name, array(
			'db_column'=>$name,
			'display_name'=>self::$default_display_name,
			'display_hint'=>self::$default_display_hint,
			'delay_load'=>false,
			'nullable'=>false,
			'required'=>false,
			'default_value'=>new PrimaryKeyValueProxy()
		));
	}

	public function correctValue(&$value, &$message = null, $strict = false)
	{
		if($value instanceof PrimaryKeyValueProxy)
			return true;
		return parent::correctValue($value, $message, $strict);
	}

	public function fromDbFormat($value)
	{
		return new PrimaryKeyValueProxy($value);
	}

	public function toDbFormat($value)
	{
		if($value instanceof PrimaryKeyValueProxy)
			return $value->toSql();
	}

}