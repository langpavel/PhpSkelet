<?php

/**
 * Abstract for permission resolver
 * @author langpavel
 */
interface IPermissionResolver
{
	/**
	 * Resolve permission.
	 * @param string $permission
	 * @return bool
	 */
	abstract public function queryPermission($permission_name);
}