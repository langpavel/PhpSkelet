<?php

/**
 * Autoloader
 *
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/../Patterns/Singleton.php';

// specific exception
class AutoloaderException extends PhpSkeletException { }

class PhpSkeletAutoloader extends Singleton
{
	protected $classes = array();
	
	/**
	 * Register class definition file for autoloader
	 * @param string $classname Class to be registered
	 * @param array $file Codebase file of the class
	 * @param bool[optional] $override default false - set new codebase for class
	 */	
	public static function register($classname, $file, $override = false)
	{
		self::getInstance()->registerClass($classname, $file, $override);
	}

	/**
	 * Register class definition file for autoloader
	 * @param string $classname Class to be registered
	 * @param array $file Codebase file of the class
	 * @param bool[optional] $override default false - set new codebase for class
	 * @throws PhpSkeletException
	 */
	public function registerClass($classname, $file, $override = false)
	{
		if($override && isset(self::$classes[$classname]))
			throw new PhpSkeletException("Class '$classname' is alredy registered in file '".self::$classes[$classname]."'");

		if(!is_array($file))
			$file = array('pathname'=>$file);
			
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
		$filename = $this->getClassFilePathname($classname);
		if($filename === null)
			throw new AutoloaderException("Class '$classname' is not registered in autoloader");
		
		if(!is_file($filename))
			throw new AutoloaderException("Class '$classname' is registered but file '$filename' does not exists!");
			
		require_once $filename;
	}

	/**
	 * Get path of source file with class definition
	 * @param string $classname
	 * @return string pathname of source file with class or NULL if class not found
	 */
	public function getClassFilePathname($classname)
	{
		if(!isset($this->classes[$classname]))
			return null;
		return $this->classes[$classname]['pathname'];
	}
	
	/**
	 * Get all registered classes with metadata
	 */
	public function getRegisteredClasses()
	{
		return $this->classes;
	}
}
