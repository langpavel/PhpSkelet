<?php

class HttpResponse extends SafeObject
{
	public static $HTTP_CODE_TEXT = array(
	    /* HTTP 1xx Informational */
	    100=>"Continue",
	    101=>"Switching Protocols",
	    102=>"Processing",
	    122=>"Request-URI too long",
	    
		/* HTTP 2xx Success */
	    200=>"OK",
	    201=>"Created",
	    202=>"Accepted",
	    203=>"Non-Authoritative Information",
	    204=>"No Content",
	    205=>"Reset Content",
	    206=>"Partial Content",
	    207=>"Multi-Status",
	    226=>"IM Used", // (RFC 3229) The server has fulfilled a GET request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance.
	    
		/* HTTP 3xx Redirection */
	    300=>"Multiple Choices",
	    301=>"Moved Permanently",
	    302=>"Found",
	    303=>"See Other",
	    304=>"Not Modified",
	    305=>"Use Proxy",
	    306=>"Switch Proxy", // deprecated
	    307=>"Temporary Redirect",
	    
		/* HTTP 4xx Client Error */
	    400=>"Bad Request",
	    401=>"Authorization Required",
	    402=>"Payment Required",
	    403=>"Forbidden",
	    404=>"Not Found",
	    405=>"Method Not Allowed",
	    406=>"Not Acceptable",
	    407=>"Proxy Authentication Required",
	    408=>"Request Time-out",
	    409=>"Conflict",
	    410=>"Gone",
	    411=>"Length Required",
	    412=>"Precondition Failed",
	    413=>"Request Entity Too Large",
	    414=>"Request-URI Too Large",
	    415=>"Unsupported Media Type",
	    416=>"Requested Range Not Satisfiable",
	    417=>"Expectation Failed",
	    418=>"I'm a teapot",
	    //419=>"unused",
	    //420=>"unused",
	    //421=>"unused",
	    422=>"Unprocessable Entity",
	    423=>"Locked",
	    424=>"Failed Dependency",
	    425=>"No code",
	    426=>"Upgrade Required",
	    
	    /* others... */
	    444=>"No Response", // An Nginx HTTP server extension. The server returns no information to the client and closes the connection (useful as a deterrent for malware).
		449=>"Retry With", // A Microsoft extension. The request should be retried after performing the appropriate action.
		450=>"Blocked by Windows Parental Controls", // A Microsoft extension. This error is given when Windows Parental Controls are turned on and are blocking access to the given webpage.
		499=>"Client Closed Request", // An Nginx HTTP server extension. This code is introduced to log the case when the connection is closed by client while HTTP server is processing its request, making server unable to send the HTTP header back.
	
		/* HTTP 5xx Server Error */
	    500=>"Internal Server Error",
	    501=>"Method Not Implemented",
	    502=>"Bad Gateway",
	    503=>"Service Temporarily Unavailable",
	    504=>"Gateway Time-out",
	    505=>"HTTP Version Not Supported",
	    506=>"Variant Also Negotiates",
	    507=>"Insufficient Storage",
	    //508=>"unused",
	    //509=>"unused",
	    510=>"Not Extended"
	);

	private $headers;
	private $response_code;
	
	public function __costruct()
	{
		parent::__construct();
		$this->response_code = 200;
	}
	
	public function setResponseCode($code = 200)
	{
		$this->response_code = $code;					
	}
	
	public function getResponseCode()
	{
		return $this->response_code;
	}
	
	public function setHeader($name, $value=null, $replace=true)
	{
		if($value === null)
			header($name, $replace);
		else
			header("$name: $value", true);
	}

}
