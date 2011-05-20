<?php

require_once __DIR__.'/SourceEntry.php';

class SourceFile extends SourceEntry
{
	public $extension;
	public $subtype;
	public $mimeType;
	
	public function __construct($entry, $extension=null, $subtype=null)
	{
		parent::__construct($entry);
		if($extension === null)
			$extension = pathinfo($this->pathname, PATHINFO_EXTENSION);
		$this->extension = $extension;
		if($subtype === null)
			$subtype = get_class($this);
		$this->subtype = $subtype;

		try {
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	    	$this->mimeType = finfo_file($finfo, $this->pathname);
			finfo_close($finfo);
		}
		catch (Exception $err)
		{
			$this->mimeType = 'application/octet-stream';
		}
	}
	
	public function getContent()
	{
		return file_get_contents($this->pathname);
	}
	
	public function getFileHash($algo = 'sha256')
	{
		return hash_file($algo, $this->pathname);
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
