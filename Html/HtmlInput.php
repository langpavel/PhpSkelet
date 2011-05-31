<?php

class HtmlInput extends HtmlWidget
{
	public function __construct($name_or_attrs = null, $parent = null)
	{
		parent::__construct('input', $name_or_attrs, $parent);
	}
}
