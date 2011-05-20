<?php

class SessionPhpNative extends Session
{
	private $started = false;
	
	protected function __construct()
	{
		parent::__construct();
	}

	public function isStarted()
	{
		return $this->started;
	}
	
	public function start()
	{
		if($this->started)
			return;
			
		if(!session_start())
			throw new InvalidOperationException('Session cannot be started');

		// read variables and replace _SESSION global
		Session::$variables->setVars($_SESSION);
		$_SESSION = Session::$variables; 
			
		return $this->started = true;
	}
	
	public function startNew()
	{
		if($this->started)
			throw new InvalidOperationException('Session already started');
		
		session_regenerate_id(true);
			
		if(!session_start())
			throw new InvalidOperationException('Session cannot be started');
			
		$_SESSION = Session::$variables;
			
		return $this->started = true;
	}
	
	protected function checkStarted($start = false)
	{
		if(!$this->isStarted())
		{
			if($start)
				return $this->start();
			else
				throw new InvalidOperationException('Session was not started');
		}
				
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
