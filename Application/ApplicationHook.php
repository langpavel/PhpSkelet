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
	}
	
	function finish(Application $app)
	{
	}
}
