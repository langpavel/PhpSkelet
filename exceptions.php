<?php

/**
 * This file is part of the Morpheus Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the Morpheus/LGPL license.
 */

require_once('Morpheus.php');

//	Some global meaning exceptions (commons to php and Morpheus):
//
//	 + Exception
//	 |  + LogicException - php
//	 |  |  | InvalidArgumentException - php
//	 |  |  + LengthException - php
//	 |  + RuntimeException - php
//	 |     | OutOfBoundsException - php
//	 |     | UnexpectedValueException - php
//	 |     + MorpheusException
//	 |        + ApplicationException
//	 |
//	 + ErrorException - DO NOT USE

// Specific exceptions used by one class should be defined 
// in same file as class that throws it itself.
// In this file should be only exceptions with global meaning 

/**
 * Base class for all Morpheus Framework exceptions
 * @author langpavel
 */
class MorpheusException extends RuntimeException { }

/**
 * Base class for all user defined Morpheus applications
 * @author langpavel
 */
class ApplicationException extends MorpheusException { }
