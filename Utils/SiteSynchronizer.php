<?php

/* 
 * Two phases:
 * 1) Get difference info
 * 2) Let's user select actions
 * 3) Create batch files in public temp directory BatchQueue using redirects
 * 4) Verify sync again 
 */

class SiteSynchronizer extends SafeObject
{
	public $hash_func = 'sha1';
	private $partner_url;
	private $pass_hash;
	private $password;
	private $exclude_names = array();
	private $exclude_full_names = array();
	private $exclude_pattern;
	
	public function __construct($pass_hash, $partner_url)
	{
		parent::__construct();
		$this->pass_hash = $pass_hash;
		$this->partner_url = $partner_url;
	}

	public function addExcludeName($exclude)
	{
		$this->exclude_names[] = $exclude;
	}
	
	public function addExcludeFullName($exclude)
	{
		$this->exclude_full_names[] = $exclude;
	}
	
	protected function isExclude($filename, $root_path)
	{
		if(in_array($filename, $this->exclude_names) 
		|| in_array($root_path, $this->exclude_full_names))
			return true;
		$ignored = ($this->exclude_pattern !== null) ? 
			preg_match($this->exclude_pattern, $root_path)
			: 0;
		if($ignored === false)
			throw new InvalidOperationException('Exclusion regex error');
		return $ignored;
	}
	
	public function setExcludeRegex($regex_pattern = null)
	{
		$this->exclude_pattern = $regex_pattern;
	}
	
	protected function prepareExcludeRegex()
	{
		if(empty($this->excludes))
			return $this->exclude_pattern = null;
		
		return $this->exclude_pattern = '%('.implode('|', $this->excludes).')%';
	}
	
	public function getFileStates()
	{
		return array(
			'dir_separator'=>DIRECTORY_SEPARATOR,
			'content'=>$this->getDirInfo()
		);
	}
	
	protected function getDirInfo($dirname = null, $trimmed_root = null)
	{
		if($dirname === null)
			$trimmed_root = $dirname = rtrim(DIR_ROOT, DIRECTORY_SEPARATOR);
			
		$result = array();

		$trimmed_root_length = strlen($trimmed_root);
		
		$iterator = new DirectoryIterator($dirname);
		foreach ($iterator as $fileinfo) 
		{
			$fname = $fileinfo->getFileName();
			if($fileinfo->isDot()) 
				continue;
			
			$full_path = $fileinfo->getPathName();
			
			if(substr($full_path, 0, $trimmed_root_length) != $trimmed_root)
				throw new Exception("trimmed_root is probably wrong: '$full_path' does not start with '$trimmed_root'");
			$root_path = substr($full_path, $trimmed_root_length);

			$ignored = $this->isExclude($fname, $root_path);
			
			$real_path = $fileinfo->getRealPath();
				
			$result[$fname] = array(
				'FileName'=>$fname,
				'RootPathName'=>$root_path,
				//'full_name'=>$full_path, // no interest
				'RealPath'=>$real_path,
				'Type'=>$fileinfo->getType(),
				'MTime'=>$fileinfo->getMTime(), // for touch()
				'isReadable'=>$fileinfo->isReadable(),
				'isWritable'=>$fileinfo->isWritable()
			);
			
			if($ignored)
			{
				$result[$fname]['ignore'] = $ignored;
				continue;
			}
			
			if($fileinfo->isDir())
			{
				$result[$fname]['isDir'] = true;
				if($fileinfo->isReadable())
				{
					if($fname == '.git' || $fname == '.svn')
					{
						$result[$fname]['_INFO'] = 'Skipped'; 
						continue;
					}
					$result[$fname]['content'] = $this->getDirInfo($full_path, $trimmed_root);
				}
			}
			else if($fileinfo->isFile())
			{
				$result[$fname]['isFile'] = true;
				$result[$fname]['size'] = $fileinfo->getSize();
				if($fileinfo->isReadable())
				{
					$result[$fname]['hash_'.$this->hash_func]=hash_file($this->hash_func, $full_path);
				}
			}
			else 
			{
				$result[$fname]['_ERR'] = 'Not file or directory';
			}
		}
		//get directory tree in array
		//contain files - file size, MTime
		
		ksort($result);
		return $result;
		
	}
	
	/*
	public function process($action = null)
	{
		$result = null;
		$this->action = $action = self::loadParam('action', null, '');
		if($action = '')
			return $this->manager();
		if(!$this->verifyPassword())
			return array('error'=>'Password incorrect');
			
		switch($action)
		{
			case '':
				$result = $this->manager();
				break;
			case 'ping_request':
				$result = $this->ping_request();
				break;
			case 'ping':
				$result = $this->ping();
				break;
		}
		if(self::loadParam('ser') !== null)
		{
			var_export($result);
			Application::getInstance()->done();
		}
		return $result;
	}

	protected function call_remote($action)
	{
		if(!$this->verifyPassword())
			return array('error'=>'Password incorrect');
		$result = file_get_contents("$this->partner_url?ser=1&action=$action&pass=$this->password");
		dd($result);
		//return eval();
	}
	
	public function ping()
	{
		return $this->call_remote('ping_request');
	}
	
	public function ping_request()
	{
		return array(
			'action'=>'ping',
			'password_ok'=>$this->verifyPassword(),
			'remote_server'=>$_SERVER
		);		
	}
	*/
	
}

