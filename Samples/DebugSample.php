<?php

/**
 * Debugging sample
 *
 * This file is part of the PhpSkelet Framework.
 *
 * @copyright Copyright (c) 2011 Pavel Lang (langpavel at gmail dot com)
 * @license This source file is subject to the PhpSkelet/LGPL license.
 */

require_once __DIR__.'/../Classes/Debug.php';

$d = Debug::getInstance();
$d->registerErrorHandlers();

$val = true;
echo '<div>true: '.$d->dump($val).'</div>';

$val = false;
echo '<div>false: '.$d->dump($val).'</div>';

$val = null;
echo '<div>null: '.$d->dump($val).'</div>';

$val = 1;
echo '<div>int 1: '.$d->dump($val).'</div>';

$val = 3.1415927;
echo '<div>float 3.1415927: '.$d->dump($val).'</div>';

$val = array(true, false, null, 1, 2, 3, 'test2', '555', array(1,2,3));
echo '<div>Array: '.$d->dump($val).'</div>';

//$val['this_is_recursive'] = &$val;
//echo '<div>'.$d->dump($val).'</div>';

$val = $d;
echo '<div>Class: '.$d->dump($val).'</div>';

$val = get_declared_classes();
echo '<div>get_declared_classes(): '.$d->dump($val).'</div>';

$val = get_declared_interfaces();
echo '<div>get_declared_interfaces(): '.$d->dump($val).'</div>';

echo '<div>Now we try to use unset variable:</div>';

echo $unset_variable_usage;

echo '<div>And now we throw exception:</div>';

throw new Exception("Test exception");

ReallyNotExistingClass::reallyNotExistingStaticMethod();

echo "This is not executed";
