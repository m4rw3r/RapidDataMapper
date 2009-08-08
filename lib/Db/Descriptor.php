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
	
	/**
	 * Contains a list of the primary keys described by this object.
	 * 
	 * @var array
	 */
	protected $primary_keys = array();
	
	/**
	 * Contains a list of the properties described by this object.
	 * 
	 * @var array
	 */
	protected $properties = array();
	
	/**
	 * Contains a list of the relations described by this object.
	 * 
	 * @var array
	 */
	protected $relations = array();
	
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
	public function setTable($table)
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
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a descriptor object to this descriptor.
	 * 
	 * @param  Db_Descriptor_Column|Db_Descriptor_PrimaryKey|Db_Descriptor_Relation
	 * @return self
	 */
	public function add($object)
	{
		// is it of allowed type?
		foreach(array(
				'relations' => 'Db_Descriptor_Relation',
				'primary_keys' => 'Db_Descriptor_PrimaryKey',
				'properties' => 'Db_Descriptor_Column'
				) as $k => $cls)
		{
			if($object instanceof $cls)
			{
				// assign it to the proper property
				$this->{$k}[$object->getProperty()] = $object;
				
				return $this;
			}
		}
		
		throw new InvalidArgumentException(is_object($object) ? get_class($object) : gettype($object));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns an array of primary keys described in this descriptor.
	 * 
	 * @return array
	 */
	public function getPrimaryKeys()
	{
		return $this->primary_keys;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns an array of columns described in this descriptor.
	 * 
	 * @return array
	 */
	public function getColumns()
	{
		return $this->properties;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns an array of relations described in this descriptor.
	 * 
	 * @return 
	 */
	public function getRelations()
	{
		return $this->relations;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new column descriptor to be used by this descriptor.
	 * 
	 * @return Db_Descriptor_Column
	 */
	public function newColumn($name)
	{
		// TODO: More options directly in this method
		
		$c = new Db_Descriptor_Column();
		$c->setColumn($name);
		
		return $c;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new primary key descriptor to be used by this descriptor.
	 * 
	 * @return Db_Descriptor_PrimaryKey
	 */
	public function newPrimaryKey($name)
	{
		// TODO: More options directly in this method
		
		$pk = new Db_Descriptor_PrimaryKey();
		$pk->setColumn($name);
		
		return $pk;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new relation descriptor to be used by this descriptor.
	 * 
	 * @return Db_Descriptor_Relation
	 */
	public function newRelation($name)
	{
		// TODO: More options directly in this method
		
		$r = new Db_Descriptor_Relation($this);
		$r->setName($name);
		
		return $r;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a new instance of a mapper builder.
	 * 
	 * @return Db_Mapper_Builder
	 */
	public function getBuilder()
	{
		return new Db_Mapper_Builder($this);
	}
}



/* End of file Descriptor.php */
/* Location: ./lib/Db */