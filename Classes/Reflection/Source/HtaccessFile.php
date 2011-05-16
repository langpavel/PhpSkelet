<?php

require_once __DIR__.'/SourceFile.php';

class HtaccessFile extends SourceFile
{
	public function __construct($entry, $extension=null)
	{
		parent::__construct($entry, $extension, 'htaccess');
	}
	
	private function highlightOptions($line)
	{
		return preg_replace(
			array(
				'/(Options)/',
				'/(-\\w+)/',
				'/(\\+\\w+)/',
			),
			array(
				'<span class="keyword">${1}</span>', 
				'<span class="opt_remove">${1}</span>', 
				'<span class="opt_add">${1}</span>', 
			), htmlspecialchars($line));
	}
	
	public function getHighlightedSource()
	{
		echo '<code class="source">';
		$lines = file($this->pathname);
		foreach($lines as $line)
		{
			$htmlline = htmlspecialchars($line);
			$trimline = trim($line);
			if($trimline == '')
			{
				// epty line - do nothing
			}
			else if($trimline[0] == '#')
			{
				// comment line
				$htmlline = '<span class="comment">'.$htmlline.'</span>';
			}
			else 
			{
				$tokens = preg_split("/[\s,]+/", $trimline);
				if($tokens[0] == 'Options')
					$htmlline = $this->highlightOptions($line);
				else 
					$htmlline = preg_replace(array(
						'!(/.*/[\\w\\.\\?%$-_&]*)!',
						'/(Rewrite(Engine|Rule|Cond))/',
						'/((%(\\{\\w+\\}|\d+))|\\$\d+)/',
					), array(
						'<span class="location">${1}</span>',
						'<span class="keyword">${1}</span>',
						'<span class="var">${1}</span>',
					), $htmlline);
			}
			
			echo $htmlline;
		}
		echo '</code>';
	}
}
