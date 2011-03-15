<?php

/**
 * Autoloader
 *
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once('../Patterns/Singleton.php');

// specific exception
class AutoloaderException extends PhpSkeletException { }

class Autoloader extends Singleton
{
	protected $classes = array();
	
	/**
	 * Register class definition file for autoloader
	 * @param string $classname Class to be registered
	 * @param string $file Codebase file of the class
	 * @param bool[optional] $override default false - set new codebase for class
	 */	
	public static function register($classname, $file, $override = false)
	{
		self::getInstance()->registerClass($classname, $file, $override);
	}

	/**
	 * Register class definition file for autoloader
	 * @param string $classname Class to be registered
	 * @param string $file Codebase file of the class
	 * @param bool[optional] $override default false - set new codebase for class
	 * @throws PhpSkeletException
	 */
	public function registerClass($classname, $file, $override = false)
	{
		if($override && isset(self::$classes[$classname]))
			throw new PhpSkeletException("Class '$classname' is alredy registered in file '".self::$classes[$classname]."'");
			
		$this->classes[$classname] = $file;
	}
	
	public static function load($classname)
	{
		self::getInstance()->loadClass($classname);
	}
	
	/**
	 * Load requested class
	 * @param string $classname name of the class
	 * @throws AutoloaderException
	 */
	public function loadClass($classname)
	{
		if(!isset($this->classes[$classname]))
			throw new AutoloaderException("Class '$classname' is not registered in autoloader");
		
		$filename = $this->classes[$classname];
		if(!is_file($filename))
			throw new AutoloaderException("Class '$classname' is registered but file '$filename' does not exists!");
			
		require_once $filename;
	}
	
	/**
	 * returns magic __FILE__ of autoloader class
	 */
	public function getSourceBaseFile()
	{
		return __FILE__;		
	}
}
