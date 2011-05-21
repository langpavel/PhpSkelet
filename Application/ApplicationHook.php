<?php

abstract class ApplicationHook extends SafeObject implements IApplicationHook
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function init(Application $app)
	{
	}
	
	function resolveRequest(Application $app)
	{
	}
	
	function run(Application $app)
	{
		$this->checkRequest();
		$this->setMimeType();
		$this->prepareTemplate();
		$result = false;
		$result |= $this->runPhpScript();
		$result |= $this->runTemplate();
	}
	
	function finish(Application $app)
	{
	}
}
