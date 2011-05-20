<?php

require_once __DIR__.'/../../PhpSkelet.php';
require_once __DIR__.'/SourceFile.php';
require_once __DIR__.'/SourceDirectory.php';
require_once __DIR__.'/PhpSourceFile.php';

class SourceEntry extends SafeObject
{
	public $filename;
	public $pathname;
	public $realpath;
	public $isHidden;
	public $isReadable;
	public $isWritable;
	public $canDelete;
	public $type;
	public $modifyTime;
	public $errors = array();
	
	protected function __construct(SplFileInfo $entry)
	{
		parent::__construct();
		//if(!$entry instanceof SplFileInfo)
		//	$entry = new SplFileInfo($entry);

		$this->refresh($entry);
	}
	
	protected function refresh($entry = null)
	{
		if($entry === null)
			$entry = new SplFileInfo($this->pathname);
		if(!$entry instanceof SplFileInfo)
			$entry = new SplFileInfo($entry);

		$this->pathname = $entry->getPathname(); 
		$this->filename = $entry->getFilename();
		$this->realpath = $entry->getRealPath();
		$this->isReadable = $entry->isReadable();
		$this->isWritable = $entry->isWritable();
		try {
			$this->modifyTime = $entry->getMTime();
		}
		catch (Exception $err)
		{
			$this->modifyTime = 'ERROR';
			$this->errors[] = $err;
		}
		
		try {
			$this->type = $entry->getType();
			if($this->type == 'link')
				$this->type .= ($entry->isDir() ? ' dir' : ($entry->isFile() ? ' file' : ' unknown'));
		}
		catch (Exception $err)
		{
			$this->type = 'ERROR';
			$this->errors[] = $err;
		}
		$this->canDelete = $this->isWritable;
		$this->isHidden = $this->filename[0] == '.';
	}
	
	/**
	 * Virtual constructor for SourceEntry subclasses
	 * @param SplFileInfo $entry path of realized file
	 */
	public static function create($entry)
	{
		if(!$entry instanceof SplFileInfo)
			$entry = new SplFileInfo($entry);
		
		//if($entry->isDot())
		//	return null;
		if($entry->isDir())
			return new SourceDirectory($entry);

		$filename = $entry->getFilename();
			
		if($filename == '.htaccess')
			return new HtaccessFile($entry);
			
		$extension = pathinfo($entry->getPathname(), PATHINFO_EXTENSION);
		switch($extension)
		{
			case 'php': 
				return new PhpSourceFile($entry); 
			case 'tpl': 
				return new TplSourceFile($entry); 
			default: 
				return new SourceFile($entry); 
		}
	}
	
}
