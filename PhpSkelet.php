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

if(PHPSKELET_AUTOLOADER_ENABLED)
{
	if(is_file('generated_code/autoloader.php'))
	{
		// generated autoloader
		require_once 'generated_code/autoloader.php';
	}
	else
	{
		// should this be here? I thing no, but at this moment I leave this here
		require_once 'Wizards/FirstRun.php';
	}
}
