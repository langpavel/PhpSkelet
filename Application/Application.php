<?php

require_once __DIR__.'/../PhpSkelet.php';

final class Application extends Singleton implements ArrayAccess
{
	const APP_STATE_SICK = 0x00; // this should never happen
	const APP_STATE_CREATED = 0x01;
	const APP_STATE_INITIALIZING = 0x02;
	const APP_STATE_CREATED_OR_INITIALIZING = 0x03; // bitwise mix
	const APP_STATE_INITIALIZED = 0x04;
	const APP_STATE_RUNNING = 0x10;
	const APP_STATE_RUNNING_REQUEST = 0x11;
	const APP_STATE_RUNNING_RESPONSE = 0x12;
	const APP_STATE_DONE = 0x20;
	const APP_STATE_FINISHING = 0x40;
	const APP_STATE_FINISHED = 0x80;
	
	private $app_state = Application::APP_STATE_SICK;
	private $hooks = array();
	private $variableSet;
	protected $request_host;
	protected $request_uri;

	public function __construct()
	{
		parent::__construct();
		$this->variableSet = new VariableSet();
		$this->app_state = Application::APP_STATE_CREATED;
	}
	
	/**
	 * Get current application state
	 */
	public function getState()
	{
		return $this->app_state;
	}
	
	/**
	 * Check if application is in correct state, use bitwise or when multiple states are accepted 
	 * @param int $required_state
	 * @param bool $throw if will throw exception on mismatch, dafault throws
	 * @throws ApplicationException
	 */
	public function checkState($required_state = Application::APP_STATE_RUNNING, $throw = true)
	{
		if(($this->app_state & $required_state) === 0) // bitwise operation
			if($throw)
				throw new ApplicationException('Application is in state '.
					$this->app_state.' but state '.$required_state.' required');
			else 
				return false;
		return true;
	}
	
	/**
	 * register application hook
	 * @param IApplicationHook $hook
	 */
	public function registerHook(IApplicationHook $hook)
	{
		$this->checkState(Application::APP_STATE_CREATED_OR_INITIALIZING);
		$this->hooks[] = $hook;
	}
	
	protected function runHooks($method, $first_result = false)
	{
		foreach($this->hooks as $hook)
		{
			$handled = $hook->$method($this);
			if($first_result && $handled)
				return $handled;
		}
		return false;
	}
	
	/**
	 * Initialize application
	 * @param unknown_type $request_host
	 * @param unknown_type $request_uri
	 */
	public function init($request_host = null, $request_uri = null)
	{
		$this->checkState(Application::APP_STATE_CREATED);
		$this->app_state = Application::APP_STATE_INITIALIZING;

		if($request_host === null)
			$request_host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);
			
		if($request_uri === null)
			$request_uri = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null);
			
		$this->request_host = $request_host;
		$this->request_uri = $request_uri;

		register_shutdown_function(array($this, 'finish'));

		$this->runHooks('init', true);
		
		// advance to next state
		$this->app_state = Application::APP_STATE_INITIALIZED;
	}
	
	/**
	 * Start processing requests
	 */
	public function run()
	{
		$this->checkState(Application::APP_STATE_INITIALIZED);
		$this->app_state = Application::APP_STATE_RUNNING;
				
		// strip out
		$this->checkRequest();
		$this->setMimeType();
		$this->prepareTemplate();
		$result = false;
		$result |= $this->runPhpScript();
		$result |= $this->runTemplate();
		
		$result = $this->runHooks('run', true);
		
		if(!$result)
			$this->Http404();
			
		$this->app_state = Application::APP_STATE_DONE;
	}

	public function finish()
	{
		// note that finish should by called explicitly in normal flow
		// but is so called by register_shutdown_function
		
		if($this->app_state === Application::APP_STATE_FINISHED)
			return; // we fineshed correctly

		if($this->app_state !== Application::APP_STATE_FINISHING)
		{
			// TODO: Some good error/warning log mechanism should go there
			echo "\nERROR: Application not correctly finished, error ocured while finishing\n";
			return;
		}	
		if($this->app_state !== Application::APP_STATE_DONE)
		{
			// TODO: Some good error/warning log mechanism should go there
			echo "\nERROR: Application run not finish correctly\n";
		}
		
		$this->app_state === Application::APP_STATE_FINISHING;
		
		$this->runHooks('finish');
		
		$this->app_state === Application::APP_STATE_FINISHED;
	}

	public function getVariables()
	{
		return $this->variableSet;
	}
	
	// ArrayAccess - defer calls to VariableSet
	public function offsetExists ($offset) { return $this->variableSet->isVar($offset); }
	public function offsetGet ($offset) { return $this->variableSet->get($offset); }
	public function offsetSet ($offset, $value) { $this->variableSet->set($offset, $value); }
	public function offsetUnset ($offset) { $this->variableSet->unsetVar($offset); }
	
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
	
	public function showErrorPage($http_code, $path=null, $exit = true)
	{
		$http_code_name = '';
		switch ($http_code)
		{
			case 403:
				$http_code_name = 'Forbidden';
				break;
			case 404:
				$http_code_name = 'Not Found';
				break;			
		}
		header('HTTP/1.1 '.$http_code.' '.$http_code_name);
		$this->prepareErrorTemplate($http_code, $path);
		$this->runPhpErrorScript($http_code, $path);
		$this->runTemplate();
		if($exit)
			exit();
	}
	
	public function Http403($exit = true)
	{
		$this->showErrorPage(403, null, $exit);
	}
	
	public function Http404($exit = true)
	{
		$this->showErrorPage(404, null, $exit);
	}
	
	public function getExecutionTime()
	{
		return get_execution_time();
	}
}

$_GLOBALS['application'] = Application::getInstance();

