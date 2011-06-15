<?php

/**
 * Use this class only for column definition.
 * All logic should be implemented in concrete descendant of Entity
 *
 * @author Pavel Lang (langpavel@phpskelet.org)
 */
abstract class EntityTable extends Singleton
{
	protected $dbTable;
	private $columns = array();
	protected $entityType;
	protected $connection;

	protected function __construct()
	{
		parent::__construct();
		if($this->dbTable === null)
			$this->dbTable = $this->getEntityType();
	}

	public function setConnection($value)
	{
		$this->connection = $value;
	}

	public function getConnection()
	{
		if(is_array($this->connection))
		{
			if(count($this->connection) == 2)
				$this->connection = ConnectionManager::getInstance()->get(
					$this->connection[0], $this->connection[1]);
			else if(count($this->connection) == 1)
				$this->connection = ConnectionManager::getInstance()->get(
					$this->connection[0]);
		}
		else if(is_string($this->connection))
			$this->connection = ConnectionManager::getInstance()->get($this->connection);
		else if($this->connection === null)
			$this->connection = ConnectionManager::getInstance()->get();

		return $this->connection;
	}

	/**
	 * @return string Entity type name
	 */
	public function getEntityType()
	{
		if($this->entityType !== null)
			return $this->entityType;

		if(substr(get_called_class(), -5) !== 'Table')
			throw new InvalidOperationException('Cannot determine Entity type name');

		$this->entityType = substr(get_called_class(), 0, -5);
		return $this->entityType;
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

	public function getQueryColumnString($columns = null)
	{
		if($columns === null)
			$columns = $this->columns;

		$result = array();

		foreach($columns as $column)
		{
			if(is_string($column))
				$column = $this->getColumn($column);
			array_append($result, $column->getDbColumn());
		}
		return implode(', ', $result);
	}

	/**
	 * Do not call this, call static methods on concrete entity
	 * @param unknown_type $primary_key
	 * @param unknown_type $columns
	 * @param unknown_type $can_create
	 * @throws ApplicationException
	 */
	public function load($primary_key, $columns = null, $can_create = false)
	{
		$c = $this->getConnection();
		$cls =  $this->getEntityType();

		$sql = 'SELECT '.$this->getQueryColumnString().
			" FROM [$this->dbTable]".
			" WHERE id=$primary_key[0]";

		$q = $c->query($sql);
		$r = $q->fetchAll();
		if(count($r) == 0)
		{
			if(!$can_create)
				throw new ApplicationException('Entity does not exists');
			$entity = $cls::createNewUninitializedInstance();
			$this->createEntity($entity);
			return $entity;
		}
		elseif(count($r) > 1)
			throw new ApplicationException('Entity multiple matches!');

		$data = $r[0];
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
			$entity->revert();
		}
	}

	protected function createEntity(IEntity $entity)
	{
		foreach($this->columns as $column)
		{
			$colname = $column->getName();
			$val = $column->getDefaultValue();
			$entity->set($colname, $val, IEntity::VERSION_NEW, true);
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

}

