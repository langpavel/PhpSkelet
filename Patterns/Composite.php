<?php

abstract class Composite extends SafeObject implements IteratorAggregate, Countable
{
	private $name = null;
	private $parent = null;
	private $children = array();
	private $child_class = __CLASS__;

	public function __construct(Composite $parent = null)
	{
		parent::__construct();
		if($parent !== null)
			$this->setParent($parent);
	}
	
	public function __destruct()
	{
		$this->remove();
		parent::__destruct();
	}

	protected function setChildClass($classname)
	{
		$this->child_class = $classname;
	}

	public function getParent()
	{
		return $this->parent;		
	}

	public final function getRoot()
	{
		$parent = $this;
		while($parent->parent !== null)
			$parent = $parent->parent;
		return $parent;
	}
	
	public function setParent(Composite $parent)
	{
		if($this->parent !== null)
			$this->parent->remove($this);
		if($parent !== null)
			$parent->add($this);
		return $this;
	}
	
	public function add($child)
	{
		if(!$child instanceof $this->child_class)
			throw new InvalidArgumentException();
		
		if($child->parent !== null)
			$child->parent->remove($child);			
		$this->children[$child->getObjectId()] = $child;
		$child->parent = $this;
		return $this;
	}

	public final function addArray(array $children)
	{
		foreach($children as $child)
			$this->add($child);
		return $this;
	}

	public function remove($child = null)
	{
		if($child === null)
		{
			if($this->parent === null)
				return;
			$this->parent->remove($this);
			return;
		}
		
		if(!$child instanceof $this->child_class)
			throw new InvalidArgumentException();
		
		unset($this->children[$child->getObjectId()]);
		$child->parent = null;
	}

	public function getIterator()
	{
		return new ArrayIterator($this->children);
	}
	
	public function count()
	{
		return count($this->children);
	}
	
//	protected function invokeChildrenMethod($method, $param_arr = null, $recursive = false)
//	{
//		if($param_arr === null)
//			$param_arr = array();
//		
//		foreach($this->children as $child)
//		{
//			if(method_exists($child, $method))
//				call_user_func_array(array($child, $method), $param_arr);
//			if($recursive)
//				$child->invokeChildrenMethod($method, $param_arr, $recursive);
//		}
//	}
	
	public function __clone()
	{
		parent::__clone();
		$this->parent = null;
		$children = $this->children;
		$this->children = array();

		foreach($children as $child)
		{
			$child = clone $child;
			$this->add($child);
		}
	}
}
