<?php

/**
 * Password handling class. This class never store password itself for security reason, rather it store only salted password hash.
 * In default generates password hash 70 characters long, hash function is SHA256 with 5char salt prefix giving 62^5 posibilities
 * @author langpavel
 */
final class Password extends SafeObject
{
	public static $default_hash_method = 'sha256';
	public static $default_salt_width = 5;
	
	private $hash;
	private $salt;
	private $hash_method;

	/**
	 * Create new encrypted password
	 * @param string $password plain-text password
	 * @param string[optional] $hash_method algorithm to be used, defaults to value of static property $default_hash_method
	 * @return Password
	 */
	public static function create($password, $hash_method = null, $salt = null)
	{
		if($password instanceof Password)
			return clone $password;
		if(!is_string($password))
			throw new InvalidArgumentException('Password is not a string');
		return new Password($password, true, $hash_method, $salt);
	}

	/**
	 * Load encrypted password from string.
	 * @param string $hashed_password in format "{$salt}:{$hash}"
	 * @param string $hash_method hash method - possible values are from hash_algos() call result
	 * @return Password
	 */
	public static function load($enc_password, $hash_method = null)
	{
		return new Password($enc_password, false, $hash_method);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $password
	 * @param unknown_type $create
	 * @param unknown_type $hash_method
	 * @param unknown_type $salt
	 */
	public function __construct($password, $create = true, $hash_method = null, $salt = null)
	{
		parent::__construct();
		$this->hash_method = ($hash_method === null) ? self::$default_hash_method : $hash_method;
		if($create)
		{
			$this->salt = ($salt === null) ? self::createSalt() : $salt;
			$this->hash = $this->getHash($password);
		}
		else 
		{
			if($salt === null)
				list($salt, $password) = explode(':', $password, 2);
			$this->salt = $salt;
			$this->hash = $password;
		}
	}

	public static function createSalt($len = null)
	{
		return str_random(($len === null) ? self::$default_salt_width : $len);
	}
	
	/**
	 * Read current hash or create salted hash of plain-text password  
	 * @param string $password unencrypted password
	 */
	public function getHash($password=null)
	{
		if($password === null)
			return $this->hash;
		return hash($this->hash_method, $this->salt.$password);
	}

	/**
	 * Read current salt
	 */
	public function getSalt()
	{
		return $this->salt;
	}
	
	/**
	 * Verify that password match
	 * @param unknown_type $password
	 */
	public function verify($password)
	{
		if($password === null || !is_string($password))
			return false;
		return $this->hash === $this->getHash($password);
	} 

	/**
	 * output password hash with salt 
	 */
	public function __toString()
	{
		return "{$this->salt}:{$this->hash}";
	}
}
