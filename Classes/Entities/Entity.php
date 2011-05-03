<?php

abstract class Entity extends SafeObject implements ArrayAccess
{
	/**
	 * Entity manager that owns this instance 
	 * @var EntityManager 
	 */
	protected $manager;
	protected $entityID;
	private $id;
	private $invalid = true;
	
	public function __construct($entityID, EntityManager $manager)
	{
		parent::__construct();
		$this->manager = $entity_manager;
		$this->entityID = $entityID;
	}

	public function invalid($value = null)
	{
		if($value === null)
			return $this->invalid;
		$this->invalid = $value;
	}

	/*
	public function isValid()
	{
		return !$this->invalid;
	}
	*/
	
	public function checkValid()
	{
		if($this->invalid())
			throw new InvalidOperationException("Instance $this->entityID:$this->id is invalidated");
	}
	
	public function setId($id)
	{
		$this->manager->registerInstance($this->entityID, $id, $this, true);
	}
	
	public function initNew()
	{
		$this->setId(null);
	}
	
	public function __toString()
	{
		return '[Entity class '.get_class($this).": $this->entityID:$this->id]".($this->invalid()?' (invalidated)':'');
	}
}
