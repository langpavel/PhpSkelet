<?php

abstract class User extends SafeObject implements IUser
{
	private static $currentUser = null;
	
	protected static function setCurrent(IUser $user)
	{
		User::$currentUser = $user;		
	}
	
	public static function getCurrent()
	{
		if($currentUser === null)
			$currentUser = AnonymousUser::getInstance();
	}
	
	public function isAnonymous()
	{
		return false;
	}
}
