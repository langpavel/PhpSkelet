<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/../Classes/SafeObject.php';

/**
 * Singleton abstract base class implementation
 * using late static binding introduced in PHP 5.3.0
 * 
 * @abstract PHP is dynamic language and this is possible implementation.
 * Singleton class provides static method getInstance() that 
 * returns always only one same instance of called class
 *
 * @author Pavel Lang
 */
abstract class Singleton extends SafeObject
{
	/**
	 * private static array of all created singleton instances
	 * @var array of Singleton derived classes
	 */
	private static $instances = array();

	/**
	 * Magic method __clone() is FORBIDDEN, singleton should not be cloned.
	 */
	protected final function __clone()
	{
		// late static binding as of PHP 5.3.0
		$class = get_called_class();
		throw new RuntimeException("Cannot clone the singleton class $class");
	}

	/**
	 * cleanup...
	 */
	function __destruct()
	{
		$class = get_called_class();
		unset(self::$instances[$class]);
	}

	/**
	 * Get singleton instance
	 */
	public static function getInstance()
	{
		// late static binding as of PHP 5.3.0
		$class = get_called_class();
		return (isset(self::$instances[$class])) ? self::$instances[$class] : self::createNewInstance($class);
	}

//	Do not work, static and instance methods cannot share same name
//	If this behavior should be requested, the naming convention should be created 
//	/**
//	 * Emulate static call method on Singleton
//	 * as call to instance method on actual singleton instance
//	 */
//	public static function __callStatic($name, $arguments)
//	{
//		$callback = array(self::getInstance(), strtolower($name));
//		if(!function_exists($callback))
//			throw new PhpSkeletException("Call to non existing singleton method $name");
//		return call_user_func_array($callback, $arguments);
//    }

	/**
	 * For descendants of concrete singleton class replacing its functionality
	 *
	 * @param string $class class name or null for current
	 * @param string $parentClass substituted parent class, array for many replaces or null for ommit this functionality
	 * @throws InvalidProgramException
	 */
	protected static function thisIsInstance($parentClass, $selfClass = null)
	{
		if($selfClass === null)
			$selfClass = get_called_class();
		return (isset(self::$instances[$selfClass])) ? self::$instances[$selfClass] : self::createNewInstance($selfClass, $parentClass);
	}

	private static function setReplacedClass($instance, $replacedParentClass)
	{
		if(!($instance instanceof $replacedParentClass))
			throw new InvalidProgramException("$class is not instance of $replacedParentClass");
		self::$instances[$replacedParentClass] = $instance;
	}

	private static function setReplacedClasses($instance, $replacedParentClasses)
	{
		if(is_array($replacedParentClasses))
		{
			foreach($replacedParentClasses as $replacedParentClass)
				self::setReplacedClass($instance, $replacedParentClass);
		}
		else
		{
			self::setReplacedClass($instance, $replacedParentClasses);
		}

	}

	private static function createNewInstance($class = null, $replacedParentClass = null)
	{
		if($class === null)
			$class = get_called_class();

		self::$instances[$class] = $instance = new $class(); // or new static();

		if($replacedParentClass != null)
			self::setReplacedClasses($instance, $replacedParentClass);

		return $instance;
	}

	/**
	 * Get names of all created singleton instances
	 * Enter description here ...
	 */
	public static function getInstanceNames()
	{
		return array_keys(self::$instances);
	}
}

