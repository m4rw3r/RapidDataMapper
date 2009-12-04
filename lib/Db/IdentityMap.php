<?php
/*
 * Created by Martin Wernståhl on 2009-11-16.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which keeps track of the mapped objects.
 */
class Db_IdentityMap
{
	const SEPARATOR = '*';
	
	private static $map = array();
	
	private function __construct()
	{
		
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Stores an object in the IdentityMap.
	 * 
	 * @param  string
	 * @param  array
	 * @param  object
	 * @return void
	 */
	public static function add($class, $id, $object)
	{
		$class = strtolower($class);
		$id = implode(self::SEPARATOR, (Array) $id);
		
		if(isset(self::$map[$class][$id]))
		{
			throw new RuntimeException('Key "'.$key.'" is already populated.');
		}
		
		self::$map[$class][$id] = $object;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if a key has a value.
	 * 
	 * @param  string
	 * @param  array
	 * @return bool
	 */
	public static function has($class, $id)
	{
		$class = strtolower($class);
		$id = implode(self::SEPARATOR, (Array) $id);
		
		return isset(self::$map[$class][$id]);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the data of a key.
	 * 
	 * @param  string
	 * @param  array
	 * @return object
	 */
	public static function get($class, $id)
	{
		$class = strtolower($class);
		$id = implode(self::SEPARATOR, (Array) $id);
		
		if( ! isset(self::$map[$class][$id]))
		{
			throw new OutOfBoundsException($class.' ['.$id.']');
		}
		
		return self::$map[$class][$id];
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Deletes the contents of a key.
	 * 
	 * @param  string
	 * @param  array
	 * @return void
	 */
	public static function delete($class, $id)
	{
		$class = strtolower($class);
		$id = implode(self::SEPARATOR, (Array) $id);
		
		if( ! isset(self::$map[$class][$id]))
		{
			throw new OutOfBoundsException($class.' ['.$id.']');
		}
		
		unset(self::$map[$class][$id]);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the map, for debug purposes.
	 * 
	 * @return array
	 */
	public static function getMap()
	{
		return self::$map;
	}
}


/* End of file IdentityMap.php */
/* Location: ./lib/Db */