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
	private static $goid = 0;
	private $oid = 0;
	
	/**
	 * Empty constructor
	 */
	protected function __construct() 
	{
		$this->getObjectId();
	}

	/**
	 * Enpty destructor
	 */
	public function __destruct() { }

	/**
	 * Default implementation of __toString method - returns "(object {classname})"
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
	
	public static function getCurrentObjectId()
	{
		return self::$goid;
	}

	public static function getNewObjectId()
	{
		return ++self::$goid;
	}
}
