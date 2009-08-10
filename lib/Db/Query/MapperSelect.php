<?php
/*
 * Created by Martin Wernståhl on 2009-05-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Specialized select query which can keep track of loaded classes.
 */
class Db_Query_MapperQuery extends Db_Query_Select
{
	/**
	 * The linked mapper.
	 * 
	 * @var object
	 */
	protected $mapper;
	
	/**
	 * A list of the mapped objects, ie. their aliases and their classes.
	 *
	 * @var array
	 */
	public $mapped_objects = array();
	
	/**
	 * A tree-stack with related objects.
	 * 
	 * @var array
	 */
	public $alias_paths = array();
	
	/**
	 * The main object alias
	 *
	 * @var string
	 */
	public $main_object;
	
	// ------------------------------------------------------------------------

	/**
	 * Populates the object with 
	 * 
	 * @param  Db_Mapper
	 * @param  string
	 */
	public function __construct($mapper, $main_object)
	{
		$this->_instance = $mapper->getDatabase();
		$this->mapper = $mapper;
		$this->main_object = $main_object;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Loads the related records with the names specified.
	 * 
	 * @param  string|array
	 * @return self
	 */
	public function related($related)
	{
		// TODO: Old code, rewrite?
		$related = (Array) $related;
		
		foreach($related as $rel)
		{
			$rel = explode('-', $rel);
			
			$mapper = $this->mapper;
			$old_alias = $this->main_object;
			$curr_alias =& $this->alias_paths;
			
			// make relations to related records
			foreach($rel as $relation)
			{
				if( ! isset($mapper->relations[$relation]))
				{
					throw new Db_Exception_MissingRelation("'".$mapper->class.'.'.$relation.'"');
				}
				
				if( ! isset($this->mapped_objects[$old_alias.'-'.$relation]))
				{
					$mapper->joinRelated($this, $relation, $old_alias);
				}
				
				// add to the relationship cascade
				$curr_alias[$relation] = array();
				
				// save the relationship class name
				$this->mapped_objects[$old_alias.'-'.$relation] = $mapper->relations[$relation];
				
				// go deeper
				$old_alias = $old_alias.'-'.$relation;
				$curr_alias =& $curr_alias[$relation];
				$mapper = Db::getMapper($mapper->relations[$relation]);
			}
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the result of this query.
	 * 
	 * @return array
	 */
	public function get()
	{
		return $this->_get();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns one result object object.
	 * 
	 * @return object|false
	 */
	public function get_one()
	{
		// TODO: Limit to one object from the db
		$ret = $this->_get();
		
		// we only fetch one object, don't return an array
		return empty($ret) ? false : array_shift($ret);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * A common method, doing the most work of the fetching.
	 * 
	 * @return array|object|false
	 */
	protected function _get()
	{
		// execute the query
		$ret = $this->_instance->query($this->__toString());
		
		// let the mapper convert the data into objects
		$ret = $this->mapper->extract($ret, $this->mapped_objects, $this->alias_paths, $this->main_object);
		
		return $ret;
	}
}


/* End of file query.php */
/* Location: ./lib/mapper */