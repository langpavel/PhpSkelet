<?php

/**
 * Password handling class. This class never store password itself for security reason, rather it store only salted password hash.
 * In default generates password hash 70 characters long, hash function is SHA256 with 5char salt prefix giving 62^5 posibilities
 * @author langpavel
 */
class Password extends SafeObject
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
		return new Password($password, true, $hash_method, $salt);
	}

	/**
	 * Load encrypted password from string.
	 * @param string $hashed_password in format "{$salt}:{$hash}"
	 * @param unknown_type $hash_method
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
			$this->salt = ($salt === null) ? str_random(self::$default_salt_width) : $salt;
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

	/**
	 * Create salted hash of plain-text password and destroy referenced plain-text.  
	 * @param string $password reference to unencrypted password
	 */
	public function getHash(&$password)
	{
		$result = hash($this->hash_method, $this->salt.$password);
		$password = '';
		unset($password);
		return $result;
	}
	
	public function verify($password)
	{
		return $this->hash === $this->getHash($password);
	} 

	public function __toString()
	{
		return "{$this->salt}:{$this->hash}";
	}
}
