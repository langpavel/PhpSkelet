<?php

require_once __DIR__.'/SourceEntry.php';

class SourceFile extends SourceEntry
{
	public $extension;
	public $subtype;
	
	public function __construct($entry, $extension=null, $subtype=null)
	{
		parent::__construct($entry);
		if($extension === null)
			$extension = pathinfo($this->pathname, PATHINFO_EXTENSION);
		$this->extension = $extension;
		if($subtype === null)
			$subtype = get_class($this);
		$this->subtype = $subtype;
	}
	
	public function getContent()
	{
		return file_get_contents($this->pathname);
	}
	
	public function getHighlightedSource()
	{
		echo '<code class="source">'."\n";
		echo htmlspecialchars($this->getContent());
		echo "\n</code>";
	}
	
	public function backupAndWriteContent($content)
	{
		file_backup($this->pathname, true, true);
		file_put_contents($this->pathname, $content);
	}
	
	public function delete()
	{
		if(!$this->canDelete)
			return false;
		return unlink($this->pathname);
	}
}

class TplSourceFile extends SourceFile
{
}

