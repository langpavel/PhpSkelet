<?php

class SitemapEntry extends SafeObject
{
	/**
	 * URL of page. This URL must begin with the protocol (such as http) and end with a trailing slash, if your web server requires it. This value must be less than 2,048 characters.
	 * @var string
	 */
	public $loc;
	
	/**
	 * Date in format YYYY-MM-DD
	 * @var string
	 */
	public $lastmod;
	
	/**
	 * one of always, hourly, daily, weekly, monthly, yearly, never
	 * @var string
	 */
	public $changefreq;
	
	/**
	 * float from 0 to 1, default 0.5
	 * @var float
	 */
	public $priority;
	
	public function __construct($loc=null, $priority=null, $changefreq=null, $lastmod=null)
	{
		parent::__construct();
		
		if($priority === null)
			$priority = 0.5;

		$this->loc = self::normalize_loc($loc);
		$this->lastmod = $lastmod;
		$this->changefreq = $changefreq;
		$this->priority = $priority;		
	}
	
	public function write()
	{
		if($this->loc === null)
			return false;
			
		echo "\t<url>\n";
		echo "\t\t<loc>".htmlentities($this->loc)."</loc>\n";
		if($this->lastmod != null)
			echo "\t\t<lastmod>$this->lastmod</lastmod>\n";
		if($this->changefreq != null)
			echo "\t\t<changefreq>$this->changefreq</changefreq>\n";
		if($this->priority != null)
			echo "\t\t<priority>$this->priority</priority>\n";
		echo "\t</url>\n";
	}
	
	public static function normalize_loc($loc)
	{
		// TODO: implement bit pretty
		$url = parse_url($loc);
		if($url == false)
			return false;
		
		if(!isset($url['scheme']))
			$url['scheme'] = 'http';
		if(!isset($url['host']))
			$url['host'] = $_SERVER['SERVER_NAME'];
		if(isset($url['port']))
			$url['host'] .= ':'.$url['port'];
		if(isset($url['pass']))
			$url['user'] .= ':'.$url['pass'];
		if(isset($url['user']))
			$url['host'] = $url['user'].'@'.$url['host'];
		if(isset($url['query']))
			$url['path'] .= '?'.$url['query'];			
		if(isset($url['fragment']))
			$url['path'] .= '#'.$url['fragment'];			
			
		return $url['scheme'].'://'.$url['host'].$url['path'];
	}
	
}

class Sitemap extends SafeObject implements IteratorAggregate
{
	protected $entries = array();
	
	/**
	 * Add sitemap entry (do NOT rewrite)
	 * @param string $loc URL of page. This URL must begin with the protocol (such as http) and end with a trailing slash, if your web server requires it. This value must be less than 2,048 characters.
	 * @param float $priority float from 0 to 1, default 0.5
	 * @param string $changefreq one of always, hourly, daily, weekly, monthly, yearly, never
	 * @param string $lastmod Date in format YYYY-MM-DD
	 */
	public function add($loc, $priority=null, $changefreq=null, $lastmod=null)
	{
		if(!$loc instanceof SitemapEntry)
			$loc = new SitemapEntry($loc, $priority, $changefreq, $lastmod);

		if(!isset($this->entries[$loc->loc]))
			$this->entries[$loc->loc] = $loc;
	}
	
	public function getIterator()
	{
		return new ArrayIterator($this->entries);
	}
	
	public function write()
	{
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
		foreach ($this as $entry)
			$entry->write();
		echo '</urlset>';
	}
	
	public function traverse_dir($base_url, $path, $recursive=true)
	{
		$path = realpath($path).'/';
		$base_url = SitemapEntry::normalize_loc($base_url);
		$dir = new DirectoryIterator($path);
		$dirs = array();		
		foreach($dir as $d)
		{
			if($d->isfile())
			{
				$ext = substr($d, -4);
				if($ext == '.tpl' || $ext == '.php')
				{
					$file = substr($d, 0, -4);
					if($file != 'robots.txt' && $file != 'sitemap.xml')
					{
						if($file === 'index')
							$this->add($base_url);
						else
							$this->add($base_url.$file);
					}
				}
			}
			else if($recursive && $d->isdir() && !$d->isdot() && $d != 'special' && $d != 'components')
			{
				$this->traverse_dir($base_url.$d.'/', $path.$d);
			}
		}
		
	}
	
}
