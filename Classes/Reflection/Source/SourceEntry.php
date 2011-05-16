<?php

require_once __DIR__.'/../../../PhpSkelet.php';
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
	
	protected function __construct(SplFileInfo $entry)
	{
		parent::__construct();
		//if(!$entry instanceof SplFileInfo)
		//	$entry = new SplFileInfo($entry);

		$this->filename = $entry->getFilename();
		$this->pathname = $entry->getPathname(); 
		$this->realpath = $entry->getRealPath();
		$this->isReadable = $entry->isReadable();
		$this->isWritable = $entry->isWritable();
		try {
			$this->type = $entry->getType();
			if($this->type == 'link')
				$this->type .= ($entry->isDir() ? ' dir' : ($entry->isFile() ? ' file' : ' unknown'));
		}
		catch (Exception $err)
		{
			$this->type = 'err';
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
