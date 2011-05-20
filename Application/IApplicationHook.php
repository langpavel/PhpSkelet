<?php

interface IApplicationHook
{
	function init(Application $app);
	function resolveRequest(Application $app);
	function run(Application $app);
	function finish(Application $app);
}