<?php
/*
 * Created by Martin Wernståhl on 2009-05-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Specialized select query which can keep track of loaded classes.
 */
class Db_Query_MapperSelect extends Db_Query_Select
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
	
	/**
	 * A prefix for the WHERE conditions, prepended once before the first condition.
	 * 
	 * Usually something like this:
	 * <code>
	 * "user_id = 12 AND ("
	 * </code>
	 * 
	 * @var string
	 */
	public $where_prefix = '';
	
	/**
	 * A suffix for the WHERE conditions, appended once after the last condition.
	 * 
	 * Usually an ending parenthesis (")").
	 * 
	 * @var string
	 */
	public $where_suffix = '';
	
	// ------------------------------------------------------------------------

	/**
	 * Populates the object with 
	 * 
	 * @param  Db_Mapper
	 * @param  string
	 */
	public function __construct($mapper, $main_object)
	{
		$this->_instance = $mapper->getConnection();
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
	public function getOne()
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
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		if(empty($this->from))
		{
			throw new Db_Exception_QueryIncomplete('Missing FROM part');
		}
		
		$str = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '') . implode(', ', $this->columns) . "\nFROM " . implode(', ', $this->from);
		
		if( ! empty($this->join))
		{
			$str .= "\n" . implode("\n", $this->join);
		}
		
		if( ! empty($this->where))
		{
			$str .= "\nWHERE " . $this->where_prefix.implode(' ', $this->where).$this->where_suffix;
		}
		elseif( ! empty($this->where_prefix))
		{
			// TODO: Should the trim methods be here? Does it affect the SQL (except for removing parenthesis)?
			
			$str .= "\nWHERE ".rtrim($this->where_prefix, ' (').' '.ltrim($this->where_suffix, ' )');
		}
		
		if( ! empty($this->group_by))
		{
			$str .= "\nGROUP BY " . implode(', ', $this->group_by);
		}
		
		if( ! empty($this->having))
		{
			$str .= "\nHAVING " . implode(' ', $this->having);
		}

		if( ! empty($this->order_by))
		{
			$str .= "\nORDER BY " . implode(', ', $this->order_by);
		}

		if($this->limit !== false)
		{
			$str = $this->_instance->_limit($str, $this->limit, $this->offset);
		}
		
		return $str;
	}
}


/* End of file MapperSelect.php */
/* Location: ./lib/mapper/query */