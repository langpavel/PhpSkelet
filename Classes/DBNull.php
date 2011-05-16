<?php

// /**
//  * This class represents null value that behave as true value for isset
//  * WARNING
//  * UNFORTUNATELY cast to bool in if() expression, is_null(), RETURNS TRUE; 
//  * empty() RETURNS FALSE
//  */
// final class DBNull // implements HtmlValue, SQLValue
// {
// 	public static $value;
// 	public static $text = '';
// 	public static $html_text = '<span class="DBNull">null</span>';
// 	public static $sql_text = 'NULL';
// 	//public static $html_text = '<span class="DBNull" style="color:#00f">null</span>';
// 	
// 	private function __construct() { }
// 	
// 	public static function value()
// 	{
// 		if(DBNull::$value === null)
// 			DBNull::$value = new DBNull();
// 		return DBNull::$value;
// 	}
// 	
// 	public function __toString() { return DBNull::$text; }
// 	public function toHtml() { return DBNull::$html_text; }
// 	public function toSQL() { return DBNull::$sql_text; }
// 	
// 	function __get($name) { throw new Exception('Cannot get property of DBNull'); }
// 	function __set($name, $value) { throw new Exception('Cannot set property on DBNull'); }
// 	function __unset($name) { throw new Exception('Cannot unset property on DBNull'); }
// 	function __isset($name) { return false; }
// }
// 
// $GLOBALS['DBNull'] = DBNull::$value = DBNull::value();
// 
// function DBNull() { return DBNull::$value; }
