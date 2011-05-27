<?php

/**
 * URI (Uniform Resource Identifier as described in RFC3986)
 */
class URI extends SafeObject
{
	// ONLY COMMON protocol port numbers; ordered by port number
	const DEFAULT_PORT_FTP = 21;
	const DEFAULT_PORT_TELNET = 23;
	const DEFAULT_PORT_HTTP = 80;
 	//const DEFAULT_PORT_NEWS = 119; // do anyone use this? If yes, send mail to core[at]phpskelet[dot]org
 	//const DEFAULT_PORT_NNTP = 119; // do anyone use this? If yes, send mail to core[at]phpskelet[dot]org
	const DEFAULT_PORT_HTTPS = 443;

	// port -> protocol name
	private static $defaultPortScheme = array(
		// ordered by port number
		self::DEFAULT_PORT_FTP => 'ftp',
		self::DEFAULT_PORT_TELNET => 'telnet',
		self::DEFAULT_PORT_HTTP => 'http',
		//self::DEFAULT_PORT_NNTP => 'nntp', // do anyone use this? If yes, send mail to core[at]phpskelet[dot]org
		//self::DEFAULT_PORT_NEWS => 'news', // duplicit with nntp
		self::DEFAULT_PORT_HTTPS => 'https',
	);
	
	// protocol name -> port
	private static $defaultSchemePort = array(
		// ordered by port number
		'ftp' => self::DEFAULT_PORT_FTP,
		'telnet' => self::DEFAULT_PORT_TELNET,
		'http' => self::DEFAULT_PORT_HTTP,
		//'nntp' => self::DEFAULT_PORT_NNTP, // do anyone use this? If yes, send mail to core[at]phpskelet[dot]org
		//'news' => self::DEFAULT_PORT_NEWS, // do anyone use this? If yes, send mail to core[at]phpskelet[dot]org
		'https' => self::DEFAULT_PORT_HTTPS,
	);
	
	// this statics represents current server request
	public static $currentHost = null;
	public static $currentScheme = null; // http or https
	public static $currentPort = null;

	private static $currentURI = null;

	//public static $use_parens_for_query_arrays = false;

	// keep this names same as return of parse_url()
	private $scheme = null; //e.g. http
	private $host = null;
	private $port = null;
	private $user = null;
	private $pass = null;
	private $path = null;
	private $query_parts = array(); // after the question mark ?
	private $fragment = null; // after the hashmark #

	static function __static_construct()
	{
		if(isset($_SERVER['HTTP_HOST']))
			self::$currentHost = $_SERVER['HTTP_HOST'];
		else if(isset($_SERVER['SERVER_NAME']))
			self::$currentHost = $_SERVER['SERVER_NAME'];
		else
			throw new PhpSkeletCoreBugException();
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
			self::$currentScheme = 'https';
		else
			self::$currentScheme = 'http';
		
		if(isset($_SERVER['SERVER_PORT']))
			self::$currentPort = $_SERVER['SERVER_PORT'];
		else
			throw new PhpSkeletCoreBugException();
	}
	
	/**
	 * Get port number by scheme name
	 * @return int or null
	 */
	static function getPortByScheme($scheme_name)
	{
		return isset(self::$defaultSchemePort[$scheme_name])
			? self::$defaultSchemePort[$scheme_name]
			: null;
	}
	
	/**
	 * Get scheme name by port number
	 * @return string or null
	 */
	static function getSchemeByPort($port_no)
	{
		return isset(self::$defaultPortScheme[$port_no])
			? self::$defaultPortScheme[$port_no]
			: null;
	}
	
	function __construct($uri = null)
	{
		parent::__construct();
		
		if($uri !== null)
			$this->parse($uri);
	}
	
	public function parse($uri)
	{
		$uri_parts = @parse_url($uri);
		if($uri_parts === false)
			throw new InvalidArgumentException('URI is too malformed');

		foreach ($uri_parts as $key => $val)
			$this->$key = $val;
	}
	
	public static function getCurrent()
	{
		if(self::$currentURI !== null)
			return clone self::$currentURI;
		
		self::$currentURI = new URI($_SERVER['REQUEST_URI'], true);
		// TODO: static configuration of not transient GET query parameters
		if(isset($_SERVER['QUERY_STRING']))
			self::$currentURI->setQuery($_SERVER['QUERY_STRING']);

		return clone self::$currentURI;
	}

	public static function combine($uri1, $uri2)
	{
		// TODO
		throw new NotImplementedException();
		
		if($uri1 === null)
			$uri1 = URI::getCurrent();		
	}

	/* output representation */
	
	/**
	 * Returns this URI with absolute path.
	 * @param string|URI $current_uri current path. It's used if this instance is relative URI
	 * @return string
	 */
	public function getAbsolute($current_uri = null)
	{
		$path = $this->path;
		if($path === null && ($scheme == 'http' || $scheme == 'https' ))
			$path = '/';
		else if($path == '' || $path[0] != '/')
			return (string) self::combine($current_uri, $this);

		$scheme = $this->getScheme(true);
		if($scheme == null)
			$scheme = self::$currentScheme;
		
		return $scheme . '://' . $this->getAuthority() . $path
			.(($this->query != '') ? ('?'.$this->query) : '')
			.(($this->fragment != '') ? ('#'.$this->fragment) : '');
	}
	
	/**
	 * Returns the [user[:pass]@]host[:port] part of URI.
	 * @return string
	 */
	public function getAuthority()
	{
		$host = $this->getHost();
		if($host === null)
			$host = self::$currentHost;
					
		$default_port = self::getPortByScheme($this->scheme);
		if($this->port !== null && $default_port !== null && $default_port != $this->port)
			$server = $host .':'. $this->port;
		else
			$server = $host;

		$userinfo = '';
		if($this->user != '')
		{
			$userinfo = ($this->pass != '')
				? $this->user .':'. $this->pass .'@'
				: $this->user .'@';
			return $userinfo.'@'.$server;
		}
		return $server;
	}
	
	function __toString()
	{
		try
		{
			return $this->getAbsolute();
		}
		catch(Exception $err)
		{
			return "[Invalid URI:$err]";
		}
	}
	
	/* properties */
	
	/**
	 * Get value of query
	 * @return mixed query
	 */
	public function getQuery($part = null)
	{
		if(empty($this->query_parts) || ($part !== null && !isset($this->query_parts[$part])))
			return '';
		$result = http_build_query($this->query_parts, '', '&');
		// not so clever, can destroy data!
		//if(self::$use_parens_for_query_arrays)
		//	$result = preg_replace(
		//		array('#(\\[|%5B)#i', '#(\\]|%5D)#i'), 
		//		array('(', ')'), $result);
		return $result;
	}

	/**
	 * Set value of query
	 * @param mixed $value query
	 * @return URI self
	 */
	public function setQuery($value = null)
	{
		if(is_array($value))
			$this->query_parts = $value;
		else if($value == null)
			$this->query_parts = array();
		else 
			parse_str($value, $this->query_parts);
		return $this; 
	}

	/**
	 * Set value of query
	 * @param mixed $value query
	 * @return URI self
	 */
	public function appendQuery($value)
	{
		if(is_array($value))
		{
			$this->query_parts = array_merge_recursive_simple($this->query_parts, $value);
		}
		else
		{
			$val = array();
			parse_str($value, $val);
			$this->query_parts = array_merge_recursive_simple($this->query_parts, $val);
		}
		return $this; 
	}

	/**
	 * Get value of scheme
	 * @return mixed scheme
	 */
	public function getScheme($decide_from_port = false) 
	{
		if($this->scheme == '' && $decide_from_port)
			return self::getSchemeByPort($this->port);
		
		return $this->scheme;
	}

	/**
	 * Set value of scheme
	 * @param mixed $value scheme
	 * @return URI self
	 */
	public function setScheme($value = null) { $this->scheme = $value; return $this; }

	/**
	 * Get value of host
	 * @return mixed host
	 */
	public function getHost() { return $this->host; }

	/**
	 * Set value of host
	 * @param mixed $value host
	 * @return URI self
	 */
	public function setHost($value) { $this->host = $value; return $this; }

	/**
	 * Get value of port
	 * @return mixed port
	 */
	public function getPort($decide_from_scheme = false) 
	{
		if($this->port === null && $decide_from_scheme)
			return self::getPortByScheme($this->scheme);

		return $this->port; 
	}

	/**
	 * Set value of port
	 * @param mixed $value port
	 * @return URI self
	 */
	public function setPort($value = null) { $this->port = $value; return $this; }

	/**
	 * Get value of user
	 * @return mixed user
	 */
	public function getUser() { return $this->user; }

	/**
	 * Set value of user
	 * @param mixed $value user
	 * @return URI self
	 */
	public function setUser($value = null) { $this->user = $value; return $this; }

	/**
	 * Get value of pass
	 * @return mixed pass
	 */
	public function getPass() { return $this->pass; }

	/**
	 * Set value of pass
	 * @param mixed $value pass
	 * @return URI self
	 */
	public function setPass($value = null) { $this->pass = $value; return $this; }

	/**
	 * Get value of path
	 * @return mixed path
	 */
	public function getPath() { return $this->path; }

	/**
	 * Set value of path
	 * @param mixed $value path
	 * @return URI self
	 */
	public function setPath($value) { $this->path = $value; return $this; }

	/**
	 * Get value of fragment
	 * @return mixed fragment
	 */
	public function getFragment() { return $this->fragment; }

	/**
	 * Set value of fragment
	 * @param mixed $value fragment
	 * @return URI self
	 */
	public function setFragment($value = null) { $this->fragment = $value; return $this; }

	/* END properties */
	
} URI::__static_construct();

