<?php

abstract class Widget extends Composite
{
	private $name = null;
	
	public function __construct($name = null, $parent = null)
	{
		if($parent === null && $name instanceof Composite)
		{
			$parent = $name;
			$name = null;
		}
		
		parent::__construct($parent);
		$this->setChildClass(__CLASS__);
		
		if($name !== null)
			$this->setName($name);				
	}

}
