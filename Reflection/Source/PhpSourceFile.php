<?php

require_once __DIR__.'/SourceFile.php';

class PhpSourceFile extends SourceFile
{
	private $tokens = null;

	public function __construct($filename, $extension=null)
	{
		if(!$filename instanceof SplFileInfo)
			$filename = new SplFileInfo($filename);
		
		parent::__construct($filename, $extension/*, 'php'*/);
	}
	
	public function getTokens()
	{
		if($this->tokens !== null)
			return $this->tokens;
		
		if(!is_file($this->pathname))
			throw new PhpSkeletException("Requested codebase file '".$this->pathname."' not found");
		
		return $this->tokens = token_get_all(file_get_contents($this->pathname));
	}

	/**
	 * Primary use of this is for AutoloaderGenerator class
	 * @return array of associative arrays with keys name, type(class/interface), 'implements', 'extends', 'final', 'abstract'
	 */
	public function findDefinedClasses()
	{
		$class_match_proto = array('name'=>null, 'type'=>'class', 'extends'=>array(), 'implements'=>array(), 'final'=>false, 'abstract'=>false);
		$result = array();
		$class = $class_match_proto;
		$detected = 0;
		$tokens = $this->getTokens();
		foreach($tokens as $wholetoken)
		{
			if(is_array($wholetoken))
				list($token, $text) = $wholetoken;
			else
				$token = $text = $wholetoken;

			// process tokens
			if($token === T_COMMENT)
				continue;

			if($token === T_FINAL)
				$class['final'] = true;

			if($token === T_ABSTRACT)
				$class['abstract'] = true;

			if($token === T_CLASS)
				$detected = 1;

			if($token === T_INTERFACE)
			{
				$detected = 1;
				$class['type'] = 'interface';
			}

			if($detected === 1 && $token === T_STRING)
			{
				$class['name'] = $text;
				$detected = 2;
			}

			if($detected >= 2 && $token === T_EXTENDS)
			{
				$detected = 3;
				$key = 'extends';
			}

			if($detected >= 2 && $token === T_IMPLEMENTS)
			{
				$detected = 3;
				$key = 'implements';
			}

			if($detected === 3 && $token === T_STRING)
			{
				$class[$key][] = $text;
			}

			if($token === ';' || $token === '{')
			{
				if($detected > 0)
				{
					$detected = 0;
					$result[] = $class;
					$class = $class_match_proto;
				}
				$class['final'] = false;
				$class['abstract'] = false;
			}

		}
		return $result;
	}

	public function getHighlightedSource()
	{
		echo '<code class="php_source">'."<span class=\"nl\"></span>";
		$pair_id_stack = array();
		foreach($this->getTokens() as $wholetoken)
		{
			if(is_array($wholetoken))
				list($token, $text) = $wholetoken;
			else
				$token = $text = $wholetoken;

			$tclass = is_int($token) ? strtolower(token_name($token)) : 't_'.bin2hex($token);
			
			//$esc_text = str_replace(array("\r\n","\n"), array("<br />\r\n","<br />\n"), htmlspecialchars($esc_text));
			$esc_text = str_replace("\t", "    ", $text);
			$esc_text = htmlspecialchars($esc_text);
			$esc_text = str_replace("\n", "\n<span class=\"nl\"></span>", $esc_text);
			switch($token)
			{
				// whitespace is not encapsulated to span 
				case T_WHITESPACE:
					echo $esc_text;
					continue 2; // TO CONTINUE FOREACH

				// all block opening tokens
				case T_OPEN_TAG:
				case T_OPEN_TAG_WITH_ECHO:
				case '(': 
				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
				//case T_STRING_VARNAME: // this not correct !
				case '{':
					array_push($pair_id_stack, $id = get_document_unique_id()); 
					echo '<span id="o'.$id.'" class="t pto '.$tclass."\">$esc_text</span><span class=\"ptm\">";
					continue 2; // TO CONTINUE FOREACH
										
				// all block closing tokens
				case T_CLOSE_TAG:
				case ')': 
				case '}':
					echo '</span><span id="c'.array_pop($pair_id_stack).'" class="t ptc '.$tclass."\">$esc_text</span>";
					continue 2; // TO CONTINUE FOREACH
					
				// other tokens
				default:
					echo '<span class="t '.$tclass."\">$esc_text</span>";
					continue 2; // TO CONTINUE FOREACH
			}
		}
		
		while(!empty($pair_id_stack))
			echo '</span><span id="c'.array_pop($pair_id_stack).'" class="t ptc t_eof"></span>';
		
		echo "\n</code>";
	}
	
//	TODO: function findDefinedFunctions()
//	public function findDefinedFunctions()
//	{
//		$function_match_proto = array('name'=>null, 'type'=>'function', 'extends'=>array(), 'implements'=>array(), 'final'=>false, 'abstract'=>false);
//		$result = array();
//		$function = $function_match_proto;
//		$detected = 0;
//		foreach($this->tokens as $wholetoken)
//		{
//			if(is_array($wholetoken))
//				list($token, $text) = $wholetoken;
//			else
//				$token = $text = $wholetoken;
//				
//			// process tokens
//			if($token === T_COMMENT)
//				continue;
//			
//			switch($token)
//			{
//				case T_FINAL:
//					break;
//
//				case T_ABSTRACT:
//					break;
//
//				case T_CLASS:
//					break;
//
//				case T_INTERFACE:
//					break;
//
//				case T_FUNCTION:
//					break;
//
//			}
//		}
//	}
	
}

