<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class describing the mapping between a class and a table.
 */
class Db_Descriptor
{
	/**
	 * The class this object describes.
	 * 
	 * @var string
	 */
	protected $class;
	
	/**
	 * The table the described class maps to.
	 * 
	 * @var string
	 */
	protected $table;
	
	/**
	 * The singular name of the described class.
	 * 
	 * @var string
	 */
	protected $singular;
	
	/**
	 * The factory to use to create objects of the described class.
	 * 
	 * @var string
	 */
	protected $factory;
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name which this descriptor describes.
	 * 
	 * @throws Db_Exception_Descriptor_MissingClassName
	 * @return string
	 */
	public function getClass()
	{
		if(empty($this->class))
		{
			throw new Db_Exception_Descriptor_MissingClassName();
		}
		
		return $this->class;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the class to describe.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setClass($class)
	{
		$this->class = $class;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the singular name of the described object.
	 * 
	 * Default:
	 * <code>
	 * return strtolower($this->getClass());
	 * </code>
	 * 
	 * @return string
	 */
	public function getSingular()
	{
		return empty($this->singular) ? strtolower($this->getClass()) : $this->singular;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the singular name of the described object.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setSingular($singular)
	{
		$this->singular = $singular;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the table the described class will be mapped to.
	 * 
	 * Default:
	 * <code>
	 * return Db_Inflector::pluralize($this->getSingular());
	 * </code>
	 * 
	 * @return string
	 */
	public function getTable()
	{
		return empty($this->table) ? $this->table = Db_Inflector::pluralize($this->getSingular()) : $this->table;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the table to use by the described class.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setTable()
	{
		$this->table = $table;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the call which will create an object of the described class.
	 * 
	 * Default:
	 * <code>
	 * return 'new '.$this->getClass();
	 * </code>
	 * 
	 * @return string
	 */
	public function getFactory()
	{
		return empty($this->factory) ? 'new '.$this->getClass() : $this->factory;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Set the factory to create instances of the described object.
	 * 
	 * NOTE:
	 * The factory must be a one-liner to be fitted between "=" and ";".
	 * 
	 * @param  string
	 * @return self
	 */
	public function setFactory($factory)
	{
		$this->factory = $factory;
		
		return $this;
	}
}



/* End of file Descriptor.php */
/* Location: ./lib/Db */