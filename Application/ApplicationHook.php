<?php

abstract class ApplicationHook extends SafeObject implements IApplicationHook
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function init(Application $app)
	{
		return true;
	}
	
	function resolveRequest(Application $app)
	{
		return false;
	}
	
	function run(Application $app)
	{
		return true;
	}
	
	function finish(Application $app)
	{
		return true;
	}
}
