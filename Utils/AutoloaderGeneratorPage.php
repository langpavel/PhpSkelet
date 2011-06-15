<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

header('Content-Type: text/html');

echo '<?xml version="1.0" encoding="utf-8" ?>';

?>

<html>
<head>
<title>Autoloader generator</title>
</head>
<body>
<h1>Autoloader generator</h1>
<?php

require_once __DIR__.'/../Reflection/AutoloaderGenerator.php';

$generator = new AutoloaderGenerator();

if(isset($GLOBALS['generator_paths']))
{
	foreach($GLOBALS['generator_paths'] as $value)
	{
		if(is_array($value))
			$generator->addPath($value[0], $value[1]);
		else
			$generator->addPath($value, true);
	}
}
else
	$generator->addPath(__DIR__.'/..', true);

$generator->process();
highlight_string($generator);
$filename = realpath(__DIR__.'/../generated_code/').'/autoloader.php';
$result = $generator->writeFile($filename);
if($result)
{
	if($result === true)
		echo '<p>File "'.$filename.'" is up to date</p>';
	else
		echo '<p>File "'.$filename.'" was saved</p>';
}

require_once __DIR__.'/../Classes/Debug.php';

echo Debug::getInstance()->dump($generator);

?>
</body>
</html>