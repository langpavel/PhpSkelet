<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/Object.php';

/**
 * Safe object class - deny setting and reading of undeclared properties
 * @author Pavel Lang
 */
abstract class SafeObject extends Object
{

//	implicit behavior |cs> implicitni chovani 
//	public function __isset( $name )
//	{
//		return isset($this->name);
//	}

	/**
	 * Magic method __unset is forbidden
	 * @param string $name
	 * @throws InvalidPropertyAccessException
	 * @see SafeObjectMixin::objectUnset
	 */
	public function __unset($name)
	{
		SafeObjectMixin::objectUnset($this, $name);
    }

    /**
	 * Magic method __set is forbidden
     * @param string $name
     * @param mixed $value
	 * @throws InvalidPropertyAccessException
	 * @see SafeObjectMixin::objectSet
     */
	public function __set($name, $value)
	{
		SafeObjectMixin::objectSet($this, $name, $value);
	}

    /**
	 * Magic method __get is forbidden
     * @param string $name
	 * @throws InvalidPropertyAccessException
	 * @see SafeObjectMixin::objectGet
     */
	public function __get($name)
	{
		/*return*/ SafeObjectMixin::objectGet($this, $name);
	}

}