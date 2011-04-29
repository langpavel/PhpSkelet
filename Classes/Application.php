<?php

require_once __DIR__.'/../PhpSkelet.php';

class Application extends Singleton
{
	private $session;
	
	public function __construct()
    {
    	parent::__construct();
    }
	
    public static function Init()
    {
    	Application::getInstance();
    }
    
}

Application::Init();
