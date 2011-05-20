<?php

/**
 * Abstract
 * @author langpavel
 *
 */
interface IUser extends IPermissionResolver
{
	public function isAnonymous();

	/**
	 * Get URL safe nickname
	 * @return
	 */
	public function getNick();
}
