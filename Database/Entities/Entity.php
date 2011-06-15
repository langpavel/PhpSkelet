<?php

/**
 *
 */
abstract class Entity implements IEntity
{
	// KEEP IN MIND THAT FOR EVERY DATABASE ROW THIS IS INSTANTIATED
	// TRY STORE MINIMUM AS POSSIBLE IN ENTITY INSTANCE!!!
	// IF POSSIBLE, MOVE ENTITY CONSTANT THINGS TO CLASS STATICS

	/**
	 * Entity manager that owns this instance
	 * @var EntityManager
	 */
	private $flags = IEntity::FLAGS_UNKNOWN_STATE;

	/**
	 * All data are stored here in array with key as version
	 */
	private $data;

	/**
	 * Good reason for private constructor
	 */
	private function __construct()
	{
		$this->data = array(
			IEntity::VERSION_ORIGINAL => array(),
			IEntity::VERSION_ORIGINAL_DB => array(),
		);
	}

	/**
	 * Do not call this. Internal use only
	 */
	public static final function createNewUninitializedInstance()
	{
		$cls = get_called_class();
		return new $cls();
	}

	public function __toString()
	{
		return '[Entity '.get_class($this)."']";
	}

	/**
	 * @return EntityMapping
	 */
	public static function getTable()
	{
		$cls = get_called_class().'Table';
		return $cls::getInstance();
	}

	public static final function create()
	{
		return static::getTable()->create(func_get_args());
	}

	public static final function load()
	{
		return static::getTable()->load(func_get_args(), null, false);
	}

	public static function loadOrCreate()
	{
		return static::getTable()->load(func_get_args(), null, true);
	}

	public static function exists()
	{
		return static::getTable()->exists(func_get_args());
	}

	public static function find()
	{
		return static::getTable()->find(func_get_args());
	}

	public function save()
	{
		return static::getTable()->save($this);
	}

	public function delete()
	{
		return static::getTable()->delete($this);
	}

	/*
	public function setPrimaryKey($id, $version = IEntity::VERSION_NEW)
	{
		throw new NotImplementedException();
	}

	public function getPrimaryKey($version = IEntity::VERSION_NEW)
	{
		throw new NotImplementedException();
	}
	*/

	public function set($name, $value, $version = Entity::VERSION_NEW, $trust_args = false)
	{
		if(!$trust_args && !$this->has($name))
			throw new InvalidArgumentException("set('$name', ..., ver$version)");
		$this->data[$version][$name] = $value;
	}

	public function has($name, $version = null)
	{
		if($version === null)
			return $this->getTable()->hasColumn($name);

		return array_key_exists($name, $this->data[$version]);
	}

	public function get($name, $version = Entity::VERSION_NEW, $trust_args = false)
	{
		if(!$this->has($name))
			throw new InvalidArgumentException("get('$name', ver$version)");
		return $this->data[$version][$name];
	}

	public function revert($name=null)
	{
		if($name === null)
		{
			$this->data[IEntity::VERSION_NEW] = $this->data[IEntity::VERSION_ORIGINAL];
			$this->data[IEntity::VERSION_NEW_DB] = $this->data[IEntity::VERSION_ORIGINAL_DB];
		}
		else
			$this->setValue($name, $this->getValue($name, $version = IEntity::VERSION_ORIGINAL));
	}

	public function hasChanges()
	{
		throw new NotImplementedException();
	}

	public function getChanges()
	{
		throw new NotImplementedException();
	}

	public function offsetExists ($offset) { return $this->has($offset); }
	public function offsetGet ($offset) { return $this->get($offset); }
	public function offsetSet ($offset, $value) { return $this->set($offset, $value); }
	public function offsetUnset ($offset) { $this->revert($offset); }

	public function __get($name) { return $this->get($name); }
	public function __set($name, $value) { $this->set($name, $value); }

}
