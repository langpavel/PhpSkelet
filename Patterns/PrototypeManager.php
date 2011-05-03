<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/../Classes/SafeObject.php';

/**
 * Abstract base class for prototype managers
 * @author langpavel@phpskelet.org
 */
abstract class PrototypeManager extends Singleton
{
	protected $prototypes = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Register prototype ID and assotiate it with class name or prototype instance.
	 * @param string $prototypeID
	 * @param mixed[optional] $prototype String means class name (if ommited same as $entityID), instance is prototype
	 * @param bool[optional] $replace Enable replace of registered prototype
	 */
	public function register($prototypeID, $prototype = null, $replace = false)
	{
		if($prototype === null)
			$prototype = $prototypeID;
		if(isset($this->prototypes[$prototypeID]))
		{
			if(!$replace)
				throw new InvaldOperationException("Cannot register prototype with ID '$prototypeID': alredy registered");
			unset($this->prototypes[$prototypeID]);
		}
			
		$this->prototypes[$prototypeID] = $prototype;
	}

	/**
	 * Create new instance of prototype
	 * Enter description here ...
	 * @param unknown_type $prototypeID
	 * @throws InvalidArgumentException
	 */
	public function create($prototypeID)
	{
		if(!isset($this->prototypes[$prototypeID]))
			throw new InvalidArgumentException("Cannot create prototype ID '$prototypeID'");
		$proto = $this->prototypes[$prototypeID];
		return is_string($proto) ? $this->createInstanceFromClassName($prototypeID, $proto) : clone $proto;
	}

	protected function createInstanceFromClassName($prototypeID, $className)
	{
		return new $className();
	}
	
	/*
	protected function get_raw_instance($prototypeID, $defaul = null)
	{
		return isset($this->prototypes[$prototypeID]) ? $this->prototypes[$entityID] : $defaul;		
	}
	*/

}
