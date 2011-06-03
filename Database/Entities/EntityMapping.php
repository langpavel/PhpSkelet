<?php

abstract class EntityMapping extends Singleton
{
	private $columns = array();
	
	public function __construct()
	{
		parent::__construct();
	}

	public function addColumn(ColumnMapping $column)
	{
		$col_name = $column->getName();
		if(isset($this->columns[$col_name]))
			throw new InvalidOperationException("Cannot add column name '$col_name' to mapping: column with same name exist");
		$this->columns[$col_name] = $column;
	}
	
	public function removeColumn($column)
	{
		if(is_string($column))
			unset($this->columns[$column]);
		if($column instanceof ColumnMapping)
			return in_array($column, $this->columns, true);		
	}

	public function hasColumn($column)
	{
		if(is_string($column))
			return isset($this->columns[$column]);
		if($column instanceof ColumnMapping)
			return in_array($column, $this->columns, true);		
	}
	
	public function getColumn($col_name)
	{
		return $this->columns[$col_name];
	}

	/**
	 * Transform raw database data to entity
	 * @param Entity $entity entity or entity alias 
	 * @param array $data values readed from database
	 * @return Entity
	 */
	public abstract function loadEntity($entity, $data);
	
	/**
	 * Transform entity to database values
	 * @param Entity $entity
	 * @return array data to persist
	 */
	public abstract function saveEntity(Entity $entity);

	/* ArrayAccess interface */
	public function offsetExists ($offset) { return $this->hasColumn($offset); }
	public function offsetGet ($offset) { return $this->getColumn($offset); }
	public function offsetSet ($offset, $value) { throw new InvalidOperationException('Operation not alowed. Use addColumn() method instead.'); }
	public function offsetUnset ($offset) { $this->removeColumn($offset); }
	
	/* IteratorAggregate interface */
	public function getIterator () { return new ArrayIterator($this->columns); }
	
}

