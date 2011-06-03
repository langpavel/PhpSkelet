<?php

class TrackUri extends Entity
{
	
}

class TrackUriMapping extends EntityMapping
{
	public function __construct()
	{
		parent::__construct();
		$this->addColumn(new ColumnId());
	}
}
