<?php

/**
 * Use this class to represent html text nodes 
 * or raw mixed text & html
 * 
 * also acts as static factory
 */
final class Html extends HtmlWidget
{
	private $html_text;
	
	public function __construct($html_text, $parent = null)
	{
		parent::__construct('#text', null, $parent);
		$this->html_text = $html_text;
	}

	public function getAttribute($name, $default = null)
	{
		throw new InvalidOperationException('Cannot access attributes on html text widget');
	}
	
	public function setAttribute($name, $value)
	{
		throw new InvalidOperationException('Cannot access attributes on html text widget');
	}
	
	public function toHtml()
	{
		return $this->html_text;
	}
	
	public static function Html($html_text, $parent = null)
	{
		return new Html($html_text, $parent);
	}
	
	public static function Text($unsafe_text, $parent = null)
	{
		return new Html(htmlspecialchars($unsafe_text), $parent);
	}
	
	public static function Tag($tag, $attributes = null, $content = null, $parent = null)
	{
		if(is_string($attributes))
		{
			$content = $attributes;
			$attributes = null;
		}
		$widget = new HtmlWidget($tag, $attributes, $parent);
		if($content !== null)
			$widget->addText($content);
		return $widget;
	}

	public static function Form($name = null, $parent = null)
	{
		return new HtmlForm($name, $parent);
	}
	
	public static function Select($attributes = null, $binding = null, $this = null)
	{
		$select = new HtmlSelect($attributes, $this);
		if($binding !== null)
			$select->bind($binding);
		return $select;
	}
}
