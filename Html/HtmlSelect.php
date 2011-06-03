<?php

class HtmlSelect extends HtmlWidget
{
	private $select_options = array();
	private $selected_key;
	
	public function __construct($name_or_attrs = null, $parent = null)
	{
		if(is_string($name_or_attrs))
			$name_or_attrs = array(
				'name'=>$name_or_attrs,
				'id'=>$name_or_attrs);
				
		parent::__construct('select', $name_or_attrs, $parent);
	}
	
	protected function bind_array($binding)
	{
		$this->select_options = $binding;
	}
	
	private function getOptionHtml($key, $option)
	{
		$selected = $this->selected_key == $key ? ' selected="selected"' : '';

		$key = htmlentities($key);
		if(is_array($option))
		{
			return "<optgroup label=\"$key\">\r\n" . 
				$this->getOptionsHtml($option) . '</optgroup>';
		}	
		
		$option = htmlspecialchars($option);
		return "<option value=\"$key\"$selected>$option</option>";
	}
	
	private function getOptionsHtml($options)
	{
		$result = array();
		
		foreach($options as $key=>$option)
		{
			$result[] = $this->getOptionHtml($key, $option);
		}
		
		return implode("\t\t\r\n", $result);
	}
	
	public function getContent()
	{
		return $this->getOptionsHtml($this->select_options);
	}
	
	public function bind($binding)
	{
		if(is_array($binding))
			$this->bind_array($binding);
		return $this;
	}
	
	public function setSelectedKey($value)
	{
		$this->selected_key = $value;
	}
}
