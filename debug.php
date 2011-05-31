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
	$d = Debug::getInstance();
	
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

		$cls = isset($t['class']) ? $t['class'] : '';
		$obj = isset($t['object']) ? $d->dump($t['object']) : '';
		$type = isset($t['type']) ? $d->dump($t['type']) : '';
		
		echo '<tr style="background-color:#ffa;">';
		echo '<td style="font-family:Monospace; font-size:smaller;">'.($cnt+$skip).'</td>';
		echo '<td style="font-family:Monospace;">'.$f.'</td>';
		echo '<td style="font-family:Monospace;">'.(isset($t['line']) ? $t['line'] : '').'</td>';
		echo '<td style="font-family:Monospace;">'.(isset($t['file']) ? $t['file'] : '').'</td>';
		echo '<td style="font-family:Monospace;">'.$cls.'</td>';
		echo '<td>'.$obj.'</td>';
		echo '<td>'.$type.'</td>';
		if(isset($t['args']))
		{
			if(is_array($t['args']))
				echo '<td style="font-family:Monospace;">('.$d->dump_array_comma($t['args']).')</td>';
			else 
				echo '<td style="font-family:Monospace;">('.$d->dump($t['args']).')</td>';
		}
		else 
			echo '<td style="font-family:Monospace;"> </td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}

/**
 * Debug Dump with backtrace
 */
function dd()
{
	$d = Debug::getInstance();

	$result = '';
	
	$args = func_get_args();
	$result = '<span style="font-family:Monospace;">dd('. $d->dump_array_comma($args) .')</span>';		
	echo $result;
}

/**
 * Debug Dump with backtrace
 */
function ddd()
{
	$d = Debug::getInstance();

	$result = '';
	
	$args = func_get_args();
	$btrc = debug_backtrace();
	if(empty($args))
	{
		if(!isset($btrc[1]))
			$result = '<span style="font-family:Monospace;">function dd();</span>';
		else 
		{
			$args = $btrc[1]['args'];
			$result = '<span style="font-family:Monospace;">function '. $btrc[1]['function'].'('. $d->dump_array_comma($args) .');</span>';
		}
	}
	else
	{
		$result = '<span style="font-family:Monospace;">dd('. $d->dump_array_comma($args) .')</span>';		
	}

	echo $result;
	
	dd_trace(0, $btrc);
}

