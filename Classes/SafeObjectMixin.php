<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/../PhpSkelet.php';

/**
 * Safe object class - deny setting and reading of undeclared properties
 * @author langpavel
 */
final class SafeObjectMixin
{

	/**
	 * For magic method __unset - forbidden and throws InvalidPropertyAccessException
	 * @param string $name
	 * @throws InvalidPropertyAccessException
	 */
	public static function objectUnset($instance, $name)
	{
		throw new InvalidPropertyAccessException('Cannot unset property of SafeObject ('.get_class($instance).'::'.$name.')');
    }

    /**
	 * For magic method __set - forbidden and throws InvalidPropertyAccessException
     * @param string $name
     * @param mixed $value
	 * @throws InvalidPropertyAccessException
     */
	public static function objectSet($instance, $name, $value)
	{
		throw new InvalidPropertyAccessException('Invalid property access (set '.get_class($instance).'::'.$name.')');
	}

    /**
	 * For magic method __get, call method "get$name" if exists 
     * @param string $name
	 * @throws InvalidPropertyAccessException
     */
	public static function objectGet($instance, $name)
	{
		// This is here because of template rendering
		$methodname = 'get'.$name;
		if(method_exists($instance, $methodname))
			return $instance->$methodname();
		throw new InvalidPropertyAccessException('Invalid property access (get '.get_class($instance).'::'.$name.')');
	}

}
