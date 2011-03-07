<?php

/**
 *
 * This file is part of the Morpheus Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the Morpheus/LGPL license.
 */

function dd_array_vals($array)
{
	$result = array();
	foreach($array as $v)
	{
		$result[] = dd_var($v);
	}
	return implode(', ', $result);
}

function dd_array(array $array)
{
	$result = '<a href="javascript:alert(\'test\')">array</a>';
	return $result;
}

function dd_object($object)
{
	$result = '<a href="javascript:alert(\'test\')">object</a>';
	$result .= dd_array((array)$object);
	return $result;
}

function dd_var($var)
{
	if($var === null)
		return '<span style="color:#800; font-family:Monospace; font-weight:bolder;">null</span>';

	if(is_bool($var))
		return '<span style="color:#800; font-family:Monospace; font-weight:bolder;">'.($var ? 'true' : 'false').'</span>';

	if(is_string($var))
		return '<span style="color:#050; font-family:Monospace;">'.var_export($var, true).'</span>';

	if(is_int($var))
		return '<span style="color:#008; font-family:Monospace; font-weight:bolder;">'.var_export($var, true).'</span>';

	if(is_array($var))
		return dd_array($var);

	if(is_object($var))
		return dd_object($var);

	return '???';
}

function dd_trace($skip = 0, $trace = null)
{
	if($trace === null)
		$trace = debug_backtrace();

	echo '<table style="border:1px #888 solid;"><thead>';
	echo '<tr>';
	echo '<th> </th>';
	echo '<th>Function</th>';
	echo '<th>Line</th>';
	echo '<th>File</th>';
	echo '<th>Class</th>';
	echo '<th>Object</th>';
	echo '<th>Type</th>';
	echo '<th>Args</th>';
	echo '</tr></thead><tbody>';

	$cnt = count($trace) - $skip + 1;
	foreach($trace as $t)
	{
		$f=$t['function'];
		$skip--;
		if($skip >= 0)
		{
			continue;
		}
		/*elseif($skip == -1)
		{
			$f = '<span style="color:#f00;">*</span>';
		}*/

		echo '<tr style="background-color:#ffa;">';
		echo '<td style="font-family:Monospace; font-size:smaller;">'.($cnt+$skip).'</td>';
		echo '<td style="font-family:Monospace;">'.$f.'</td>';
		echo '<td style="font-family:Monospace;">'.$t['line'].'</td>';
		echo '<td style="font-family:Monospace;">'.$t['file'].'</td>';
		echo '<td style="font-family:Monospace;">'.@$t['class'].'</td>';
		echo '<td>'.@dd_var($t['object']).'</td>';
		echo '<td>'.@dd_var($t['type']).'</td>';
		echo '<td style="font-family:Monospace;">('.dd_array_vals($t['args']).')</td>';
		echo '</tr>';
	}
	echo '</tr></tbody></table>';
}

/**
 * Debug Dump, die after dump
 */
function dd()
{
	$trace = debug_backtrace();
	dd_trace(0, $trace);

	$args = func_get_args();
	if(count($args) == 0)
		@$args = $trace[1]['args'] ?: array();
	if(count($args) != 0)
	{
		foreach($args as $k=>$v)
		{
			echo "<h1>$k</h1>";
			highlight_string("<?php args[$k] =\n".var_export($v, true));
		}
	}
	die;
}

/**
 * Debug Dump NO die after dump
 */
function ddd()
{
	$trace = debug_backtrace();
	dd_trace(0, $trace);

	$args = func_get_args();
	if(count($args) == 0)
		$args = $trace[1]['args'] ?: array();
	foreach($args as $k=>$v)
	{
		echo "<div>Debug $k<br/>";
		highlight_string("<?php args[$k] =\n".var_export($v, true));
		echo '</div>';
	}
}
