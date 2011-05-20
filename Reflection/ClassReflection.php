<?php

// there is not to see yet

class ClassReflection extends ReflectionClass
{
	//public $classname;
	//public $instance = null;
	
	public function __construct($class)
	{
		parent::__construct($class);
		/*
		if(is_object($class))
		{
			$this->instance = $class;
			$this->classname = get_class($class);
		}
		else if(is_string($class))
		{
			$this->classname = $class;
		}
		*/
	}
	
	public static function getPropertiesAsArray($instance, $recursive = false)
	{
		return ReflectionMixin::getPropertiesAsArray($instance, $recursive);
	}
	
	public function getReflectionArray()
	{
		return static::getPropertiesAsArray($this);
	}
}
