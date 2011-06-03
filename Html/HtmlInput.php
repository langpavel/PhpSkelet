<?php

class HtmlInput extends HtmlWidget
{
	public function __construct($name_or_attrs = null, $parent = null)
	{
		if(is_string($name_or_attrs))
			$name_or_attrs = array(
				'type'=>'text',
				'name'=>$name_or_attrs,
				'id'=>$name_or_attrs);

		parent::__construct('input', $name_or_attrs, $parent);
	}
}
