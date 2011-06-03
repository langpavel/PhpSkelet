<?php

final class EntityManager extends Singleton
{
	/**
	 * all possible entityIds and aliases as keys to array(db_table, className, entityId, connection_profile)
	 */
	private $entities = array();
	
	/**
	 * all currently loaded instances
	 */
	private $instances = array();	
	
	private function getEntityProperty($entity, $key)
	{
		if($entity instanceof Entity)
			$entity = get_class($entity);
		if(is_string($entity))
		{
			if(!isset($this->entities[$entity]))
				throw new InvalidArgumentException("Cannot resolve entity propert for '$entity'");
			return $this->entities[$entity][$key];
		}
		else
			throw new InvalidArgumentException('Cannot resolve entity property from '.get_type($entity));
	}
	
	/**
	 * get database table name of entity
	 */
	public function getEntityTable($entity)
	{
		return $this->getEntityProperty($etity, 0);
	}

	/**
	 * get class name of entity
	 */
	public function getEntityClass($entity)
	{
		return $this->getEntityProperty($etity, 1);
	}

	/**
	 * get entityID of entity
	 */
	public function getEntityId($entity)
	{
		return $this->getEntityProperty($etity, 2);
	}

	public function registerEntity($db_table, $entity_class = null, $entityId = null, $connection_profile = null)
	{
		if(is_array($db_table))
		{
			$entity_class = isset($db_table['class']) ? $db_table['class'] : null;
			$entityId = isset($db_table['id']) ? $db_table['id'] : null;
			
			$db_table = $db_table['table'];
		}
		
		if($entity_class === null)
			$entity_class = $db_table;
		if($entityId === null)
			$entityId = $entity_class;
		
		if(isset($this->entities[$entityId]))
			throw new InvalidOperationException("Cannot register entity '$entityId', already registered");
		
		$this->entities[$entityId] = array($db_table, $entity_class, $entityId, $connection_profile);
	}
	
	public function registerEntityAlias($entityId, $alias)
	{
		if(!isset($this->entities[$entityId]))
			throw new InvalidOperationException("Cannot register alias, entity '$entityId' must be registered first");
		if(isset($this->entities[$entityId]))
			throw new InvalidOperationException("Cannot register alias '$entityId', already registered");
		$this->entities[$alias] = & $this->entities[$entityId];
	}
	
	
}


/** OLD CODE

class EntityManager extends Singleton
{
	protected $entityInstances = array();
	
	/ **
	 * Get EntityManager instance
	 * @return EntityManager
	 * /
	public static function getInstance()
	{
		return parent::getInstance();
	}
	
	/ **
	 * Register entity ID and assotiate it with class name or prototype entity instance.
	 * @param string $entityID
	 * @param mixed[optional] $entityClass String means class name (if ommited same as $entityID), instance is prototype
	 * /
	public function register($entityID, $entityClass = null, $replace = false)
	{
		parent::register($entityID, $entityClass, $replace);
		if(!isset($this->entityInstances[$entityID]))
			$this->entityInstances[$entityID] = array();
	}
	
	public function registerInstance($entityID, $instanceID, Entity $instance, $replace=false)
	{
		if(isset($this->entityInstances[$entityID][$instanceID]))
		{
			if(!$replace)
				throw new InvalidOperationException("Cannot re-register entity instance $entityID:$instanceID");
			if($this->entityInstances[$entityID][$instanceID] !== $instance)
				$this->entityInstances[$entityID][$instanceID]->invalid(true);
				
		}
		$this->entityInstances[$entityID][$instanceID] = $instance;
	}
	
	public function create($entityID)
	{
		$entity = parent::create($entityID);
		$entity->initNew();
		return $entity;
	}
	
	public function load($entityID, $instanceID)
	{
		if(isset($this->entityInstances[$entityID][$instanceID]))
			return $this->entityInstances[$entityID][$instanceID];
		$entity = parent::create($entityID);
		$entity->setId($id);
		return $entity;
	} 

	public function save($entity)
	{
		
	} 
	
	public function delete($entity)
	{
		
	}
	
	protected function createInstanceFromClassName($prototypeID, $className)
	{
		return new $className($prototypeID, $this);
	}
	
}

 */

