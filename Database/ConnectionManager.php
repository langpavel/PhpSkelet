<?php

class ConnectionManager extends Singleton 
{
	private $connections = array();
	
	public function get($connection_name = null, $profile_name = null)
	{
		if($profile_name === null)
			$profile_name = 'default';
		if($connection_name === null)
			$connection_name = 'default';
		
		if(!isset($this->connections[$profile_name])
		|| !isset($this->connections[$profile_name][$connection_name]))
			return $this->createConnection($connection_name, $profile_name);
		
		return $this->connections[$profile_name][$connection_name];
	}
	
	public function createConnection($connection_name = null, $profile_name = null)
	{
		if($profile_name === null)
			$profile_name = 'default';
		if($connection_name === null)
			$connection_name = 'default';
		
		if(!isset($this->connections[$profile_name]))
			$this->connections[$profile_name] = array();
		
		if(isset($this->connections[$profile_name][$connection_name]))
			$this->connections[$profile_name][$connection_name]->disconnect();
		
		if(!isset($GLOBALS['CONFIG']['DB'][$profile_name][$connection_name]))
			throw new ApplicationException("Configuration for connection [$connection_name] at profile [$profile_name] not found");
		
		return $this->connections[$profile_name][$connection_name] = 
			dibi::connect($GLOBALS['CONFIG']['DB'][$profile_name][$connection_name]);
	}
}
