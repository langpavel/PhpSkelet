<?php

/**
 *
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/../PhpSkelet.php';

function test_RecursiveDirectoryIterator($dir)
{
	$realdir = realpath($dir);
	if($realdir === false)
		throw new InvalidArgumentException('Directory not exists or not accessible: "'.$dir.'"');

	$r_dir_iter = new RecursiveDirectoryIterator($realdir,
		RecursiveDirectoryIterator::CURRENT_AS_FILEINFO |
		RecursiveDirectoryIterator::SKIP_DOTS
	);
	$recur_iter = new RecursiveIteratorIterator($r_dir_iter);

	foreach($recur_iter as $finfo)
	{
		$basename = $finfo->getBasename();
		$extensionpos = strrpos($basename, '.');
		if($extensionpos === false)
			continue;
		$extension = substr($basename, $extensionpos+1);

		if(strtolower($extension) != 'php')
			continue;

		echo "'$finfo' - $extension <br/>";
	}
}

test_RecursiveDirectoryIterator('..');
