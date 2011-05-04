<?php

abstract class ColumnMapping extends SafeObject implements IMapping
{
	/**
	 * EntityManager 
	 * @var EntityManager
	 */
	private $manager;
	
	protected $name;
	protected $db_name;
	protected $display_name;
	protected $display_hint;
	protected $delay_load;
	
	public abstract function loadQuery(QueryBuilder $builder);
	public abstract function saveQuery(QueryBuilder $builder);
	public abstract function loadData($qresult, Entity $entity);
	public abstract function saveData(Entity $entity, QueryBuilder $builder);
}
