<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class describing a relationship.
 */
class Rdm_Descriptor_Relation
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
	 * If to load this relation eagerly.
	 * 
	 * @var bool
	 */
	protected $eager_loading = false;
	
	/**
	 * What to do ON DELETE.
	 * 
	 * @var int
	 */
	protected $on_delete;
	
	/**
	 * The parent descriptor.
	 * 
	 * @var Rdm_Descriptor
	 */
	protected $desc_parent;
	
	/**
	 * The object handling the relation-unique things.
	 * 
	 * @var Rdm_Descriptor_RelationInterface
	 */
	protected $handler;
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the parent descriptor.
	 * 
	 * @throws Rdm_Descriptor_Exception
	 * @return Rdm_Descriptor
	 */
	public function getParentDescriptor()
	{
		if(empty($this->desc_parent))
		{
			throw new Rdm_Descriptor_Exception('parent', 'No parent exception for instance of Rdm_Descriptor_Relation.');
		}
		
		return $this->desc_parent;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the parent descriptor for this relation (ie. the descriptor describing
	 * the class this relation relates from).
	 * 
	 * @param  Rdm_Descriptor
	 * @return self
	 */
	public function setParentDescriptor(Rdm_Descriptor $parent)
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
			throw new Rdm_Descriptor_MissingValueException('relation name', $this->getParentDescriptor()->getClass());
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
	 * Returns this relation's unique identifier id.
	 * 
	 * @return int
	 */
	public function getIntegerIdentifier()
	{
		return $this->int_identifier;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Used internally to assign this relation an unique id.
	 * 
	 * @param  int
	 * @return void
	 */
	public function setIntegerIdentifier($int)
	{
		$this->int_identifier = $int;
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
	 * return ucfirst(Rdm_Util_Inflector::singularize($this->getProperty()));
	 * </code>
	 * 
	 * @return string
	 */
	public function getRelatedClass()
	{
		return empty($this->related_class) ? ucfirst(Rdm_Util_Inflector::singularize($this->getProperty())) : $this->related_class;
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
	 * @return Rdm_Descriptor
	 */
	public function getRelatedDescriptor()
	{
		return Rdm_Config::getDescriptor($this->getRelatedClass());
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
	 * This uses constants from the Rdm_Descriptor class.
	 * 
	 * @param  int
	 * @return self
	 */
	public function setType($type)
	{
		// TODO: Validate the action?
		$this->type = $type;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if this relation should be loaded eagerly
	 * 
	 * @return bool
	 */
	public function getEagerLoading()
	{
		return $this->eager_loading;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets if this relation should be loaded eagerly.
	 * 
	 * @param  bool
	 * @return self
	 */
	public function setEagerLoading($value)
	{
		$this->eager_loading = $value;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the action to perform ON DELETE.
	 * 
	 * @return int
	 */
	public function getOnDeleteAction()
	{
		return $this->on_delete ? $this->on_delete : Rdm_Descriptor::NOTHING;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets which action to perform ON DELETE.
	 * 
	 * This method uses constants defined in Rdm_Descriptor.
	 *
	 * Default: Rdm_Descriptor::SET_NULL
	 * 
	 * @param  int
	 * @return self
	 */
	public function setOnDeleteAction($action)
	{
		// TODO: Validate the action?
		$this->on_delete = $action;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the RelationFilter class for this relationship.
	 * 
	 * @return string
	 */
	public function getRelationFilterClass()
	{
		return $this->desc_parent->getClass().ucfirst($this->getName()).'RelationFilter';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the object which is handling the grunt work.
	 * 
	 * TODO: Does it need to be public?
	 * 
	 * @return Rdm_Descriptor_RelationInterface
	 */
	public function getRelationHandler()
	{
		$klass = $this->getRelationHandlerClass();
		
		if(empty($this->handler) OR ! $this->handler instanceof $klass)
		{
			$this->handler = new $klass($this);
		}
		
		return $this->handler;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a list of foreign key associations.
	 * 
	 * Receives the object which contains the primary keys of the relation,
	 * it should be used to create a list of names for the foreign keys to use
	 * in the related object.
	 * 
	 * Return example:
	 * <code>
	 * array(
	 *     'id' => 'user_id'
	 *     );
	 * </code>
	 * 
	 * NOTE:
	 * Everything is returned in property names!
	 * 
	 * @param  Rdm_Descriptor
	 * @return array
	 */
	public function guessForeignKeyMappings(Rdm_Descriptor $descriptor)
	{
		$ret = array();
		
		foreach($descriptor->getPrimaryKeys() as $pk)
		{
			$ret[$pk->getProperty()] = $descriptor->getSingular().'_'.$pk->getProperty();
		}
		
		return $ret;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of Rdm_Descriptor_Column objects from the passed descriptor.
	 * 
	 * The keys are the property names.
	 * 
	 * @param  array
	 * @param  Rdm_Descriptor
	 * @return array
	 */
	public function getKeyObjects(array $key_list, Rdm_Descriptor $descriptor)
	{
		$properties = array_merge($descriptor->getColumns(), $descriptor->getPrimaryKeys());
		$keys = array();
		
		foreach($key_list as $key)
		{
			if( ! isset($properties[$key]))
			{
				// TODO: Set some default values, based on the linked keys?
				$c = $descriptor->newColumn($key);
				
				$descriptor->add($c);
				
				$keys[] = $c;
			}
			else
			{
				$keys[] = $properties[$key];
			}
		}
		
		return $keys;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Dispatches unknown methods to the relation handling object.
	 * 
	 * @param  string
	 * @param  array
	 * @return mixed
	 */
	public function __call($method, $params)
	{
		$handler = $this->getRelationHandler();
		
		$ref = new ReflectionObject($handler);
		
		if($ref->hasMethod($method) && $ref->getMethod($method)->isPublic())
		{
			return call_user_func_array(array($handler, $method), $params);
		}
		else
		{
			throw new BadMethodCallException($method);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name of the object which will handle the relation specific things.
	 * 
	 * @throws UnexpectedValueException
	 * @return string
	 */
	protected function getRelationHandlerClass()
	{
		$t = $this->getType();
		
		switch($t)
		{
			case Rdm_Descriptor::HAS_MANY:
				return 'Rdm_Descriptor_Relation_HasMany';
				
			case Rdm_Descriptor::HAS_ONE:
				return 'Rdm_Descriptor_Relation_HasOne';
				
			case Rdm_Descriptor::BELONGS_TO:
				return 'Rdm_Descriptor_Relation_BelongsTo';
			
			case Rdm_Descriptor::MANY_TO_MANY:
				return 'Rdm_Descriptor_Relation_ManyToMany';
				
			default:
				throw new UnexpectedValueException($t);
		}
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
		if(substr($this->getName(), -1) == 's')
		{
			// TODO: Refine the process of determining if a word is plural
			
			// plural
			return Rdm_Descriptor::HAS_MANY;
		}
		else
		{
			$cls = new ReflectionClass($this->getParentDescriptor()->getClass());
			
			try
			{
				// Check if all the primary keys has a corresponding foreign key
				// expects that the default naming of the *property* is relationName_PrimaryKeyName
				foreach($this->guessForeignKeyMappings($this->getRelatedDescriptor()) as $col)
				{
					$prop = $cls->getProperty($col);
				}
			}
			catch(ReflectionException $e)
			{
				// Did not find a property
				return Rdm_Descriptor::HAS_ONE;
			}
			
			// Found property(ies)
			return Rdm_Descriptor::BELONGS_TO;
		}
	}
}

/* End of file Relation.php */
/* Location: ./lib/Rdm/Descriptor */