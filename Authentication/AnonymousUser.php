<?php

/**
 * Singleton class for anonymous user
 * @author langpavel
 */
class AnonymousUser extends User
{
	private static $instance = null;
	
	public static function getInstance()
	{
		if(AnonymousUser::$instance === null)
			AnonymousUser::$instance = new AnonymousUser();
		return AnonymousUser::$instance;
	}
		
	public function isAnonymous()
	{
		return true;
	}

	public function getNick()
	{
		return null;
	}
	
	public function queryPermission($permission_name)
	{
		return false;
	}
	
}
