<?php

abstract class TableColumn extends SafeObject
{
	private $name;
	private $db_column;
	private $display_name;
	private $display_hint;
	private $delay_load;
	private $nullable;
	private $required;
	private $default_value;
	private $concurrency_checked;

	/**
	 * consumes and checks costructor parameters
	 */
	protected function consume_param(array &$params, $property_name, $property_setter = null, $default = null, array $possibilities = null)
	{
		$value = $default;
		if(isset($params[$property_name]))
		{
			$value = $params[$property_name];
			unset($params[$property_name]);
		}
		else
			$value = $default;
		
		if($possibilities !== null)
		{
			if(!in_array($value, $possibilities, true))
				throw new InvalidArgumentException("Invalid value for property '$property_name'");
		}
		
		if(is_array($property_setter))
			call_user_func($property_setter, $value);
		else if($property_setter !== null)
			$this->$property_setter = $value;
		else
			$this->$property_name = $value;
	}

	public function __construct($name, array $params = null)
	{
		parent::__construct();
		
		$this->setName($name);
		
		if($params !== null)
		{
			$this->consume_param($params, 'db_column', array($this, 'setDbColumn'), $name);
			$this->consume_param($params, 'display_name');
			$this->consume_param($params, 'display_hint');
			$this->consume_param($params, 'delay_load', null, false, array(true, false));
			$this->consume_param($params, 'nullable', null, true, array(true, false));
			$this->consume_param($params, 'required', null, false, array(true, false));
			$this->consume_param($params, 'default_value');
			$this->consume_param($params, 'default', array($this, 'setDefaultValue'));
			$this->consume_param($params, 'concurrency_checked', null, true, array(true, false));
		}
		
		if(!empty($params))
			throw new InvalidArgumentException('Column: \''.$name.'\'Unknown parameter(s) passed: \''.(
				implode('\', \'', array_keys($params))).'\'');
	}	
	
	/**
	 * Validate and correct value. Must be able convert from string 
	 * (to use with html forms).
	 * @param mixed $value reference to validate
	 * @return bool if is correct or repaired
	 */
	public abstract function correctValue(&$value, &$message = null, $strict = false);
	
	/**
	 * Transform $value given from database to PHP mapped type
	 * @return mixed value transformed to appropriate PHP type or class 
	 */
	public function fromDbFormat($value)
	{
		return $value;
	}

	/**
	 * Transform $value to sclalar types storable in database
	 * @return mixed sclalar type or array if  
	 */	
	public function toDbFormat($value)
	{
		return $value;
	}
	
	/* PROPERTIES*/
	
	/**
	 * Get value of name
	 * @return mixed name
	 */
	public function getName() { return $this->name; }

	/**
	 * Set value of name
	 * @param mixed $value name
	 * @return ColumnMapping self
	 */
	protected function setName($value) { $this->name = $value; return $this; }

	/**
	 * Get value of db_name
	 * @return string|array database column name(s)
	 */
	public function getDbColumn() { return $this->db_column; }

	/**
	 * Set value of db_name
	 * @param mixed $value db_name
	 * @return ColumnMapping self
	 */
	protected function setDbColumn($value) { $this->db_column = $value; return $this; }

	/**
	 * Get value of delay_load
	 * @return mixed delay_load
	 */
	public function getDelayLoad() { return $this->delay_load; }

	/**
	 * Set value of delay_load
	 * @param mixed $value delay_load
	 * @return ColumnMapping self
	 */
	public function setDelayLoad($value) { $this->delay_load = $value; return $this; }

	/**
	 * Get value of display_name
	 * @return mixed display_name
	 */
	public function getDisplayName() { return $this->display_name; }

	/**
	 * Set value of display_name
	 * @param mixed $value display_name
	 * @return ColumnMapping self
	 */
	public function setDisplayName($value) { $this->display_name = $value; return $this; }

	/**
	 * Get value of display_hint
	 * @return mixed display_hint
	 */
	public function getDisplayHint() { return $this->display_hint; }

	/**
	 * Set value of display_hint
	 * @param mixed $value display_hint
	 * @return ColumnMapping self
	 */
	public function setDisplayHint($value) { $this->display_hint = $value; return $this; }


/**
	 * Get value of not_null
	 * @return mixed not_null
	 */
	public function isNullable() { return $this->nullable; }

	/**
	 * Set value of not_null
	 * @param mixed $value not_null
	 * @return ColumnMapping self
	 */
	public function setNullable($value) { $this->nullable = $value; return $this; }

	/**
	 * Get value of required
	 * @return mixed required
	 */
	public function isRequired() { return $this->required; }

	/**
	 * Set value of required
	 * @param mixed $value required
	 * @return ColumnMapping self
	 */
	public function setRequired($value) { $this->required = $value; return $this; }

	/**
	 * Get value of default_value
	 * @return mixed default_value
	 */
	public function getDefaultValue() { return $this->default_value; }

	/**
	 * Set value of default_value
	 * @param mixed $value default_value
	 * @return ColumnMapping self
	 */
	public function setDefaultValue($value) { $this->default_value = $value; return $this; }

	/**
	 * Get value of concurrency_checked
	 * @return mixed concurrency_checked
	 */
	public function isConcurrencyChecked() { return $this->concurrency_checked; }

	/**
	 * Set value of concurrency_checked
	 * @param mixed $value concurrency_checked
	 * @return ColumnMapping self
	 */
	public function setConcurrencyChecked($value) { $this->concurrency_checked = $value; return $this; }


}
