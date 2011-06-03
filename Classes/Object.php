<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/../PhpSkelet.php';

/**
 * Base class for all
 * @author langpavel at gmail dot com
 */
class Object
{
	/**
	 * runtime-wide counter of objects
	 */
	private static $goid = 0;

	/**
	 * current object id
	 */
	private $oid = 0;
	
	/**
	 * Do not call. Static initialization. 
	 * It's called only once after class definition
	 */
	static function __static_construct()
	{
		self::$goid = 0;
	}
	
	/**
	 * Default constructor
	 */
	protected function __construct() 
	{
		$this->getObjectId();
	}

	/**
	 * Default destructor
	 */
	public function __destruct() 
	{
	}

	/**
	 * Default implementation of __toString method - returns "(object {classname}[{oid}])"
	 */
	public function __toString()
	{
		$oid = $this->getObjectId();
		return '(object '.get_class($this)."[$oid])";
	}
	
	/**
	 * Get class name of current instance 
	 */
	public function getClassName()
	{
		return get_class($this);
	}
	
	/**
	 * default implementation of clone
	 */
	protected function __clone()
	{
		$this->oid = self::getNewObjectId();
	}
	
	/**
	 * Get current script unique object ID, usefull for hashes
	 */
	public function getObjectId()
	{
		return ($this->oid !== 0) ? $this->oid : ($this->oid = self::getNewObjectId());
	}
	
	/**
	 * get last used object identifier
	 * @return int
	 */
	public static function getCurrentObjectId()
	{
		return self::$goid;
	}

	/**
	 * get new object identifier
	 * @return int
	 */
	public static function getNewObjectId()
	{
		return ++self::$goid;
	}
} Object::__static_construct();
