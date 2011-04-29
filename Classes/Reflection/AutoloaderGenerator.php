<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet license.
 */

// this file does not use PhpSkelet autoloader for good reason ;-)
if(!defined('PHPSKELET_AUTOLOADER_ENABLED'))
	define('PHPSKELET_AUTOLOADER_ENABLED', false);

require_once __DIR__.'/../../PhpSkelet.php';
require_once __DIR__.'/SourceGenerator.php';
require_once __DIR__.'/PHPSourceReader.php';

class AutoloaderGenerator extends SourceGenerator
{
	private $dirs = array();
	private $extensions = array();
	private $classes_paths = array();

	public function __construct()
	{
		parent::__construct();

		$this->addExtension('php'/*, 'php3', 'phtml'*/);
	}

	public function addPath($path, $recursive = true)
	{
		$path = realpath($path);
		$this->dirs[] = array($path, $recursive);
	}

	public function addExtension($file_extension)
	{
		$exts = func_get_args();
		foreach($exts as $ext)
			$this->extensions[] = $ext;
	}

	public function writeIncludes()
	{
		$this->writeLn();
		$this->writeLn('// require autoloader class');
		$this->writeLn("require_once __DIR__.'/../Classes/Autoloader.php';");
	}
	
	public function process()
	{
		$this->beginSourceFile();
		
		$this->writeIncludes();

		foreach($this->dirs as $dir)
		{
			list($path, $recursive) = $dir;
			$this->processPath($path, $recursive);
		}

		$this->writeLn('
function __autoload($class)
{
	Autoloader::load($class);
}
');

		$this->writeAmbiguous();
	}

	private function writeAmbiguous()
	{
		foreach($this->classes_paths as $class=>$files)
		{
			if(!is_array($files))
				continue;
			$this->writeLn('// WARNING: Class "'.$class.'" defined in multiple files:');
			foreach($files as $file)
				$this->writeLn('//   at file '.$file);
		}
	}

	private function isSourceFile($file)
	{
		$extensionpos = strrpos($file, '.');
		if($extensionpos === false)
			return false;
		$extension = substr($file, $extensionpos+1);
		return array_search($extension, $this->extensions) !== false;
	}

	private function processPath($path, $recursive)
	{
		try
		{
			$diriter = new DirectoryIterator(realpath($path));
			foreach($diriter as $entry)
			{
				// filter parent dirs and unix hidden files and dirs
				if($entry->isDot() || substr($entry->getBaseName(),0,1) === '.')
					continue;
				$source_filename = realpath($entry->getPathName());
				if($entry->isDir())
				{
					if(!$recursive)
						continue;
					$this->writeLn();
					$this->writeLn('// DIR:  '.$source_filename);
					$this->processPath($source_filename, $recursive);
					continue;
				}
				if(!$this->isSourceFile($source_filename))
					continue;
				$this->writeLn('// File: '.$source_filename);
				$this->processFile($source_filename);
			}
		}
		catch (Exception $err)
		{
			$this->writeLn('/*****************************************************
ERROR: '.$err.'
*/');
		}
	}

	public function processFile($file)
	{
		gc_collect_cycles();
		$phpreader = new PHPSourceReader($file);
		$classes = $phpreader->findDefinedClasses();
		foreach($classes as $classinfo)
		{
			$classname = $classinfo['name'];
			if(isset($this->classes_paths[$classname]))
			{
				$v =& $this->classes_paths[$classname];
				$this->writeLn('// WARNING - class name collision');
				if(is_array($v))
					$v[] = $file;
				else
					$v = array($v, $file);
			}
			else
				$this->classes_paths[$classname] = $file;

			$modif = ($classinfo['abstract'] ? 'abstract ' : ($classinfo['final'] ? 'final ' : ''));
			//ddd($modif, $classinfo);
			$extends = (count($classinfo['extends'])) ? ' extends '.implode(', ', $classinfo['extends']) : '';
			$implements = (count($classinfo['implements'])) ? ' implements '.implode(', ', $classinfo['extends']) : '';

			$comment = '// '.$modif.'class '.$classname.$extends.$implements.';';
			$this->writeLn("Autoloader::register(".var_export($classname, true).", ".var_export($file, true).'); '.$comment);
		}
	}

	public function getClassesPaths()
	{
		return $this->classes_paths;
	}

}

