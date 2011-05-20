<?php

class MySQLQueryBuilder extends QueryBuilder
{
	public function escapeIdentifier($identifier)
	{
		if(is_array($identifier))
			return '`'.implode('`.`', $identifier).'`';
		return '`'.$identifier.'`';
	}
	
	public function escapeString($value)
	{
		return "'".str_replace("'","''",$value)."'";
	}
	
}