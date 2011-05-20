<?php

interface IMapping
{
	public function loadQuery(QueryBuilder $builder);
	public function saveQuery(QueryBuilder $builder);
	public function loadData($qresult, Entity $entity);
	public function saveData(Entity $entity, QueryBuilder $builder);
}
