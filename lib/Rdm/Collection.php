<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

namespace Rdm;

/**
 * 
 */
abstract class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
	const FILTER_CLASS = '\\Rdm\\Collection\\Filter';
	
	/**
	 * If this flag is true, this object has already been used with entity objects,
	 * therefore it can no longer use filters.
	 * 
	 * TODO: HOW TO CHANGE THIS FROM NESTED FILTERS/FILTER BY COLLECTIONS ?!?!?!
	 * 
	 * Needs to be public because the filter objects needs to be able to create a reference
	 * to this variable.
	 * 
	 * @var boolean
	 */
	public $is_locked = false;
	
	/**
	 * Flag which tells us if we've populated this object already.
	 * 
	 * @var boolean
	 */
	protected $is_populated = false;
	
	/**
	 * A list of filter objects
	 * 
	 * @var array
	 */
	protected $filters = array();
	
	/**
	 * The array of data objects.
	 * 
	 * @var array
	 */
	protected $contents = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new instance of this class.
	 * 
	 * @return \Rdm\Collection\Base
	 */
	public static function create()
	{
		$c = get_called_class();
		
		return new $c();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Will register the passed object as a persistent object in the database.
	 * 
	 * @param  Object
	 * @return Object	The object registered with this collection
	 */
	public static function persist($object)
	{
		// TODO: Code, or abstract?
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Will register the passed object for deletion from the database.
	 * 
	 * @param  Object
	 * @return Object
	 */
	public function delete($object)
	{
		# code...
	}
	
	///////////////////////////////////////////////////////////////////////////
	//  FILTER RELATED METHODS                                               //
	///////////////////////////////////////////////////////////////////////////
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function has()
	{
		if($this->is_locked)
		{
			// TODO: Better exception message and proper exception class
			throw new \Exception('Object is locked');
		}
		
		$c = static::FILTER_CLASS;
		
		empty($this->filters) OR $this->filters[] = 'AND';
		
		return $this->filters[] = new $c($this);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function orHas()
	{
		if($this->is_locked)
		{
			// TODO: Better exception message and proper exception class
			throw new \Exception('Object is locked');
		}
		
		$c = static::FILTER_CLASS;
		
		empty($this->filters) OR $this->filters[] = 'OR';
		
		return $this->filters[] = new $c($this);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __toString()
	{
		return implode(' ', $this->filters);
	}
	
	///////////////////////////////////////////////////////////////////////////
	//  ENTITY RELATED METHODS                                               //
	///////////////////////////////////////////////////////////////////////////
	
	/**
	 * Adds an entity to this collection, this collection will be locked and
	 * the entity will assume data which matches the filters of this collection.
	 * 
	 * @param  Object
	 * @return self
	 */
	public function add($object)
	{
		// TODO: Code
		
		$this->is_locked = true;
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Removes the object from this collection, this collection will be locked
	 * and the entity's values will be set so that they no longer match filters.
	 * 
	 * @param  Object
	 * @return self
	 */
	public function remove($object)
	{
		// TODO: Code
		
		$this->is_locked = true;
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Removes all objects from this collection, this collection will be locked
	 * and the entity's values will be set so that they no longer match filters.
	 * 
	 * @return int	Number of objects removed
	 */
	public function removeAll()
	{
		$num = 0;
		
		// TODO: Code
		
		$this->is_locked = false;
		return $num;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Deletes all objects matching this collection from the database, also locks
	 * this collection's filters.
	 * 
	 * @return int	Number of objects removed
	 */
	public function deleteAll()
	{
		$num = 0;
		
		// TODO: Code
		
		$this->is_locked = false;
		return $num;
	}
	
	/**
	 * This method should populate this object with data in respect to the $filters parameter.
	 * 
	 * !!! ATTENTION:
	 * 
	 * THIS METHOD MUST SET THE LOCAL INSTANCE VARIABLES is_populated AND is_locked TO true!!!
	 * 
	 * @return void
	 */
	public abstract function populate();
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function isEmpty()
	{
		// TODO: Use a COUNT() query instead of populate the object when it is empty
		$this->is_populated OR $this->populate();
		
		return empty($this->contents);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns an array of objects of this collection.
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$this->is_populated OR $this->populate();
		
		return $this->contents;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function offsetExists($offset)
	{
		$this->is_populated OR $this->populate();
		
		return isset($this->contents[$offset]);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function offsetGet($offset)
	{
		$this->is_populated OR $this->populate();
		
		return $this->contents[$offset];
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function offsetSet($offset, $value)
	{
		// TODO: Implement?
		throw \Exception('Not yet implemented');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function offsetUnset($offset)
	{
		// TODO: Implement?
		throw \Exception('Not yet implemented');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function count()
	{
		// TODO: COUNT() query instead of populate?
		$this->is_populated OR $this->populate();
		
		return count($this->contents);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function getIterator()
	{
		$this->is_populated OR $this->populate();
		
		return new \ArrayIterator($this->contents);
	}
}


/* End of file Collection.php */
/* Location: ./lib/Rdm */