<?php

require_once  __DIR__.'/PhpSkelet.php';

dd();

$d = Debug::getInstance();

function a()
{
	dd();
}

function b()
{
	return a(func_get_args());
}

a('test',1.2,5,array('a'=>5,5=>'a'),'endtest');

dd(ini_get_all());

$app = ApplicationController::getInstance();

dd($app);

$app->run();
