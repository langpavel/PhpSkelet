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
 * For 'not implemented yet' code
 * @author langpavel
 */
final class NotImplementedException extends Exception { }

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
 * Special program-flow-control exception
 * @author langpavel
 */
abstract class ApplicationSpecialException extends ApplicationException { }

/**
 * Do not create this exception, call Application::getInstance()->done(); 
 * Inform application that execution is done
 * @author langpavel
 */
class ApplicationDoneSpecialException extends ApplicationSpecialException { }

/**
 * Base class for all application invalid operations 
 * @author langpavel
 */
class InvalidOperationException extends PhpSkeletException { }

/**
 * Thrown by SafeObject
 * @author langpavel
 */
class InvalidPropertyAccessException extends PhpSkeletException { }

/**
 * Base class for all exceptions that sends bugs to phpskelet.org website
 */
abstract class PhpSkeletBugException extends PhpSkeletException { } 

/**
 * DO NOT USE unless you're contibuting at phpskelet.org core. Use PhpSkeletCustomBugException instead  
 */
class PhpSkeletCoreBugException extends PhpSkeletBugException { } 

/**
 * Extend this class if you want track serious unpredictable errors in your code
 * at public tracking system at phpskelet.org
 */
abstract class PhpSkeletCustomBugException extends PhpSkeletBugException { }

