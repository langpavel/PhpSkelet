<?php

$em = EntityManager::getInstance();

$em->registerEntity(array(
	'table'=>'track_uri', 
	'class'=>'TrackUri'
));
