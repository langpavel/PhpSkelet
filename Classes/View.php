<?php

/**
 *
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 * @tag "Core classes"
 */
abstract class View extends HttpResponse implements IView
{
	private $request;

	public function __construct(HttpRequest $request)
	{
		parent::__construct();
		$this->request = $request;
	}
}
