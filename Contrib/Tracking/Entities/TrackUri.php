<?php

class TrackUri extends Entity
{

	public function __toString()
	{
		return $this->uri;
	}

	public function toHtml()
	{
		$ue = htmlentities($this->uri);
		return "<a href=\"$ue\">$ue</a>";
	}
}

class TrackUriTable extends EntityTable
{
	protected $entityType = 'TrackUri';
	protected $dbTable = 'track_uri';

	public function __construct()
	{
		parent::__construct();
		$this->addColumn(new ColumnId());
		$this->addColumn(new ColumnVarchar('uri', 255));
		$this->addColumn(new ColumnBool('track_params', array('default'=>false)));
		$this->addColumn(new ColumnInteger('cnt', array('default'=>0)));
	}
}
