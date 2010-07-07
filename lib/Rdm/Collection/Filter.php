<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Object handling filter generation for Rdm_Collection objects.
 */
class Rdm_Collection_Filter implements Rdm_Collection_FilterInterface
{
	/**
	 * A list of filters which are to be imploded to a filter string.
	 * 
	 * @var array
	 */
	protected $filters = array();
	
	/**
	 * A list of modifying objects which will be able to modify objects
	 * passed to modifyToMatch().
	 * 
	 * @var array()
	 */
	protected $modifiers = array();
	
	/**
	 * If this object contains dynamic filters, like id < 34.
	 * 
	 * @var boolean
	 */
	protected $is_dynamic = false;
	
	/**
	 * The table alias which should be prepended to the column names.
	 * 
	 * @var string
	 */
	protected $table_alias;
	
	/**
	 * The parent object, used for method chaining.
	 * 
	 * @var Rdm_Collection|Rdm_Collection_Filter
	 */
	protected $parent = null;
	
	/**
	 * Variable which is referenced to the parent's $is_locked property.
	 * 
	 * @var boolean
	 */
	public $is_locked = false;
	
	/**
	 * The database adapter used by this filter instance to escape filter data.
	 * 
	 * @var Rdm_Adapter
	 */
	public $db = null;
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct($parent, Rdm_Adapter $db, $parent_alias = '')
	{
		$this->parent = $parent;
		$this->is_locked =& $parent->is_locked;
		$this->db = $db;
		$this->table_alias = $parent_alias;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Ends the current filter block.
	 * 
	 * @return Rdm_Collection|Rdm_Collection_Filter
	 */
	public function end()
	{
		return $this->parent;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Starts a subfilter, it will be of the same class as this filter and it will
	 * be nested inside a parenthesis.
	 * 
	 * @return Rdm_Collection_Filter
	 */
	public function has()
	{
		if($this->is_locked)
		{
			throw Rdm_Collection_Exception::objectLocked();
		}
		
		$c = get_class($this);
		
		empty($this->filters) OR $this->filters[] = 'AND';
		
		// Add so we later on can modify objects using modifyToMatch()
		$this->modifiers[] = $o = new $c($this);
		$this->filters[] = $o;
		
		return $o;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Starts a subfilter, it will be of the same class as this filter and it will
	 * be nested inside a parenthesis, it will be preceded by OR if there is a previous condition.
	 * 
	 * @return Rdm_Collection_Filter
	 */
	public function orHas()
	{
		if($this->is_locked)
		{
			throw Rdm_Collection_Exception::objectLocked();
		}
		
		$this->is_dynamic = true;
		
		$c = get_class($this);
		
		empty($this->filters) OR $this->filters[] = 'OR';
		
		$this->filters[] = $o = new $c($this);
		
		return $o;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds an SQL fragment to this filter, concatenates with an AND part to the
	 * previous condition, if there is one, use ":alias" to get the table alias.
	 * 
	 * @param  string	Raw SQL fragment
	 * @param  array	A list of key => values to be escaped and inserted into the $sql
	 * @return self
	 */
	public function sql($sql, $parameters = array())
	{
		$this->is_dynamic = true;
		
		empty($this->filters) OR $this->filters[] = 'AND';
		
		$sql = preg_replace('/:alias\b/', $this->table_alias, $sql);
		
		$this->filters[] = $this->db->bindParameters($sql, $parameters);
		
		return $this;
	}
	
	// ------------------------------------------------------------------------
	
	public function canModifyToMatch()
	{
		if($this->is_dynamic)
		{
			return false;
		}
		
		foreach($this->modifiers as $mod)
		{
			if( ! $mod->canModifyToMatch())
			{
				return false;
			}
		}
		
		return true;
	}
	
	// ------------------------------------------------------------------------
	
	public function modifyToMatch($object)
	{
		foreach($this->modifiers as $mod)
		{
			$mod->modifyToMatch($object);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __toString()
	{
		return '('.implode(' ', $this->filters).')';
	}
}


/* End of file Filter.php */
/* Location: ./lib/Rdm/Collection */