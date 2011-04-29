<?php

/**
 * This is main file for whole PhpSkelet Framework.
 * require_once this file should be all what you must do
 * when you want use PhpSkelet Framework.
 *
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

if(defined('PHPSKELET_PHP'))
	throw new ApplicationException("require_once or include_once is preffered when you using PhpSkelet Framework");

define('PHPSKELET_PHP', true);

if(!defined('PHPSKELET_AUTOLOADER_ENABLED'))
	define('PHPSKELET_AUTOLOADER_ENABLED', true);

// common exceptions
require_once __DIR__.'/exceptions.php';

// helper functions - cannot be autoloaded
require_once __DIR__.'/debug.php';

// alwais requested classes
require_once __DIR__.'/Classes/Object.php';
require_once __DIR__.'/Classes/SafeObjectMixin.php';
require_once __DIR__.'/Classes/SafeObject.php';
require_once __DIR__.'/Classes/Debug.php';

// error handlers - set_error_handler and set_exception_handler
Debug::getInstance()->registerErrorHandlers();

if(defined('PHPSKELET_AUTOLOADER_ENABLED') && PHPSKELET_AUTOLOADER_ENABLED)
{
	if(is_file(__DIR__.'/generated_code/autoloader.php'))
	{
		// generated autoloader
		require_once  __DIR__.'/generated_code/autoloader.php';
	}
	else
	{
		// TODO: handle autoloader/instalation error
		echo '<p>Autoloader enabled but no autoloader file generated</p>';
		require_once __DIR__.'/index.php';
		exit;
	}
}
