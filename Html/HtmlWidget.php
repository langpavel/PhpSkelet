<?php

class HtmlWidget extends Widget
{
	private $name = null;
	protected $tagName;
	protected $attributes = array();
	
	public function __construct($tagName, $name_or_attrs = null, $parent = null)
	{
		$name = $name_or_attrs;
		if($parent === null && $name_or_attrs instanceof Composite)
		{
			$parent = $name;
			$name_or_attrs = $name = null;
		}
		else if(is_array($name_or_attrs))
		{
			$this->attributes = array_merge($this->attributes, $name_or_attrs);
			$name = isset($name_or_attrs['id']) ? $name_or_attrs['id'] :
				(isset($name_or_attrs['name']) ? $name_or_attrs['name'] : null);
		}
		
		parent::__construct($parent);
		$this->setChildClass(__CLASS__);
		
		$this->tagName = $tagName;
		
		if($name !== null)
			$this->setName($name);				
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function hasAttribute($name)
	{
		return isset($this->attributes[$name]) && !empty($this->attributes[$name]);
	}
	
	public function getAttribute($name, $default = null)
	{
		if(isset($this->attributes[$name]))
			return $this->attributes[$name];
		else
			return $default;
	}
	
	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}

	public function getAttributes()
	{
		$result = array();
		foreach($this->attributes as $atr=>$val)
		{
			if(is_array($val))
				$val = implode(' ', $val);
			else if($val === false)
				continue;
			else if($val === true)
				$result[] = "$atr=\"$atr\"";
			else if(is_real($val))
				$result[] = sprintf('%s="%F"', $atr, $val);
			else
				$result[] = $atr.'="'.htmlentities($val).'"';
		}
		return implode(' ', $result);
	}

	public function getContent()
	{
		if(count($this) == 0)
			return null;

		$result = array();
		foreach($this as $child)
			$result[] = $child->toHtml();

		return implode('', $result);
	}

	public function toHtml()
	{
		$tag = $this->tagName;
		$attrs = $this->getAttributes();

		$content = $this->getContent();
	
		$tag_atrs = trim($tag.' '.$attrs);
		
		if($content === null)
			return "<$tag_atrs />";
		else
			return "<$tag_atrs>$content</$tag>";
	}
	
	public function __tostring()
	{
		return $this->toHtml();
	}

	public function addText($unsafe_text)
	{
		Html::Text($unsafe_text, $this);
		return $this;
	}

	public function addHtml($html_text)
	{
		Html::Html($html_text, $this);
		return $this;
	}

	public function addTag($tag, $attributes=null, $content=null)
	{
		Html::Tag($tag, $attributes, $content, $this);
		return $this;
	}

}
