<?php

/**
 * global session 
 * @var Session $session
 */
$session = null;

class Session extends Singleton implements ArrayAccess
{
	private $started = false;
	private $var_classes;

	public static function getInstance()
	{
		$instance = parent::getInstance();
		$instance->started = session_start();
		if(!isset($_SESSION['___VAR_CLASSES']))
			$_SESSION['___VAR_CLASSES'] = array();
		$this->var_classes &= $_SESSION['___VAR_CLASSES']; 
		$GLOBALS['session'] = $instance;
		return $instance;
	}
	
	// core implementation
	
	/**
	 * is session variable defined?
	 * @param string $varname
	 */
	public function is($varname)
	{
		return isset($_SESSION[$varname]);
	}

	/**
	 * Unset session variable
	 * @param string $varname
	 */
	public function delete($varname)
	{
		unset($_SESSION[$varname]);
	}

	/**
	 * get session variable $varname
	 * @param string $varname
	 * @param mixed[optional] $default - default value if $varname is not set
	 */
	public function get($varname, $default = null)
	{
		return isset($_SESSION[$varname]) ? $_SESSION[$varname] : $default;
	}
	
	/**
	 * Set session variable
	 * @param string $varname
	 * @param mixed $value
	 */
	public function set($varname, $value)
	{
		$_SESSION[$varname] = $value;
	}
	
	// interface ArrayAccess

	/**
	 * @param offset
	 */
	public function offsetExists($offset) 
	{
		return $this->is($offset);
	}

	/**
	 * @param offset
	 */
	public function offsetGet($offset) 
	{
		return $this->get($offset);
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet($offset, $value) 
	{
		$this->set($offset, $value);
	}

	/**
	 * @param offset
	 */
	public function offsetUnset($offset) 
	{
		return $this->delete($offset);
	}

}