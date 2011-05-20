<?php

/**
 * Variable set. Storage for application variables
 * @author langpavel
 */
class VariableSet extends SafeObject implements ArrayAccess, IteratorAggregate
{
	private $variables = array();
	private $soft_mode = false;
	
	public function __construct($initial_set = null)
	{
		parent::__construct();
		if($initial_set !== null)
			$this->setVars($initial_set);
	}

	/**
	 * If variable is defined
	 * @param string $name
	 * @return bool
	 */
	public function has($name) { return isset($this->variables[$name]); }
	
	/**
	 * Set variable value
	 * @param string $name
	 * @param mixed $val
	 */
	public function set($name, $val) { $this->variables[$name] = $val; }
	
	/**
	 * Get variable value. If not set, behavior is depended on 'soft_mode'.
	 * If soft mode is set, return null for nonexisting 
	 * otherwise throws InvalidOperationException
	 * @param unknown_type $name
	 * @return mixed variable value
	 */
	public function get($name) 
	{
		if(isset($this->variables[$name]))
			return $this->variables[$name];
		if($this->soft_mode)
		{
			// TODO: possibly some warning here 
			return null;
		}
		throw new InvalidOperationException(
			"Cannot return undefined application variable '$name'");
	}
	
	/**
	 * Unset variable
	 * @param string $name
	 */
	public function unsetVar($name) { unset($this->variables[$name]); }

	/**
	 * Behaves like array_merge
	 * @param mixed $iterable key/value array or iterator
	 */
	public function setVars($iterable)
	{
		foreach ($iterable as $name=>$val)
			$this->setVar($name, $val);		
	}

	// ArrayAccess
	public function offsetExists ($offset) { return $this->has($offset); }
	public function offsetGet ($offset) { return $this->get($offset); }
	public function offsetSet ($offset, $value) { $this->set($offset, $value); }
	public function offsetUnset ($offset) { $this->unsetVar($offset); }
	
	// IteratorAggregate
	public function getIterator()
	{
		return new ArrayIterator($this->variables);
	}
}
