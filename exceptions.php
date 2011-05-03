<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

//	Some global meaning exceptions (commons to php and PhpSkelet):
//
//	 + Exception
//	 |  + LogicException - php
//	 |  |  | InvalidArgumentException - php
//	 |  |  + LengthException - php
//	 |  + RuntimeException - php
//	 |     | OutOfBoundsException - php
//	 |     | UnexpectedValueException - php
//	 |     + PhpSkeletException
//	 |        + ApplicationException
//	 |
//	 + ErrorException - DO NOT USE

// Specific exceptions used by one class should be defined 
// in same file as class that throws it itself.
// In this file should be only exceptions with global meaning 

/**
 * Base class for all PhpSkelet Framework exceptions
 * @author langpavel
 */
class PhpSkeletException extends RuntimeException { }

/**
 * Base class for all user defined PhpSkelet applications
 * @author langpavel
 */
class ApplicationException extends PhpSkeletException { }

/**
 * Base class for all application invalid operations 
 * @author langpavel
 */
class InvalidOperationException extends PhpSkeletException { }
