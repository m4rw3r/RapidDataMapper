<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
abstract class Rdm_Collection implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * Internal: If this flag is true, this object has already been used with entity objects,
	 * therefore it can no longer use filters.
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
	 * Internal: The alias of the current table.
	 * 
	 * @internal
	 * @var string
	 */
	public $table_alias = '';
	
	/**
	 * Internal: A list of filter objects
	 * 
	 * @internal
	 * @var array(Collection_Filter)
	 */
	public $filters = array();
	
	/**
	 * Internal: A list of objects which can modify an add()ed object using
	 * modifyToMatch().
	 * 
	 * @var array()
	 */
	protected $modifiers = array();
	
	/**
	 * Internal: A list of joined relation names and their collection objects.
	 * 
	 * @var array(string => Rdm_Collection)
	 */
	public $with = array();
	
	/**
	 * The limit which will be used in the SQL limit of the query.
	 * 
	 * @var false|int
	 */
	protected $limit = false;
	
	/**
	 * The offset which will be used in the SQL OFFSET of the query.
	 * 
	 * @var false|int
	 */
	protected $offset = false;
	
	/**
	 * Internal: The array of data objects.
	 * 
	 * @var array(Object)
	 */
	public $contents = array();
	
	/**
	 * Variable caching the SELECT COUNT() query.
	 * 
	 * NOTE: Clean it when calling limit(), offset etc.
	 * 
	 * @var int
	 */
	protected $num_rows = null;
	
	/**
	 * Parent collection when they are joined.
	 * 
	 * @var Rdm_Collection
	 */
	protected $parent = null;
	
	/**
	 * Internal: Reference to the relation filter employed by this collection
	 * if it is a sub-collection (ie. JOINed).
	 * 
	 * @var Rdm_Collection_FilterInterface
	 */
	public $relation = null;
	
	/**
	 * Internal: Relation id of the parent's relation with this collection.
	 * 
	 * @var int
	 */
	public $relation_id = null;
	
	/**
	 * Internal: An integer telling which type of relation this collection has with parent.
	 * 
	 * @var int
	 */
	public $join_type = null;
	
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Returns the UnitOfWork instance used by this collection.
	 * 
	 * @internal
	 * @return Rdm_UnitOfWork
	 */
	public static function getUnitOfWork()
	{
		throw Rdm_Collection_Exception::missingMethod(__METHOD__);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new instance of this class.
	 * 
	 * @return Rdm_Collection
	 */
	public static function create()
	{
		throw Rdm_Collection_Exception::missingMethod(__METHOD__);
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
		throw Rdm_Collection_Exception::missingMethod(__METHOD__);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Will register the passed object for deletion from the database.
	 * 
	 * @param  Object
	 * @return Object
	 */
	public static function delete($object)
	{
		throw Rdm_Collection_Exception::missingMethod(__METHOD__);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Pushes all changes to entities to the database.
	 * 
	 * @param  boolean  If to only push changes for this collection class's entities
	 * @return void
	 */
	public static function pushChanges($private_push = false)
	{
		throw Rdm_Collection_Exception::missingMethod(__METHOD__);
	}
	
	// ------------------------------------------------------------------------
	// --  FETCH RELATED METHODS                                             --
	// ------------------------------------------------------------------------
	
	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct($parent = null, $relation = null, $table_alias = '')
	{
		// TODO: Enable syntax like this: new TrackCollection($artist); where $artist owns a set of tracks
		
		if($parent)
		{
			$this->parent = $parent;
			$this->is_locked =& $parent->is_locked;
			
			// The relationship type
			$this->relation = $relation;
			$this->relation_id = $relation->id;
			$this->join_type = $relation->type;
			// -1 is reserved for the relation filter
			$this->filters[-1] = $this->relation;
			$this->modifiers[-1] = $this->relation;
			
			// The alias the parent object tells us to use
			$this->table_alias = $table_alias;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Clones the possible relation filter.
	 * 
	 * @return void
	 */
	public function __clone()
	{
		if( ! empty($this->relation))
		{
			$this->relation = clone $this->relation;
			// We need to fix the filter too
			$this->filters[-1] = $this->relation;
			$this->modifiers[-1] = $this->relation;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the parent collection object in case this collection was creted
	 * as a join to the first using with().
	 * 
	 * @return Rdm_Collection
	 */
	public function end()
	{
		return $this->parent;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Returns the relationship type which this collection has been joined with.
	 * 
	 * @internal
	 * @return int
	 */
	public function getJoinType()
	{
		return $this->join_type;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Returns the adapter instance for this Collection instance.
	 * 
	 * @return Rdm_Adapter
	 */
	public abstract function getAdapter();
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Returns the UnitOfWork instance for this Collection instance,
	 * PHP 5.2 compatibility.
	 * 
	 * @return Rdm_UnitOfWork
	 */
	public abstract function getUnitOfWorkInstance();
	
	// ------------------------------------------------------------------------
	// --  SELECT QUERY RELATED METHODS                                      --
	// ------------------------------------------------------------------------

	/**
	 * Joins the relation with the supplied identifier.
	 * 
	 * @param  int  Integer from a class constant identifying the relation
	 * @return Rdm_Collection  <Class>Collection
	 */
	abstract public function with($relation_id);
	
	/**
	 * Internal: Creates the SELECT part of the query, does not include the SELECT keyword.
	 * 
	 * @internal
	 * @param  array   The list of columns, these will later be joined with ", " between them
	 * @param  array   A list to keep track of which column goes where, aliases are not
	 *                 used, so therefore storing the columns integer index is important.
	 *                 To add a column there, just add it at the end with
	 *                 $column_mappings[] = 'column';
	 * @return void
	 */
	abstract public function createSelectPart(&$list, &$column_mappings);
	
	/**
	 * Internal: Creates the COUNT() part for a select.
	 * 
	 * This query should only count the entities managed by the current collection.
	 * 
	 * @internal
	 * @param  string  The SQL query, beginning at FROM (missing LIMIT part, but includes JOIN + WHERE)
	 * @return string  The SQL query with the SELECT part
	 */
	abstract public function createSelectCountPart($from);
	
	/**
	 * Internal: Creates the FROM and JOIN part of the query, does not includes the FROM keyword.
	 * 
	 * @internal
	 * @param  string  The alias of the parent table, if this collection is joined onto another
	 *                 False if this is the root Collection object
	 * @param  array   The list of parts which is to be inserted into the space where
	 *                 the FROM clause will be, they will be joined with "\n" as the separator
	 * @return void
	 */
	abstract public function createFromPart($parent_alias, &$list);
	
	/**
	 * Internal: Hydrates the result row into objects.
	 * 
	 * @internal
	 * @param  array       The result row with integer indexed columns
	 * @param  array       The result array with primary keys as the keys
	 * @param  array       The column map which describes which column resides in which index
	 * @return void|false  False if there is no object to hydrate
	 */
	abstract public function hydrateObject(&$row, &$result, &$map);
	
	// ------------------------------------------------------------------------
	
	/**
	 * Internal: Creates a select query for this Collection object.
	 * 
	 * Can be used for debugging or dumping the SQL to see if the collection
	 * will generate the desired SQL.
	 * 
	 * @return array(string, array(int => string))  SQL and column mappings
	 */
	public function createSelectQuery()
	{
		$this->is_locked = true;
		
		$select = array();
		$from = array();
		$column_mappings = array();
		
		$this->createSelectPart($select, $column_mappings);
		$this->createFromPart(false, $from);
		
		$sql = 'SELECT '.implode(', ', $select)."\n".implode("\n", $from);
		
		if( ! empty($this->filters))
		{
			$sql .= "\nWHERE ".implode(' ', $this->filters);
		}
		
		if($this->limit !== false OR $this->offset !== false)
		{
			$sql = $this->getAdapter()->limitSqlQuery($sql, $this->limit, $this->offset);
		}
		
		return array($sql, $column_mappings);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Creates a count query which counts the data matching the filters of this
	 * object.
	 * 
	 * @return string
	 */
	public function createSelectCountQuery()
	{
		$this->is_locked = true;
		
		$from = array();
		
		$this->createFromPart(false, $from);
		
		$sql = implode("\n", $from);
		
		if( ! empty($this->filters))
		{
			$sql .= "\nWHERE ".implode(' ', $this->filters);
		}
		
		$sql = $this->createSelectCountPart($sql);
		
		return $sql;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Creates a query which returns a list of all the specified columns of this collection.
	 * 
	 * @param  string   The select part of the query, no escaping will take place!
	 * @return string
	 */
	public function createSelectColumnsQuery($select)
	{
		$from = array();
		
		$this->createFromPart(false, $from);
		
		$sql = "\n".implode("\n", $from);
		
		if( ! empty($this->filters))
		{
			$sql .= "\nWHERE ".implode(' ', $this->filters);
		}
		
		$sql = 'SELECT '.$select.$sql;
		
		if($this->limit !== false)
		{
			$sql = $this->getAdapter()->limitSqlQuery($sql, $this->limit, $this->offset);
		}
		
		return $sql;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Flags this collection as populated.
	 * 
	 * @internal
	 * @return void
	 */
	public function setPopulated()
	{
		$this->is_populated = true;
	}
	
	// ------------------------------------------------------------------------
	// --  FILTER RELATED METHODS                                            --
	// ------------------------------------------------------------------------
	
	/**
	 * Internal: Creates a new instance of the appropriate Rdm_Collection_Filter.
	 * 
	 * @internal
	 * @return Rdm_Collection_Filter
	 */
	abstract protected function createFilterInstance();
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new filter object which will be filtering the future contents
	 * of this collection, if there already is a filter, AND will be prepended.
	 * 
	 * @return Rdm_Collection_Filter
	 */
	public function has()
	{
		if($this->is_locked)
		{
			throw Rdm_Collection_Exception::objectLocked();
		}
		
		empty($this->filters) OR $this->filters[] = 'AND';
		
		$this->modifiers[] = $o = $this->createFilterInstance();
		$this->filters[] = $o;
		
		return $o;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new filter object which will be filtering the future contents
	 * of this collection, if there already is a filter, OR will be prepended.
	 * 
	 * @return Rdm_Collection_Filter
	 */
	public function orHas()
	{
		if($this->is_locked)
		{
			throw Rdm_Collection_Exception::objectLocked();
		}
		
		empty($this->filters) OR $this->filters[] = 'OR';
		
		$this->modifiers[] = $o = $this->createFilterInstance();
		$this->filters[] = $o;
		
		return $o;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Limits the number of rows that should be fetched from the database.
	 * 
	 * @param  int
	 * @param  false|int
	 * @return self
	 */
	public function limit($num, $offset = false)
	{
		// TODO: Have an idea for limit of related rows, but not sure about the implementation's efficiency, throw exception for now
		if( ! is_null($this->parent))
		{
			throw Rdm_Collection_Exception::notRootObject(get_class($this));
		}
		
		// Cannot limit number of fetched objects if they already have been fetched
		if($this->is_populated)
		{
			throw Rdm_Collection_Exception::objectAlreadyPopulated();
		}
		
		if($offset != false)
		{
			$this->offset($offset);
		}
		
		$this->limit = (Int) $num;
		
		// Reset the count, we have to do a recount because we will probably get less rows now
		$this->num_rows = null;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the SQL offset value.
	 * 
	 * @param  int
	 * @return self
	 */
	public function offset($num)
	{
		if( ! is_null($this->parent))
		{
			throw Rdm_Collection_Exception::notRootObject(get_class($this));
		}
		
		// Cannot set offset of fetched objects if they already have been fetched
		if($this->is_populated)
		{
			throw Rdm_Collection_Exception::objectAlreadyPopulated();
		}
		
		$this->offset = (Int) $num;
		
		// Reset the count, we have to do a recount because we will probably get less rows now
		$this->num_rows = null;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __toString()
	{
		// TODO: Replace or remove, this is currently for debug
		return $this->createSelectQuery();
	}
	
	// ------------------------------------------------------------------------
	// --  ENTITY RELATED METHODS                                            --
	// ------------------------------------------------------------------------
	
	/**
	 * Populates this object with entities which match the specified filters.
	 * 
	 * @return void
	 */
	public function populate()
	{
		if( ! is_null($this->parent))
		{
			throw Rdm_Collection_Exception::notRootObject(get_class($this));
		}
		
		$this->is_locked = true;
		
		list($sql, $map) = $this->createSelectQuery();
		
		// Flip so that the columns becomes the keys, faster column index lookup
		$map = array_flip($map);
		
		// Empty the contents, to prevent newly added objects from appearing first
		// no matter how it was sorted
		$this->contents = array();
		
		$result = $this->getAdapter()->query($sql);
		
		while($row = $result->nextArray())
		{
			$this->hydrateObject($row, $this->contents, $map);
		}
		
		$this->is_populated = true;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Converts the supplied entity to an XML fragment.
	 * 
	 * Format:
	 * <code>
	 * <singular>
	 *     <property>value</property>
	 * </singular>
	 * </code>
	 * 
	 * @param  object
	 * @return string
	 */
	public static function entityToXML($entity)
	{
		throw Rdm_Collection_Exception::missingMethod(__METHOD__);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Converts this collection's data into XML.
	 * 
	 * Format:
	 * <code>
	 * <plural>
	 *     <singular>
	 *         <property>value</property>
	 *     </singular>
	 *     <singular>
	 *         <property>value</property>
	 *     </singular>
	 * </plural>
	 * </code>
	 * 
	 * @return string
	 */
	public function toXML()
	{
		// TODO: Code
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Adds an entity to this collection, this collection will be locked and
	 * the entity will assume data which matches the filters of this collection.
	 * 
	 * @param  Object
	 * @return self
	 */
	abstract public function add($object);
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Contains the common instructions for add(), will perform the calls to
	 * filters and check the contents of the internal array.
	 * 
	 * @param  Object
	 * @return boolean  True if the object already is in the collection
	 */
	protected function _add($object)
	{
		$this->is_locked = true;
		
		// OR cannot decide what side of the filters we need to modify
		if(in_array('OR', $this->filters, true))
		{
			throw Rdm_Collection_Exception::filterCannotModify();
		}
		
		// Check if we already have it
		if($this->is_populated && in_array($object, $this->contents, true))
		{
			// Yes, we're done
			return true;
		}
		
		// Check that the subfilters doesn't contain anything simila
		foreach($this->modifiers as $mod)
		{
			if( ! $mod->canModifyToMatch())
			{
				throw Rdm_Collection_Exception::filterCannotModify();
			}
		}
		
		// Modify the object
		foreach($this->modifiers as $mod)
		{
			$mod->modifyToMatch($object);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Removes the object from this collection, this collection will be locked
	 * and the entity's values will be set so that they no longer match filters.
	 * 
	 * @param  Object
	 * @return self
	 */
	abstract public function remove($object);
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Contains the common instructions for remove(), will perform the calls to
	 * filters and check the contents of the internal array.
	 * 
	 * @param  Object
	 * @return void
	 */
	protected function _remove($object)
	{
		$this->is_locked = true;
		
		// Check that the subfilters doesn't contain anything which cannot be applied
		foreach($this->modifiers as $mod)
		{
			if( ! $mod->canModifyToMatch())
			{
				throw Rdm_Collection_Exception::filterCannotModify();
			}
		}
		
		// Modify the object
		foreach($this->modifiers as $mod)
		{
			$mod->modifyToNotMatch($object);
		}
		
		// Remove it if it exists
		if(($i = array_search($object, $this->contents, true)) !== false)
		{
			unset($this->contents[$i]);
		}
		
		return true;
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
	abstract public function deleteAll();
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if this collection is empty.
	 * 
	 * NOTE: PHP's empty() function will always return true because collections
	 * are objects.
	 * 
	 * @return boolean
	 */
	public function isEmpty()
	{
		return $this->is_populated ? empty($this->contents) : $this->count() == 0;
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
		if(is_null($offset))
		{
			// $collection[] = $object; syntax
			$this->add($value);
		}
		else
		{
			// TODO: Implement?
			throw new Exception('Not yet implemented');
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function offsetUnset($offset)
	{
		// TODO: Implement? Usable as a shortcut for remove()
		throw new Exception('Not yet implemented');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Counts the number of objects in this collection.
	 * 
	 * @return int
	 */
	public function count()
	{
		if( ! $this->is_populated)
		{
			if( ! is_null($this->parent))
			{
				throw Rdm_Collection_Exception::notRootObject(get_class($this));
			}
			
			// Do we have it cached?
			if(is_null($this->num_rows))
			{
				// Nope
				$c = $this->getAdapter()->query($this->createSelectCountQuery())->val();
				
				if($this->limit !== false)
				{
					// We have a limit, get the total count, then return the limit if the count is greater
					// also compensate for the offset
					$this->num_rows = ($c - $this->offset) > $this->limit ? $this->limit : $c - $this->offset;
					$this->num_rows < 0 && $this->num_rows = 0;
				}
				elseif($this->offset !== false)
				{
					// We have offset, but no limit of the rows, it will result in an SQL error if we fetch it
					// like that, show the error now to make sure the user seees it
					throw Rdm_Adapter_QueryException::offsetWithoutLimit();
				}
				else
				{
					// No limit, just get the raw query value
					$this->num_rows = $c;
				}
			}
			
			return $this->num_rows;
		}
		else
		{
			return count($this->contents);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the iterator for iteration of this collection.
	 * 
	 * @return Iterator
	 */
	public function getIterator()
	{
		$this->is_populated OR $this->populate();
		
		return new ArrayIterator($this->contents);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the first element of this collection.
	 * 
	 * @return object|false
	 */
	public function first()
	{
		$this->is_populated OR $this->populate();
		
		return reset($this->contents);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the last element of this collection.
	 * 
	 * @return object|false
	 */
	public function last()
	{
		$this->is_populated OR $this->populate();
		
		return end($this->contents);
	}
}


/* End of file Collection.php */
/* Location: ./lib/Rdm */