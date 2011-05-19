<?php

/**
 * Write permanent or temporaly redirect header and end proccesing.
 * Enter description here ...
 * @param string $target url
 * @param bool[optional] $temporaly if redirect is temporaly
 */
function redirect($target = null, $temporaly = false)
{
	if($target === null)
		$target = get_POST_GET('redirect', get_POST_GET('return_url', '..'));
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

/**
 * Check if $text start with $required
 * @param unknown_type $required
 * @param unknown_type $text
 * @return bool 
 */
function str_start_with($required, $text)
{
	return substr($text, 0, strlen($required)) === $required;	
}

/**
 * Generate unique html id
 */
function get_document_unique_id()
{
	static $counter = 0;
	return sprintf('__%x', $counter++);
}

function str_remove_dia($text)
{
	return iconv("utf-8", "ascii//TRANSLIT", $text);
}

function get_POST($name, $default=null)
{
	return isset($_POST[$name]) ? $_POST[$name] : $default; 
}

function get_GET($name, $default=null)
{
	return isset($_GET[$name]) ? $_GET[$name] : $default; 
}

function get_POST_GET($name, $default=null)
{
	return isset($_POST[$name]) ? 
		$_POST[$name] : 
		(isset($_GET[$name]) ? $_GET[$name] : $default); 
}

function get_SERVER($name, $default=null)
{
	return isset($_SERVER[$name]) ? $_SERVER[$name] : $default; 
}

function request_is_ajax()
{
	return ('xmlhttprequest' == strtolower(get_SERVER('HTTP_X_REQUESTED_WITH', '')))
		|| get_GET('ajax', false)
		|| get_GET('json', false);
}

function header_nocache()
{
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
}

/**
 * Write JSON response and exit
 */
function response_json($data)
{
	while(ob_get_level() > 0)
		ob_end_clean();
	header_nocache();
	header('Content-type: application/json');
	echo json_encode($data);
	exit;	
}

function file_backup($file, $throw=true, $rename=false, $prefix='', $suffix='.bak')
{
	if(!$file instanceof SplFileInfo)
		$file = new SplFileInfo($file);

	if($file->isDir())
		if($throw) throw new InvalidOperationException('Cannot backup directory');
		else return false;
		
	if($file->isLink())
		if($throw) throw new InvalidOperationException('Cannot backup file, file is link');
		else return false;
		
	$path = $file->getPath().'/';
	$filename = $file->getFilename();
	
	$i = 0;
	do
	{
		$newfilename = $path.$prefix.$filename.sprintf('.%04d',$i).$suffix;
		$i++;
	}
	while(is_file($newfilename));
	
	$result = $rename ? 
		rename($file->getPathname(), $newfilename) :
		copy($file->getPathname(), $newfilename);
	
	if(!$result)
		if($throw) throw new InvalidOperationException('Cannot backup file "'.$filename.'" to "'.$newfilename.'"');
	
	return $result;
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