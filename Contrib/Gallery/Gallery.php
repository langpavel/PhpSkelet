<?php

class Gallery extends SafeObject implements IteratorAggregate
{
	public $images = array();

	public function __construct($gallery_dir = null, $url_path = null)
	{
		parent::__construct();
		if($gallery_dir !== null && $url_path !== null)
			$this->loadImages($gallery_dir, $url_path);
	}

	protected function _sort_callback($a, $b)
	{
		$a = $a->url; $b = $b->url;
    	if ($a == $b) { return 0; }
    	return ($a < $b) ? -1 : 1;
	}

	protected function sort() {	usort($this->images, array($this, '_sort_callback')); }

	public function loadImages($gallery_dir, $url_path)
	{
		$dir = new DirectoryIterator($gallery_dir);
		foreach($dir as $file)
		{
			if($file->isFile())
			{
				$img = GalleryImage::tryCreate($file, $url_path);
				if($img)
					$this->images[] = $img;
			}
		}
		$this->sort();
	}

	public function exclude($file)
	{
		$this->images = array_values($this->images);
		$l = count($this->images);
		for($i=0; $i < $l; $i++)
		{
			$img = $this->images[$i];
			if($img && ($img->pathinfo['filename'] == $file)
			|| ($img->pathinfo['basename'] == $file))
			{
				unset($this->images[$i]);
				return;
			}
		}
	}

	public function loadImage($file, $url_path)
	{
		if(!$file instanceof SplFileInfo)
			$file = new SplFileInfo($file);
		$img = GalleryImage::tryCreate($file, $url_path);
		if(!$img)
			return false;
		return $this->images[] = $img;
	}

	public function getIterator () { return new ArrayIterator($this->images); }
}
