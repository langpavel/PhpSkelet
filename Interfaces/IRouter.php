<?php

interface IRouter
{
	/**
	 * get IOutputStream of resource 
	 * @param ResourcePath $url
	 */
	function getResource(ResourcePath $resource_path);
}