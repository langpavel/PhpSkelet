<?php

function redirect($target, $temporaly = false)
{
	header('Location: '.$target, true, $temporaly ? 307 : 301);
	exit();
}

/**
 * Create random string from characters in $valid_chars
 * @param int $length
 * @param string[optional] $valid_chars defaults to [0-9a-zA-Z] '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM'
 */
function str_random($length = 8, $valid_chars = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM')
{
	$result = '';
	$max = strlen($valid_chars)-1;
	for($n=0; $n < $length; $n++)
		$result .= $valid_chars[rand(0,$max)];
	return $result;
}

function str_remove_dia($text)
{
	return iconv("utf-8", "ascii//TRANSLIT", $text);
}

/*
if(!isset($GLOBALS['__STR_CAMEL_TO_UNDERSCORE']))
	$GLOBALS['__STR_CAMEL_TO_UNDERSCORE'] = array();

function str_camel_to_underscore($camelCasedId, $register_expected_result=null)
{
	$dict =& $GLOBALS['__STR_CAMEL_TO_UNDERSCORE'];
	if(isset($dict[$camelCasedId]))
		return $dict[$camelCasedId];

	if($register_expected_result !== null)
	{
		if(!is_string($register_expected_result))
			throw new InvalidArgumentException('second argument of str_camel_to_underscore() must be string');
		$dict[$camelCasedId] = $register_expected_result;
		return $register_expected_result;
	}
}
*/