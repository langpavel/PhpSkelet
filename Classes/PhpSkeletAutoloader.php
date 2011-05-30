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
	public static $throw_on_failure = false;
	
	protected $classes = array();
	
	/**
	 * Register class definition file for autoloader
	 * @param string $classname Class to be registered
	 * @param array $file Codebase file of the class
	 * @param bool[optional] $override default false - set new codebase for class
	 * @throws PhpSkeletException
	 */
	public function register($classname, $file, $override = false)
	{
		if(!$override && isset($this->classes[$classname]))
			throw new PhpSkeletException("Class '$classname' is alredy registered in file '".$this->classes[$classname]['pathname']."'");

		if(!is_array($file))
			$file = array('pathname'=>$file);
			
		$this->classes[$classname] = $file;
	}
	
	/**
	 * Load requested class, 
	 * optionally throw an exception as configured by PhpSkeletAutoloader::$throw_on_failure
	 *  
	 * @param string $classname name of the class
	 * @throws AutoloaderException
	 */
	public function load($classname)
	{
		$filename = $this->getClassFilePathname($classname);
		if($filename === null)
		{
			if(self::$throw_on_failure)
				throw new AutoloaderException("Class '$classname' is not registered in autoloader");
			else
				return false;
		}
		
		if(!is_file($filename))
		{
			if(self::$throw_on_failure)
				throw new AutoloaderException("Class '$classname' is registered but file '$filename' does not exists!");
			else
				return false;
		}
			
		require_once $filename;
		return true;
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
	 * Get info about class definition
	 * @param string $classname
	 * @return array 
	 */
	public function getClassInfo($classname)
	{
		if(!isset($this->classes[$classname]))
			return null;
		return $this->classes[$classname];
	}
	
	/**
	 * Get all registered classes with metadata
	 */
	public function getRegisteredClasses()
	{
		return $this->classes;
	}
}
