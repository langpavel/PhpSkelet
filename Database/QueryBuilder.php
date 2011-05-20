<?php

/**
 * DO NOT USE THIS CLASS DIRECTLY - contribute and write bettrer api instead ;-)
 * Quick and dirty query builder.
 * This class is for internal use by IMapping implementors 
 * way for some SQL syntax specific abstraction layer
 * TODO: Create better API
 * @author langpavel
 */
abstract class QueryBuilder extends SafeObject
{
	protected $col_sources = array();
	protected $from_tables = array();
	//protected $joins = array();
	protected $where = array();
	//protected $group_by = array();
	protected $order_by = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getColumnSources()
	{
		return $this->col_sources;
	}

	public function addColumnSource($col_source)
	{
		$this->col_sources[] = $col_source;
		return $this;
	}
	
	public function addFromTable($table)
	{
		$this->from_tables[] = $table;
		return $this;
	}
	
	public function addWhereUnsafe($expression)
	{
		$this->where[] = $expression;
	}
	
	/**
	 * add 'ORDER BY' sorting to query 
	 * @param mixed $column
	 * @param bool $reverse
	 * @param bool $priority
	 */
	public function addSorting($column, $reverse=false, $priority=false)
	{
		if($priority === false)
			array_push($this->order_by, array($column, $reverse));
		else if($priority === true)
			array_unshift($this->order_by, array($column, $reverse));
		else
			$this->order_by[$priority] = array($column, $reverse);
	}
	
	public function getFromTables()
	{
		return $this->from_tables;
	}
	
	/**
	 * If array accepted, escape each item separately and join it with '.' as table name and column name usualy are
	 * @param mixed $identifier array or string
	 * @return string
	 */
	public abstract function escapeIdentifier($identifier);
	
	/**
	 * Make string safe in same way as mysql_real_escape_string do
	 * @param mixed $value
	 */
	public abstract function escapeString($value); 

	/**
	 * check type of value and convert it to safe form for SQL query inclusion 
	 * @param string $value
	 */
	public function escape($value)
	{
		if(is_int($value))
			return $value;
		else if(is_float($value))
			return str_replace(',', '.', ''.$value);
		else
			return $this->escapeString($value);
	}
	
	public function escapeIdentifiers(array $array)
	{
		$result = array();
		foreach($array as $item)
			$result[] = $this->escapeIdentifier($item);
		return $result;
	}
	
	public function getSQL()
	{
		// universal implementation
		
		$cols = $this->escapeIdentifiers($this->getColumnSources());
		$tables = $this->escapeIdentifiers($this->getFromTables());
		
		$cols = empty($cols) ? '*' : implode(', ', $cols);
		$tables = implode(', ', $tables);
			
		$result = "SELECT $cols\nFROM $tables\n";
		if(!empty($this->where))
		{
			$result .= 'WHERE ('. implode(') AND (', $this->where).")\n"; 
		}
		
		if(!empty($this->order_by))
		{
			$order_cols = array();
			foreach($this->order_by as $order_col)
			{
				$order_cols[] = $this->escapeIdentifier($order_col[0]).($order_col[1]?' DESC':'');
			}
			$result .= 'ORDER BY '.implode(', ', $order_cols); 
		}
		
		return $result;		
	}
	
	public function __toString()
	{
		return $this->getSQL();
	}
}
