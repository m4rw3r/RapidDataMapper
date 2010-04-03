<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Object handling filter generation for Rdm_Collection objects.
 */
class Rdm_Collection_Filter
{
	/**
	 * A list of filters which are to be imploded to a filter string.
	 * 
	 * @var array
	 */
	protected $filters = array();
	
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
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct($parent = null)
	{
		$this->parent = $parent;
		$this->is_locked =& $parent->is_locked;
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
			// TODO: Better exception message and proper exception class
			throw new Exception('Object is locked');
		}
		
		$c = get_class($this);
		
		empty($this->filters) OR $this->filters[] = 'AND';
		
		return $this->filters[] = new $c($this);
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
			// TODO: Better exception message and proper exception class
			throw new Exception('Object is locked');
		}
		
		$c = get_class($this);
		
		empty($this->filters) OR $this->filters[] = 'OR';
		
		return $this->filters[] = new $c($this);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds an SQL fragment to this filter, concatenates with an AND part to the
	 * previous condition, if there is one.
	 * 
	 * @param  string	Raw SQL fragment
	 * @param  array	A list of key => values to be escaped and inserted into the $sql
	 * @return self
	 */
	public function fragment($sql, $binds = array())
	{
		// TODO: Code
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