<?php

class MimeTypes extends Singleton
{
	private $file_extension_mime_types = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->registerFileExtensionMimeType('html', array($this, 'decide_xhtml'), true);
		$this->registerFileExtensionMimeType('xhtml', array($this, 'decide_xhtml'), true);
		$this->registerFileExtensionMimeType('xml', 'application/xml', true);
		$this->registerFileExtensionMimeType('js', 'application/javascript', true);
		$this->registerFileExtensionMimeType('json', 'application/json', true);
		$this->registerFileExtensionMimeType('xml', 'application/xml', true);
		$this->registerFileExtensionMimeType('txt', 'text/plain', true);
		$this->registerFileExtensionMimeType('xsl', 'application/xslt+xml', true);
		$this->registerFileExtensionMimeType('xslt', 'application/xslt+xml', true);
	}

	/**
	 * Register 
	 * @param unknown_type $extension
	 * @param unknown_type $mime_type
	 * @param unknown_type $charset
	 */
	public function registerFileExtensionMimeType($extension, $mime_type, $charset = null)
	{
		$this->file_extension_mime_types[$extension] = array($mime_type, $charset);
	}

	public function setHeaderContentType($filename, $default_charset = 'utf-8')
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		return $this->setHeaderContentTypeByExtension($ext, $default_charset);
	}
	
	public function setHeaderContentTypeByExtension($ext, $default_charset = 'utf-8')
	{
		if(isset($this->file_extension_mime_types[$ext]))
		{
			list($contentType, $charset) = $this->file_extension_mime_types[$ext];
			
			if(is_array($contentType))
				$contentType = call_user_func($contentType);
			
			if($charset === true && $default_charset)
				$charset = "; charset=$default_charset";
			else if(is_string($charset))
				$charset = "; charset=$charset";
			header('Content-Type: '.$contentType.$charset);
			return true;			
		}
		return false;
	}
	
	protected function decide_xhtml()
	{
		if(!isset($_GET['noxhtml']))
		{
			$accepts_xhtml = isset($_SERVER['HTTP_ACCEPT']) ? (false !== strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml')) : false;
			return $accepts_xhtml ? 'application/xhtml+xml' : 'text/html';
		}
		else 
			return 'text/html';
	}
}
