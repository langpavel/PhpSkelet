<?php

/**
 *
 * This file is part of the Morpheus Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the Morpheus/LGPL license.
 */

require_once('../Morpheus.php');

class PHPSourceReader extends SafeObject
{
	private $filename;
	private $tokens;

	public function __construct($filename)
	{
		parent::__construct();

		if(!is_file($filename))
			throw new MorpheusException("Requested codebase file '$filename' not found");
		
		$this->filename = $filename;
		$this->tokens = token_get_all(file_get_contents($filename));
	}

	/**
	 * Primary use of this is for AutoloaderGenerator class
	 * @return array of associative arrays with keys name, type(class/interface), 'implements', 'extends', 'final', 'abstract'
	 */
	public function findDefinedClasses()
	{
		$class_match_proto = array('name'=>null, 'type'=>'class', 'extends'=>array(), 'implements'=>array(), 'final'=>false, 'abstract'=>false);
		$result = array();
		$class = $class_match_proto;
		$detected = 0;
		foreach($this->tokens as $wholetoken)
		{
			if(is_array($wholetoken))
				list($token, $text) = $wholetoken;
			else
				$token = $text = $wholetoken;

			// process tokens
			if($token === T_COMMENT)
				continue;

			if($token === T_FINAL)
				$class['final'] = true;

			if($token === T_ABSTRACT)
				$class['abstract'] = true;

			if($token === T_CLASS)
				$detected = 1;

			if($token === T_INTERFACE)
			{
				$detected = 1;
				$class['type'] = 'interface';
			}

			if($detected === 1 && $token === T_STRING)
			{
				$class['name'] = $text;
				$detected = 2;
			}

			if($detected >= 2 && $token === T_EXTENDS)
			{
				$detected = 3;
				$key = 'extends';
			}

			if($detected >= 2 && $token === T_IMPLEMENTS)
			{
				$detected = 3;
				$key = 'implements';
			}

			if($detected === 3 && $token === T_STRING)
			{
				$class[$key][] = $text;
			}

			if($token === ';' || $token === '{')
			{
				if($detected > 0)
				{
					$detected = 0;
					$result[] = $class;
					$class = $class_match_proto;
				}
				$class['final'] = false;
				$class['abstract'] = false;
			}

		}
		return $result;
	}

//	TODO: function findDefinedFunctions()
//	public function findDefinedFunctions()
//	{
//		$function_match_proto = array('name'=>null, 'type'=>'function', 'extends'=>array(), 'implements'=>array(), 'final'=>false, 'abstract'=>false);
//		$result = array();
//		$function = $function_match_proto;
//		$detected = 0;
//		foreach($this->tokens as $wholetoken)
//		{
//			if(is_array($wholetoken))
//				list($token, $text) = $wholetoken;
//			else
//				$token = $text = $wholetoken;
//				
//			// process tokens
//			if($token === T_COMMENT)
//				continue;
//			
//			switch($token)
//			{
//				case T_FINAL:
//					break;
//
//				case T_ABSTRACT:
//					break;
//
//				case T_CLASS:
//					break;
//
//				case T_INTERFACE:
//					break;
//
//				case T_FUNCTION:
//					break;
//
//			}
//		}
//	}
	
}