<?php

class SmartyAppHook extends ApplicationHook
{
	public function __create()
	{
		parent::__create();
	}
	
	function resolveRequest(Application $app)
	{
	}
	
	function run(Application $app)
	{
	}

	/**
	 * Template 
	 * @var Template
	 */
	public $template;
	private $template_file;
	
	public function getTemplateResource($extension = 'php', $url_path = null, $recurse_up = false)
	{
		if($url_path === null)
			$url_path = $this->url_path;
		
		if($recurse_up)
			return $this->getTemplateResourceRecurseUp($extension, $url_path);
		
		// TODO: SECURITY: Jail path to getTemplateDir()
		$path = $this->getTemplateDir().$url_path;
		if(is_dir($path))
			$path .= '/__index.'.$extension;
		else 
			$path .= '.'.$extension; 
		return $path; 
	}
	
	public function getTemplateResourceRecurseUp($extension = 'php', $url_path = null)
	{
		// TODO: SECURITY: Jail path to getTemplateDir()
		
		$url_path = explode('/', $url_path);
		$resource = array_pop($url_path);
		while(true)
		{
			$path = $this->getTemplateDir().implode('/', $url_path).'/'.$resource;
			if(is_dir($path))
				return $path .'/__index.'.$extension;
			else if(is_file($path.'.'.$extension))
				return $path.'.'.$extension;
			else if(count($url_path) == 0)
				return false;
			array_pop($url_path);
		} 
	}
	
	/**
	 * Get directory with slash at end
	 */
	public function getTemplateDir()
	{
		return DIR_TEMPLATE;
	}
	
	public function normalizeUrlPath($url)
	{
		$req_page_norm = str_remove_dia($url);
		$req_page_norm = preg_replace(array('#[/\\\\]+#','/[_\\s]+/'), array('/', '_'), $req_page_norm);
		return trim($req_page_norm, '/\\');
	}
	
	protected function checkRequest()
	{
		if($this->request_url_path != $this->url_path)
		{
			// this is one way to tell client where resource really is, 
			// but this no effect in browser's navigor  
			//header('Content-Location: /'.$req_page_norm); // prepend slash!
			
			redirect('/'.$this->url_path);
		}
	}
	
	protected function setMimeType($url_path = null)
	{
		if($url_path === null)
			$url_path = $this->url_path;
		
		$mimes = MimeTypes::getInstance();
		$ext = pathinfo($url_path, PATHINFO_EXTENSION);
		if($ext == '') $ext = 'xhtml';
		return $mimes->setHeaderContentTypeByExtension($ext);
	}

	public function prepareTemplate($url_path = null, $recurse_up = false)
	{
		$template_file = $this->getTemplateResource('tpl', $url_path, $recurse_up);
		if(is_file($template_file))
		{
			$this->template_file = $template_file;
			$this->template = new Template();
			$this->template->assign('app', $this);
			return true;
		}
		else 
		{
			$this->template_file = null;
			return false;
		}
	}

	public function runPhpScript($url_path = null, $recurse_up = false)
	{
		$php_file = $this->getTemplateResource('php', $url_path, $recurse_up);
		if(is_file($php_file))
		{
			$app = $GLOBALS['app'] = $this;
			$tpl = $GLOBALS['tpl'] = $this->template;
			require $php_file;
			//unset($GLOBALS['tpl'], $GLOBALS['app']);
			
			return true;
		}
		return false;
	}

	public function runTemplate()
	{
		if($this->template_file === null)
			return false;
		$this->template->display($this->template_file);
			return true;
	}

	public function prepareErrorTemplate($http_code, $url_path=null)
	{
		if($url_path === null)
			$url_path = $this->url_path;
		return $this->prepareTemplate($url_path.'/__http'.$http_code, true);
	}
	
	public function runPhpErrorScript($http_code, $url_path = null)
	{
		if($url_path === null)
			$url_path = $this->url_path;
		return $this->runPhpScript($url_path.'/__http'.$http_code, true);
	}
	
	
}