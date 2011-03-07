<?php

/**
 * This is main file for whole Morpheus Framework.
 * require_once this file should be all what you must do
 * when you want use Morpheus Framework.
 *
 * This file is part of the Morpheus Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the Morpheus/LGPL license.
 */

if(defined('MORPHEUS_PHP_DEFINED'))
	throw new ApplicationException("require_once or include_once is preffered when you using Morpheus Framework");

define('MORPHEUS_PHP_DEFINED', true);

if(!defined('MORPHEUS_AUTOLOADER_ENABLED'))
	define('MORPHEUS_AUTOLOADER_ENABLED', true);

// common exceptions
require_once('exceptions.php');

// helper functions - cannot be autoloaded
require_once('debug.php');

// alwais requested classes
require_once('Classes/Object.php');
require_once('Classes/SafeObjectMixin.php');
require_once('Classes/SafeObject.php');
require_once('Classes/Debug.php');

// error handlers - set_error_handler and set_exception_handler
Debug::getInstance()->registerErrorHandlers();

if(MORPHEUS_AUTOLOADER_ENABLED)
{
	// generated autoloader
	require_once('generated_code/autoloader.php');
}
