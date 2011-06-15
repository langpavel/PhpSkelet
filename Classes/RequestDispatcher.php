<?php
/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

/**
 * Base abstract class for request to view dispatchers
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 */
abstract class RequestDispatcher extends SafeObject
{
	private static $rootDispatcher = null;

	public static function getRootDispatcher($create = false, $check_type = true)
	{
		$result = self::$rootDispatcher;
		if($result === null)
		{
			if($create)
				return self::$rootDispatcher = new static();
			if($check_type)
				throw new ApplicationException("Root request dispatcher is not created");
		}
		elseif($check_type && (!$result instanceof static))
		{
			$requested = get_called_class();
			$instantiated = get_class($result);
			throw new ApplicationException("Root request dispatcher is of different type ($requested requested but $instantiated found)");
		}
		return $result;
	}

	/**
	 * @var array stores resolved arguments
	 */
	protected $resolved_arguments = array();

	/**
	 * Initialize request dispatcher
	 * @param array $resolved_arguments already resolved arguments (key=>value)
	 */
	protected function __construct(array $resolved_arguments = null)
	{
		parent::__construct();
		if($resolved_arguments !== null)
			$this->resolved_arguments = $resolved_arguments;
	}

	/**
	 * Try resolve appropriate view corresponding to passed http request
	 * @param HttpRequest $request
	 */
	public abstract function dispatch(HttpRequest $request);
}

