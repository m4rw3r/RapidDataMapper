<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class describing a relationship.
 */
class Db_Descriptor_Relation
{
	/**
	 * The name of this relation.
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * The name of the property in which the related data will be placed.
	 * 
	 * @var string
	 */
	protected $property;
	
	/**
	 * The class name of the related object.
	 * 
	 * @var string
	 */
	protected $related_class;
	
	/**
	 * The relationship type.
	 * 
	 * @var int
	 */
	protected $type;
	
	/**
	 * The parent descriptor.
	 * 
	 * @var Db_Descriptor
	 */
	protected $desc_parent;
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the parent descriptor.
	 * 
	 * @return Db_Descriptor
	 */
	public function getParentDescriptor()
	{
		if(empty($this->desc_parent))
		{
			throw new Db_Exception_Descriptor_MissingDescriptor('parent');
		}
		
		return $this->desc_parent;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the parent descriptor for this relation (ie. the descriptor describing
	 * the class this relation relates from).
	 * 
	 * @param  Db_Descriptor
	 * @return self
	 */
	public function setParentDescriptor(Db_Descriptor $parent)
	{
		$this->desc_parent = $parent;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of this relation.
	 * 
	 * @return strin
	 */
	public function getName()
	{
		if(empty($this->name))
		{
			throw new Db_Exception_Descriptor_MissingName();
		}
		
		return $this->name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the name of this relation.
	 * 
	 * @return self
	 */
	public function setName($name)
	{
		$this->name = $name;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the property to place the related data in.
	 * 
	 * Default:
	 * <code>
	 * return strtolower($this->getName());
	 * </code>
	 * 
	 * @return string
	 */
	public function getProperty()
	{
		return empty($this->property) ? strtolower($this->getName()) : $this->property;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the property name in which the related data will be placed.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setProperty($prop)
	{
		$this->property = $prop;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name of the related object.
	 * 
	 * Default:
	 * <code>
	 * return ucfirst(Db_Inflector::singularize($this->getProperty()));
	 * </code>
	 * 
	 * @return string
	 */
	public function getRelatedClass()
	{
		return empty($this->related_class) ? ucfirst(Db_Inflector::singularize($this->getProperty())) : $this->related_class;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the class name of the related object.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setRelatedClass($class)
	{
		$this->related_class = $class;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the related descriptor object.
	 * 
	 * @return Db_Descriptor
	 */
	public function getRelatedDescriptor()
	{
		return Db::getDescriptor($this->getRelatedClass());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the relationship type.
	 * 
	 * @return int
	 */
	public function getType()
	{
		return empty($this->type) ? $this->guessType() : $this->type;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the relationship type.
	 * 
	 * @param  int
	 * @return self
	 */
	public function setType($type)
	{
		$this->type = $type;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Guesses the relationship type based on the property name and if a foreign key exists in the local object.
	 * 
	 * @return int
	 */
	protected function guessType()
	{
		// Singular or plural?
		if(substr($this->getProperty(), -1) == 's')
		{
			// plural
			return Db_Descriptor::HAS_MANY;
		}
		else
		{
			$cls = new ReflectionClass($this->getParentDescriptor()->getClass());
			
			try
			{
				$prop = $cls->getProperty($this->getRelatedDescriptor()->getSingular().'_id');
			}
			catch(ReflectionException $e)
			{
				// Did not find a property
				return Db_Descriptor::OWNED_BY;
			}
			
			// Found property
			return Db_Descriptor::HAS_ONE;
		}
	}
}

/* End of file Relation.php */
/* Location: ./lib/Db/Descriptor */