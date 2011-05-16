<?php

class Track_URIMapping extends EntityMapping
{
	
}

class Track_URI extends Entity 
{
	public function __toString()
	{
		return "Track_URI ID:$this[id]";
	}
}