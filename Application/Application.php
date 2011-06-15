<?php

require_once __DIR__.'/../PhpSkelet.php';

final class Application extends SafeObject implements ArrayAccess
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

	private static $instance = null;
	private $app_state = Application::APP_STATE_SICK;
	private $hooks = array();
	private $variableSet;
	protected $request_host;
	protected $request_uri;

	/**
	 * Get Application instance
	 * @return Application
	 */
	public static function getInstance()
	{
		return (Application::$instance === null) ?
			Application::$instance = new Application() :
			Application::$instance;
	}

	protected function __construct()
	{
		parent::__construct();
		$this->variableSet = new \VariableSet();
		$this->app_state = Application::APP_STATE_CREATED;
	}

	protected function __clone()
	{
		throw new InvalidOperationException('Application cannot be cloned');
	}

	public function getRequestUri()
	{
		return $this->request_uri;
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
		if($this === null)
			return false;

		try
		{

			foreach($this->hooks as $hook)
			{
				$handled = $hook->$method($this);
				if($first_result && $handled)
					return $handled;
			}
			return !$first_result;
		}
		catch(ApplicationDoneSpecialException $exception)
		{
			return true;
		}
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
		{
			$request_uri = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null);
			$qmark = strpos($request_uri, '?');
			if($qmark > 0)
				$request_uri = substr($request_uri, 0, $qmark);
		}

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
			return true; // we fineshed correctly

		if($this->app_state === Application::APP_STATE_FINISHING)
		{
			// TODO: Some good error/warning log mechanism should go there
			echo "\nERROR: Application not correctly finished, error ocured while finishing\n";
			return false;
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

	public function done()
	{
		throw new ApplicationDoneSpecialException();
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
		header('Content-Type: text/html');

		$errtext = "HTTP $http_code $http_code_name";
		echo "<html>\n<head><title>$errtext</title></head>\n";
		echo "<body>\n<h1>$errtext</h1>\n";
		echo "<p>You see this page because your request is probably invalid.</p>";
		echo "</body></html>";
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

	// ArrayAccess - defer calls to VariableSet
	public function offsetExists ($offset) { return $this->variableSet->isVar($offset); }
	public function offsetGet ($offset) { return $this->variableSet->get($offset); }
	public function offsetSet ($offset, $value) { $this->variableSet->set($offset, $value); }
	public function offsetUnset ($offset) { $this->variableSet->unsetVar($offset); }

}

$_GLOBALS['application'] = Application::getInstance();
