<?php

class GalleryImage extends SafeObject
{
	private $pathname;
	public $pathinfo;
	private $width;
	private $height;

	public $base_url;
	public $url;
	public $alt;

	public static function tryCreate(SplFileInfo $file, $url_path)
	{
		if(!$file->isFile())
			return false;

		return new GalleryImage($file, $url_path);
	}

	public function __construct($file, $url_path)
	{
		parent::__construct();

		if(!$file instanceof SplFileInfo)
			$file = new SplFileInfo($file);

		$this->base_url = $url_path;
		$this->url = $url_path.($file->getFileName());
		$this->alt = $file->getFileName();

		$this->pathname = $file->getPathName();
		$this->pathinfo = pathinfo($this->pathname);
		//$this->extension = $file->get
	}

	public function getWidth()
	{
		return 'auto';
	}

	public function getHeight()
	{
		return 'auto';
	}

	private function getThumbDir($width, $height, $effect)
	{
		if($effect === null)
			return '__'.$width.'x'.$height;
		$effect = str_replace(array('#','-','+',':'), '', $effect);
		return '__'.$effect.'_'.$width.'x'.$height;
	}

	public function getThumb($width, $height, $effect=null, $extension=null)
	{
		if($effect === null)
			$effect = 'thumb';
		if($extension === null)
			$extension = 'png';

		$thumb_dir = $this->getThumbDir($width, $height, $effect);
		$thumbpathname = $this->pathinfo['dirname'].'/'.$thumb_dir;

		if(!is_dir($thumbpathname))
			return $this->createThumb($width, $height, $effect, $extension);

		$thumbpathname .= '/'.$this->pathinfo['filename'].'.'.$extension;
		if(is_file($thumbpathname))
			return new GalleryImage($thumbpathname, $this->base_url.$thumb_dir.'/');
		else
			return $this->createThumb($width, $height, $effect, $extension);
	}

	private function createThumb($width, $height, $effect, $extension)
	{
		if($effect === null)
			$effect = 'thumb';

		$thumb_dir = $this->getThumbDir($width, $height, $effect);
		$thumbpathname = $this->pathinfo['dirname'].'/'.$thumb_dir;
		if(!is_dir($thumbpathname))
		{
			mkdir($thumbpathname, 0775);
			chmod($thumbpathname, 0775);
		}
		$thumbpathname .= '/'.$this->pathinfo['filename'].'.'.$extension;

		$imagick = new Imagick($this->pathname);

		$blur = 0.75;
		$filter = Imagick::FILTER_GAUSSIAN;

		$args = explode(':', $effect);

		switch($args[0])
		{
			case 'thumb':
				$fit = in_array('fit', $args) && ($width != 0 && $height != 0);
				$imagick->resizeimage($width, $height, $filter, $blur, $fit);
				break;
			case 'photo':
				$fit = in_array('fit', $args) && ($width != 0 && $height != 0);

				$imagick->resizeimage($width * 2, $height * 2, $filter, $blur, $fit);

				$shadowcolor = isset($args[1]) ? $args[1] : '#000';

				$imagick->setImageBackgroundColor(new ImagickPixel($shadowcolor));
				$imagick->polaroidimage(new ImagickDraw(), rand(-40, 40) / 10.0);

				$imagick->resizeimage($width, $height, $filter, $blur, $fit);
				break;
			default:
				throw new InvalidArgumentException("Effect '$effect' is unknown for thumb function");
		}
		$imagick->writeimage($thumbpathname);

		return new GalleryImage($thumbpathname, $this->base_url.$thumb_dir.'/');
	}

}
