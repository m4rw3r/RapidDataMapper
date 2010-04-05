<?php
/*
 * Created by Martin Wernståhl on 2010-04-03.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
abstract class Rdm_UnitOfWork
{
	/**
	 * Registered entities which have been fetched from the database.
	 * 
	 * @var array(Object)
	 */
	protected $entities = array();
	
	/**
	 * Entities which are to be inserted into the database.
	 * 
	 * @var array(Object)
	 */
	protected $new_entities = array();
	
	/**
	 * Entities which are to be deleted from the database.
	 * 
	 * @var array(Object)
	 */
	protected $deleted_entities = array();
	
	/**
	 * A list of multi delete operations to be performed.
	 * 
	 * @var array(Rdm_UnitOfWork_MultiDelete)
	 */
	protected $multi_delete = array();
	
	/**
	 * A list of multi update operations to be performed.
	 * 
	 * @var array(Rdm_UnitOfWork_MultiUpdate)
	 */
	protected $multi_update = array();
	
	/**
	 * The database adapter to use when sending calls to the database.
	 * 
	 * @var Rdm_Adapter
	 */
	protected $db;
	
	/**
	 * The name of the database adapter to use.
	 * 
	 * @var string
	 */
	protected $adapter_name = false;
	
	// ------------------------------------------------------------------------

	/**
	 * Adds an entity to this Unit of work, only used for objects which already
	 * exist in the database.
	 * 
	 * @param  object
	 * @param  string
	 * @return void
	 */
	public function addEntity($object, $key)
	{
		$this->entities[] = $object;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Registers a new entity to be inserted into the database.
	 * 
	 * @param  object
	 * @return void
	 */
	public function addNewEntity($object)
	{
		if( ! empty($object->__id))
		{
			// TODO: Replace exception with another exception or return false?
			throw new Exception('Object has already been saved.');
		}
		
		$oid = spl_object_hash($object);
		
		isset($this->new_entities[$oid]) OR $this->new_entities[$oid] = $object;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Registers an entity for deletion, also removes it from the entity registry
	 * of this Unit of work.
	 * 
	 * @param  object
	 * @param  string
	 * @return void
	 */
	public function addForDelete($object, $key)
	{
		if(isset($this->entities[$key]))
		{
			unset($this->entities[$key]);
		}
		
		$this->deleted_entities[$key] = $object;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if an entity with the key $key exists in this unit of work.
	 * 
	 * @param  string
	 * @return boolean
	 */
	public function hasEntity($key)
	{
		return isset($this->entities[$key]);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the entity with the key $key.
	 * 
	 * @return object
	 */
	public function getEntity($key)
	{
		return $this->entites;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Commits the changes stored in this unit of work.
	 * 
	 * @return boolean
	 */
	public function commit()
	{
		try
		{
			$this->process();
			
			$this->cleanup();
		}
		catch(Exception $e)
		{
			$this->reset();
			
			// Rethrow
			throw $e;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Processes all the changes registered in this unit of work.
	 * 
	 * @return void
	 */
	public function process()
	{
		isset($this->db) OR $this->db = Rdm_Adapter::getInstance($this->adapter_name);
		
		$this->processSingleInsertions();
		$this->processSingleChanges();
		$this->processSingleDeletions();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Cleans the data and updates the objects of this unit of work after
	 * process() has been run.
	 * 
	 * @return void
	 */
	public function cleanup()
	{
		$this->moveInserted();
		$this->removeDeletedIds();
		$this->updateShadowData();
		
		$this->reset();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Resets this unit of work.
	 * 
	 * @return void
	 */
	public function reset()
	{
		// Reset this Unit of Work
		$this->new_entities =
			$this->deleted_entities =
			$this->multi_delete =
			$this->multi_update = array();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Moves the inserted rows to the $entities array with the uid as key.
	 * 
	 * @return void
	 */
	protected function moveInserted()
	{
		foreach($this->new_entities as $e)
		{
			$this->entities[implode('$', $e->__id)] = $e;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Resets the __id array of the deleted objects.
	 * 
	 * @return void
	 */
	public function removeDeletedIds()
	{
		foreach($this->deleted_entities as $entity)
		{
			$entity->__id = array();
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates insertions for objects, preferably with a single larger query if
	 * possible (ie. when not using DB generated data).
	 * 
	 * @return void
	 */
	abstract protected function processSingleInsertions();
	
	// ------------------------------------------------------------------------

	/**
	 * Diffs all loaded entity objects and determines if they have been changed,
	 * if they have, their changes are committed to the database.
	 * 
	 * Use the objects in $entities.
	 * 
	 * @return void
	 */
	abstract protected function processSingleChanges();
	
	// ------------------------------------------------------------------------

	/**
	 * This method deletes all the objects existing in $deleted_entities from the
	 * database.
	 * 
	 * Delete objects stored in $deleted_entities
	 * 
	 * @return void
	 */
	abstract protected function processSingleDeletions();
	
	// ------------------------------------------------------------------------

	/**
	 * Updates the __data array in the objects.
	 * 
	 * @return void
	 */
	abstract protected function updateShadowData();
}


/* End of file UnitOfWork.php */
/* Location: ./lib/Rdm */