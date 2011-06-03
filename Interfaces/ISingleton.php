<?php

/**
 * Base interface for all singleton designed classes
 * 
 * This should seems curious, but it have a sense as hint.
 */
interface ISingleton
{
	/**
	 * get instance of current singleton
	 */
	public static function getInstance();
}
