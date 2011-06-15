<?php

/**
 * Use DbNull::$value or DbNull::getInstance() to get value
 *
 * This class represents database null value that behave as true value for isset.
 * WARNING
 * UNFORTUNATELY cast to bool in if() expression, is_null(), RETURNS TRUE;
 * empty() RETURNS FALSE
 * There is function isnull, that returns true for instace of DbNull.
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 */
final class DbNull implements ISingleton, ISqlValue
{
	/**
	 * @var DbNull
	 */
	public static $value;

	/**
	 * private constructor blocks
	 */
	private function __construct()
	{
	}

	/**
	 * @return DbNull
	 */
	public static function getInstance()
	{
		if(DbNull::$value === null)
			DbNull::$value = new DbNull();
		return DbNull::$value;
	}

	/**
	 * Return SQL safe representation - simple string NULL (without quotes)
	 * @return string NULL
	 */
	public function toSql()
	{
		return 'NULL';
	}

} DbNull::getInstance();
