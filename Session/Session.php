<?php

/**
 * Abstract class for session management.
 * Provides unified API for working with session data
 * @author langpavel
 */
abstract class Session extends SafeObject implements ArrayAccess
{
	/**
	 * Instance of selected session manager
	 * @var Session
	 */
	private static $instance = null;

	/**
	 * Session variables
	 * @var VariableSet
	 */
	protected static $variables;
	
	/**
	 * Get selected session manager
	 * @throws InvalidOperationException
	 */
	public static function getInstance()
	{
		if(Session::$instance === null)
			throw new InvalidOperationException('Session manager has not been selected');
		return Session::$instance;
	}
	
	protected function __construct()
	{
		if(Session::$instance !== null)
			throw new InvalidOperationException('Session manager already selected');
		Session::$instance = $this;
	}

	abstract public function isStarted();
	abstract public function start();
	abstract public function startNew();
	
	protected function checkStarted()
	{
		if(!$this->isStarted())
			throw new InvalidOperationException('Session was not started');
		return true;
	}
	
	public function has($name, $value) 
	{
		Session::$variables->set($name, $value);
	}
	
	public function set($name, $value) 
	{
		Session::$variables->set($name, $value);
	}
	
	public function get($name) 
	{
		return Session::$variables->get($name);
	}
	
	public function unsetVar($name) 
	{
		return Session::$variables->unsetVar($name);
	}
	
	// ArrayAccess
	public function offsetExists ($offset) { return $this->has($offset); }
	public function offsetGet ($offset) { return $this->get($offset); }
	public function offsetSet ($offset, $value) { $this->set($offset, $value); }
	public function offsetUnset ($offset) { $this->unsetVar($offset); }
		
}
