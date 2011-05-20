<?php

/**
 * Super administrator - unlimited access
 * @author langpavel
 */
class SuperAdmin extends User
{
	public function isAnonymous()
	{
		return false;
	}

	public function getNick()
	{
		return 'administrator';
	}
	
	public function queryPermission($permission_name)
	{
		return true;
	}
}

