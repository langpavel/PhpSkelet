<?php

/**
 *
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

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

		$cls = isset($t['class']) ? $t['class'] : '';
		$obj = isset($t['object']) ? dd_var($t['object']) : '';
		$type = isset($t['type']) ? dd_var($t['type']) : '';
		
		echo '<tr style="background-color:#ffa;">';
		echo '<td style="font-family:Monospace; font-size:smaller;">'.($cnt+$skip).'</td>';
		echo '<td style="font-family:Monospace;">'.$f.'</td>';
		echo '<td style="font-family:Monospace;">'.$t['line'].'</td>';
		echo '<td style="font-family:Monospace;">'.$t['file'].'</td>';
		echo '<td style="font-family:Monospace;">'.$cls.'</td>';
		echo '<td>'.$obj.'</td>';
		echo '<td>'.$type.'</td>';
		echo '<td style="font-family:Monospace;">('.dd_array_vals($t['args']).')</td>';
		echo '</tr>';
	}
	echo '</tr></tbody></table>';
}

/**
 * Debug Dump
 */
function dd()
{
	$d = Debug::getInstance();
	
//	$trace = debug_backtrace();
//	dd_trace(0, $trace);

	$result = '';
	
	$args = func_get_args();
	if(empty($args))
	{
		$btrc = debug_backtrace();
		if(!isset($btrc[1]))
			throw new Exception('dd() called from root scope without arguments');
		$args = $btrc[1]['args'];
		$result = '<span style="font-family:Monospace;">function '. $btrc[1]['function'].'('. $d->dump_array_comma($args) .');</span>';
	}
	else
	{
		$result = '<span style="font-family:Monospace;">dd('. $btrc[1]['function'].'('. $d->dump_array_comma($args) .')</span>';		
	}

	echo $result;
	
	exit;
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
