<?php

abstract class EntityManager extends PrototypeManager
{
	protected $entityInstances = array();
	
	/**
	 * Get EntityManager instance
	 * @return EntityManager
	 */
	public static function getInstance()
	{
		return parent::getInstance();
	}
	
	/**
	 * Register entity ID and assotiate it with class name or prototype entity instance.
	 * @param string $entityID
	 * @param mixed[optional] $entityClass String means class name (if ommited same as $entityID), instance is prototype
	 */
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