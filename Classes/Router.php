<?php

/**
 * Router - translates url to view
 *
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once('../PhpSkelet.php');

class AbstractRouter extends SafeObject implements IRouter
{
	function __construct()
	{
		parent::__construct();
	}
	
	function function_name() 
	{
		;
	}
}