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
	 * Empty constructor
	 */
	public function __construct() { }

	/**
	 * Enpty destructor
	 */
	public function __destruct() { }

	/**
	 * Default implementation of __toString method - returns "(object {classname})"
	 */
	public function __toString()
	{
		return '(object '.get_class($this).')';
	}
}
