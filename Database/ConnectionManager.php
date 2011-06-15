<?php

/**
 * Singleton manager of available connections.
 *
 * Connection properties are stored in global array
 * $GLOBALS['CONFIG']['DB'][$profile][$connection_name].
 * $profile and $connection_name is determined 'default' if ommited,
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 */
class ConnectionManager extends Singleton
{
	private $connections = array();

	/**
	 * Get connection (existing or create new)
	 * @param string[optional] $connection_name
	 * @param string[optional] $profile_name
	 */
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

	/**
	 * Create new connection and register it.
	 * @param string[optional] $connection_name
	 * @param string[optional] $profile_name
	 * @throws ApplicationException if connection already created
	 */
	private function createConnection($connection_name = null, $profile_name = null)
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
