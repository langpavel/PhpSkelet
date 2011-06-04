<?php

class TrackUri extends Entity
{
	
}

class TrackUriTable extends EntityTable
{
	public function __construct()
	{
		parent::__construct();
		$this->addColumn(new ColumnId());
		$this->addColumn(new ColumnVarchar('uri', 255));
		$this->addColumn(new ColumnBool('track_params', array('default'=>false)));
		$this->addColumn(new ColumnInteger('cnt', array('default'=>0)));
	}
	
}
