<?php

interface IEntity extends ArrayAccess
{
	// KEEP IN MIND THAT FOR EVERY DATABASE ROW THIS IS INSTANTIATED
	// TRY STORE MINIMUM AS POSSIBLE IN ENTITY INSTANCE!!!
	// IF POSSIBLE, MOVE ENTITY CONSTANT THINGS TO CLASS STATICS
	
	// FLAGS
	const VERSION_DEFAULT = 0x00;
	const VERSION_ORIGINAL_DB = 0x01;
	const VERSION_NEW_DB = 0x02;
	const VERSION_MASK_DB = 0x03;
	const VERSION_ORIGINAL = 0x04;
	const VERSION_NEW = 0x08;
	const VERSION_MASK_VALUE = 0x0c;

	// FLAGS
	const FLAGS_UNKNOWN_STATE = 0x00;
	const FLAGS_ATTACHED  = 0x01; // if entity is attached to EntityTable
	const FLAGS_DATA_SAVE_INSERT  = 0x02;
	const FLAGS_DATA_SAVE_UPDATE  = 0x04;
	const FLAGS_DATA_SAVE_REPLACE  = 0x06;
	const FLAGS_DATA_SAVE_DELETE  = 0x08;
	const FLAGS_DATA_LOADED  = 0x10;
	const FLAGS_DATA_CHANGED = 0x20;

	/**
	 * Get entity mapping
	 * @return EntityTable
	 */
	public static function getTable();
	
	public static function create();
	public static function load();
	public static function replace();
	public static function exists();
	public static function find();
	
	public function save();
	public function delete();
	
	public function setPrimaryKey($id, $version = IEntity::VERSION_NEW);
	public function getPrimaryKey($version = IEntity::VERSION_NEW);
	
	public function has($name, $version = null);
	public function get($name, $version = IEntity::VERSION_NEW);
	public function set($name, $value, $version = IEntity::VERSION_NEW);
	
	public function hasChanges();
	public function getChanges();
}
