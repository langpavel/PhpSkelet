<?php

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

	protected function __construct($entityID, EntityManager $entity_manager)
	{
		parent::__construct();
		$this->manager = $entity_manager;
		$this->entityID = $entityID;
		$defaults=$this->getDefaults();
		$this->row = array(Entity::VERSION_DEFAULT=>$defaults, Entity::VERSION_OLD=>array(), Entity::VERSION_NEW=>$defaults);
	}

	public static function create($entityId = null)
	{
		if($entityId === null)
		{
			$class = get_called_class();
			$entityId = $class::getEntityId();
		}
		$entity = new $class($class, EntityManager::getInstance());
		return $entity;		
	}
	
	/**
	 * Get default values
	 * @return array all column default values, keys are column names
	 */
	public static function getDefaults()
	{
		return array('id'=>null);
	}
	
	public function setId($id, $version = Entity::VERSION_NEW)
	{
		$this->set('id', $id);
		$this->manager->registerInstance($this->entityID, $id, $this, true);
	}
	
	public function getId($version = Entity::VERSION_NEW)
	{
		return $this->getValue('id', $version);
	}
	
	public function initNew()
	{
		$this->setId(null);
	}
	
	public function __toString()
	{
		return '[Entity class '.get_class($this).": $this->entityID:$this->id]";
	}

	public function set($name, $value, $version = Entity::VERSION_NEW)
	{
		if(!$this->has($name))
			throw new InvalidArgumentException("set('$name', ..., ver$version)");
		$this->row[$version][$name] = $value;
	}
	
	public function has($name)
	{
		return array_key_exists($name, $this->row[Entity::VERSION_DEFAULT]);
	}
	
	public function get($name, $version = Entity::VERSION_NEW)
	{
		if(!$this->has($name))
			throw new InvalidArgumentException("get('$name', ver$version)");
		return $this->row[$version][$name];
	}
	
	public function revert($name=null)
	{
		if($name === null)
			$this->row[Entity::VERSION_NEW] = $this->row[Entity::VERSION_OLD];
		else 
			$this->setValue($name, $this->getValue($name, $version = Entity::VERSION_OLD));
	}
	
	public function offsetExists ($offset) { return $this->has($offset); }
	public function offsetGet ($offset) { return $this->get($offset); }
	public function offsetSet ($offset, $value) { return $this->set($offset, $value); }
	public function offsetUnset ($offset) { $this->revert($offset); }
	
}
