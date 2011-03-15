<?php

/**
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

echo '<?xml version="1.0" encoding="utf-8" ?>';

?>

<html>
<head>
<title>Autoloader generator</title>
</head>
<body>
<h1>Autoloader generator</h1>
<?php

require_once('../Classes/Reflection/AutoloaderGenerator.php');

$generator = new AutoloaderGenerator();
$generator->addPath('..', true);
$generator->process();
highlight_string($generator);
$filename = '../generated_code/autoloader.php';
$result = $generator->writeFile($filename);
if($result)
{
	if($result === true)
		echo '<p>File "'.$filename.'" is up to date</p>';
	else
		echo '<p>File "'.$filename.'" was saved</p>';
}

require_once('../Classes/Debug.php');

echo Debug::getInstance()->dump($generator);

?>
</body>
</html>