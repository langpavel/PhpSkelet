<?php

abstract class EntityTable extends Singleton
{
	private $columns = array();
	protected $entityType;
	
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return string Entity type name
	 */
	public function getEntityType()
	{
		if($this->entityType !== null)
			return $this->entityType;
		return substr(get_called_class(), 0, -5);
	}

	public function addColumn(TableColumn $column)
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

	public function load($primary_key)
	{
		$data = array('id'=>1, 'uri'=>'test_uri', 'track_params'=>0);
		$cls =  $this->getEntityType();
		$entity = $cls::createNewUninitializedInstance();
		$this->loadEntity($entity, $data);
		return $entity;
	} 

	/**
	 * Transform raw database data to entity
	 * @param Entity $entity entity or entity alias 
	 * @param array $data values readed from database
	 * @return Entity
	 */
	protected function loadEntity($entity, $data)
	{
		foreach($this->columns as $column)
		{
			$colname = $column->getName();
			$db_name = $column->getDbColumn();
			if(is_array($db_name))
				throw NotImplementedException();
			else
			{
				// if column is not readed, do nothing
				if(!isset($data[$db_name]))
					continue;
				 
				$val = $data[$db_name];
			}
			$entity->set($colname, $val, IEntity::VERSION_ORIGINAL_DB, true);
			$val = $column->fromDbFormat($val);
			$entity->set($colname, $val, IEntity::VERSION_ORIGINAL, true);
		}
	}
	
	/**
	 * Transform entity to database values
	 * @param Entity $entity
	 * @return array data to persist
	 */
	protected function saveEntity(IEntity $entity)
	{
		
	}

	/**
	 * Transform entity to database values
	 * @param Entity $entity
	 * @return array data to persist
	 */
	protected function deleteEntity(IEntity $entity)
	{
		
	}

	/* ArrayAccess interface */
	public function offsetExists ($offset) { return $this->hasColumn($offset); }
	public function offsetGet ($offset) { return $this->getColumn($offset); }
	public function offsetSet ($offset, $value) { throw new InvalidOperationException('Operation not alowed. Use addColumn() method instead.'); }
	public function offsetUnset ($offset) { $this->removeColumn($offset); }
	
	/* IteratorAggregate interface */
	public function getIterator () { return new ArrayIterator($this->columns); }
	
}

