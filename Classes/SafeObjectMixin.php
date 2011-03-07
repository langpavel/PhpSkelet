<?php

/**
 * This file is part of the Morpheus Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the Morpheus/LGPL license.
 */

require_once('../Morpheus.php');

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
	 * For magic method __get - forbidden and throws InvalidPropertyAccessException
     * @param string $name
	 * @throws InvalidPropertyAccessException
     */
	public static function objectGet($instance, $name)
	{
		throw new InvalidPropertyAccessException('Invalid property access (get '.get_class($instance).'::'.$name.')');
	}

}
