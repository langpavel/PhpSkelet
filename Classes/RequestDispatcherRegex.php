<?php

/**
 * @author Pavel Lang (langpavel@phpskelet.org)
 */
class RequestDispatcherRegex extends RequestDispatcher
{
	private $urls = array();

	public function __construct()
	{
		parent::__construct();
	}

	public function register($url_regex, $view, $name = null)
	{
		$urls[] = array($url_regex, $view, $name);
	}

	public function dispatch(HttpRequest $request)
	{
		foreach($urls as $matchee)
		{
			$matches = array();
			if(preg_match($matchee[0], $url, $matches))
			{
				$result = $matchee[1];
				if(is_string($result))
					$result = new $result($matches);
				if($result instanceof IRequestDispatcher)
					$result = $result->dispatch($request);
				if($result instanceof IView)
					return $result;
			}
		}
		return null;
	}
}
