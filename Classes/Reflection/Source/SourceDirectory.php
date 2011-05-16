<?php

class SourceDirectory extends SourceEntry implements IteratorAggregate
{
	private $entries;
	private $ignores = array();
	
	public function __construct($entry)
	{
		parent::__construct($entry);
	}
	
	public function getEntries()
	{
		if($this->entries === null)
		{
			$this->entries = $this->traverseDirectory($this->pathname);
			$this->sort();
		}

		return $this->entries;
	}

	protected function loadIgnoreFile($path)
	{
		$fname = $path.'/.skeletignore';
		$this->ignores = array('.skeletignore');
		if(is_file($fname))
		{
			$file = file($fname);
			foreach($file as $line)
			{
				$l = trim($line);
				if(!empty($l))
					$this->ignores[] = $l;
			}
		}		
	}
	
	protected function isIgnoredFile($filename)
	{
		return in_array($filename, $this->ignores);
	}
	
	private function traverseDirectory($path)
	{
		$this->loadIgnoreFile($path);
		$result = array();
		$dir = new DirectoryIterator($path);
		foreach ($dir as $entry)
		{
			if($entry->isDot() || $this->isIgnoredFile($entry->getFilename()))
				continue;
		
			$result[] = SourceEntry::create($entry);
		}
		return $result;
	}
	
	protected function sort_callback($a, $b)
	{
		$str_a = (($a instanceof SourceDirectory) ? '0' : '1') . $a->filename;
		$str_b = (($b instanceof SourceDirectory) ? '0' : '1') . $b->filename;
		if ($str_a == $str_b) 
			return 0;
    	return ($str_a < $str_b) ? -1 : 1;
	}
	
	protected function sort()
	{
		usort($this->entries, array($this, 'sort_callback'));
	}
	
	/* IteratorAggregate implementation */
	public function getIterator()
	{
		return new ArrayIterator($this->getEntries());
	}
}
