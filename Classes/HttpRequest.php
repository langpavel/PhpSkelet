<?php

/**
 * HttpRequest - request sent via HTTP.
 *
 * @author langpavel
 */

class HttpRequest extends SafeObject implements ISingleton, ArrayAccess
{
	private static $current = null;

	/** @var string http method always lowercase */
	private $method;

	/** @var URI current request uri */
	private $uri;

	/** @var array */
	private $post = array();

	/** @var array */
	private $files = array();

	/** @var array */
	private $cookies = array();

	/** @var array */
	private $headers = array();

	/** @var string */
	private $remoteAddress;

	/** @var string */
	private $remoteHost;

	/**
	 * Get instance of current HttpRequest.
	 * Be carefull, do not modify this instance if it not what you really want.
	 * You can call HttpRequest::getCurrent() with feel of safety in your head.
	 */
	public static function getInstance()
	{
		if(self::$current !== null)
			return self::$current;

		if (function_exists('apache_request_headers'))
			$request_headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
		else
		{
			$request_headers = array();
			foreach($_SERVER as $k => $v)
			{
				if(strncmp($k, 'HTTP_', 5) == 0)
				{
					$k = strtolower(strtr(substr($k, 5), '_', '-'));
					$request_headers[$k] = $v;
				}
			}
		}

		self::$current = new HttpRequest(
			URI::getCurrent(),
			$_POST,
			$_FILES,
			$_COOKIE,
			$request_headers,
			isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null,
			isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null
		);

		return self::$current;
	}

	/**
	 * Creates current HttpRequest object.
	 * @return Request
	 */
	public static function getCurrent()
	{
		return clone self::getInstance();
	}

	public function __construct(URI $uri, $post = null, $files = null, $cookies = null,
		$headers = null, $method = null, $remoteAddress = null, $remoteHost = null)
	{
		$this->setUri($uri);
		$this->setPost($post);
		$this->setFiles($files);
		$this->setCookies($cookies);
		$this->setHeaders($headers);
		$this->setMethod($method);
		$this->setRemoteAddress($remoteAddress);
		$this->setRemoteHost($remoteHost);
	}

	public function isGet() { return $this->method == 'get'; }
	public function isPost() { return $this->method == 'post'; }

	public function setQuery($value) { $this->uri->setQuery($value); }
	public function getQuery($part = null, $default = null)
	{
		return $this->uri->getQuery($part, $default);
	}

	/**
	 * if request is ajax or ajax or json HTTP query parameters are set
	 */
	public function isAjax()
	{
		return ('xmlhttprequest' == strtolower($this->getHeader('X-Requested-With', '')))
			|| $this->getQuery('ajax', false)
			|| $this->getQuery('json', false);
	}

	/**
	 * Get value of method
	 * @return mixed method
	 */
	public function getMethod() { return $this->method; }

	/**
	 * Set value of method
	 * @param mixed $value method
	 * @return HttpRequest self
	 */
	public function setMethod($value) { $this->method = strtolower($value); return $this; }

	/**
	 * Get value of uri
	 * @return mixed uri
	 */
	public function getUri() { return $this->uri; }

	/**
	 * Set value of url
	 * @param mixed $value url
	 * @return HttpRequest self
	 */
	public function setUri($value)
	{
		$this->uri = URI::get($value);
		return $this;
	}

	/**
	 * Get value of POST
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed post
	 */
	public function getPost($key = null, $default = null)
	{
		if($key === null)
			return $this->post;
		return isset($this->post[$key]) ? $this->post[$key] : $default;

	}

	/**
	 * Set value of POST
	 * @param mixed $key
	 * @param mixed $value
	 * @return HttpRequest self
	 */
	public function setPost($key, $value = null)
	{
		if($value === null && is_array($key))
			$this->post = array_merge($this->post, $key);
		else
			$this->post[$key] = $value;
		return $this;
	}

	/**
	 * Get value of files
	 * @return mixed files
	 */
	public function getFiles() { return $this->files; }

	/**
	 * Set value of files
	 * @param mixed $value files
	 * @return HttpRequest self
	 */
	public function setFiles($value) { $this->files = $value; return $this; }

	/**
	 * Get value of cookies
	 * @return mixed cookies
	 */
	public function getCookies() { return $this->cookies; }

	/**
	 * Set value of cookies
	 * @param mixed $value cookies
	 * @return HttpRequest self
	 */
	public function setCookies($value) { $this->cookies = $value; return $this; }

	/**
	 * Return the value of the HTTP header. Pass the header name as the
	 * plain, HTTP-specified header name (e.g. 'Accept-Encoding').
	 * @param  string
	 * @param  mixed
	 * @return mixed
	 */
	public function getHeader($header, $default = NULL)
	{
		$header = strtolower($header);
		return isset($this->headers[$header]) ? $this->headers[$header] : $default;
	}

	/**
	 * Get value of headers - keys are lower-cased
	 * @return mixed headers
	 */
	public function getHeaders() { return $this->headers; }

	/**
	 * Set value of headers
	 * @param mixed $value headers
	 * @return HttpRequest self
	 */
	public function setHeaders($value) { $this->headers = $value; return $this; }

	/**
	 * Get value of remoteAddress
	 * @return mixed remoteAddress
	 */
	public function getRemoteAddress() { return $this->remoteAddress; }

	/**
	 * Set value of remoteAddress
	 * @param mixed $value remoteAddress
	 * @return HttpRequest self
	 */
	public function setRemoteAddress($value) { $this->remoteAddress = $value; return $this; }

	/**
	 * Get value of remoteHost
	 * @return mixed remoteHost
	 */
	public function getRemoteHost() { return $this->remoteHost; }

	/**
	 * Set value of remoteHost
	 * @param mixed $value remoteHost
	 * @return HttpRequest self
	 */
	public function setRemoteHost($value) { $this->remoteHost = $value; return $this; }


	public function offsetExists ($offset) { throw new NotImplementedException(); }

	/**
	 * Get value of POST or GET
	 * @param offset
	 */
	public function offsetGet($offset)
	{
		// TODO: Implement all required
		return isset($this->post[$offset]) ? $this->post[$offset] : $this->uri->getQuery($offset, null);
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet ($offset, $value) { throw new NotImplementedException(); }

	/**
	 * @param offset
	 */
	public function offsetUnset ($offset) { throw new NotImplementedException(); }

	//public function execute()
	//{
	//	// TODO: check if request is not recursive
	//	// TODO: do request throught cURL
	//	// TODO: return HttpResponse object or descendant - should be transparent
	//	throw new NotImplementedException();
	//}
}
