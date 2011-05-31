<?php

class HtmlForm extends HtmlWidget
{
	public function __construct($name_or_attrs = null, $parent = null)
	{
		parent::__construct('form', $name_or_attrs, $parent);
		if(!$this->hasAttribute('action'))
			$this->setAttribute('action', URI::getCurrent()->setQuery());
	}
	
	public function setName($name)
	{
		parent::setName($name);
		$this->setAttribute('name', $name);
		$this->setAttribute('id', $name);
	}
	
}
