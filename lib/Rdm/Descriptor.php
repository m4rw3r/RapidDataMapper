<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class describing the mapping between a class and a table.
 */
class Rdm_Descriptor
{
	/**
	 * Constant telling the Rdm_Descriptor_Relation that it should use a Belongs To relationship.
	 */
	const BELONGS_TO = 'relation.BELONGS_TO';
	/**
	 * Constant telling the Rdm_Descriptor_Relation that it should use a Has One relationship.
	 */
	const HAS_ONE = 'relation.HAS_ONE';
	/**
	 * Constant telling the Rdm_Descriptor_Relation that it should use a Has Many relationship.
	 */
	const HAS_MANY = 'relation.HAS_MANY';
	/**
	 * Constant telling the Rdm_Descriptor_Relation that it should use a Many To Many (HABTM) relationship.
	 */
	const MANY_TO_MANY = 'relation.MANY_TO_MANY';
	/**
	 * Constant telling Rdm_Descriptor_PrimaryKey that it automatically increments its value compared to the last on insert.
	 */
	const AUTO_INCREMENT = 'key_type.AUTO_INCREMENT';
	/**
	 * Constant telling Rdm_Descriptor_PrimaryKey that it is assigned manually.
	 */
	const MANUAL = 'key_type.MANUAL';
	/**
	 * Constant telling Rdm_Descriptor_PrimaryKey that it is generated by a method call.
	 */
	const CALL = 'key_type.CALL';
	/**
	 * Constant telling Rdm_Descriptor_Relation that it should cascade ON DELETE.
	 */
	const CASCADE = 'on_delete.CASCADE';
	/**
	 * Constant telling Rdm_Descriptor_Relation that it should restrict ON DELETE.
	 */
	const RESTRICT = 'on_delete.RESTRICT';
	/**
	 * Constant telling Rdm_Descriptor_Relation that it should set foreign keys to NULL ON DELETE.
	 */
	const SET_NULL = 'on_delete.SET_NULL';
	/**
	 * Constant telling Rdm_Descriptor_Relation that it should do nothing with related rows ON DELETE, default.
	 */
	const NOTHING = 'on_delete.NOTHING';
	/**
	 * Constant telling Rdm_Descriptor_Column that the column is a plain column to fetch after insert
	 * and/or on delete has been performed.
	 */
	const PLAIN_COLUMN = 31;
	/**
	 * Constant telling Rdm_Descriptor_Column that the column has special logic which is to be performed
	 * after an insert and/or delete has been performed.
	 */
	const SPECIAL_COLUMN = 32;
	/**
	 * Constant telling Rdm_UnitOfWork to search all fetched objects for changes.
	 */
	const IMPLICIT = 'change_tracking.IMPLICIT';
	/**
	 * Constant telling Rdm_UnitOfWork to only search a specific set of objects,
	 * supplied by a user, for changes.
	 */
	const EXPLICIT = 'change_tracking.EXPLICIT';
	
	/**
	 * RDM type constant for Boolean data type.
	 */
	const BOOL      = 'type.BOOL';
	/**
	 * RDM type constant for Character data type.
	 */
	const CHAR      = 'type.CHAR';
	/**
	 * RDM type constant for Date data type.
	 */
	const DATE      = 'type.DATE';
	/**
	 * RDM type constant for DateTime data type.
	 */
	const DATETIME  = 'type.DATETIME';
	/**
	 * RDM type constant for Float data type.
	 */
	const FLOAT     = 'type.FLOAT';
	/**
	 * Rdm type constant for generic data.
	 */
	const GENERIC   = 'type.GENERIC';
	/**
	 * RDM type constant for Integer data type.
	 */
	const INT       = 'type.INT';
	/**
	 * RDM type constant for Text data type.
	 */
	const TEXT      = 'type.TEXT';
	/**
	 * RDM type constant for Timestamp data type.
	 */
	const TIMESTAMP = 'type.TIMESTAMP';
	/**
	 * RDM type constant for Serialize data type.
	 */
	const SERIALIZE = 'type.SERIALIZE';
	
	/**
	 * A list of the default RDM type mappings.
	 * 
	 * @var array(string => string)
	 */
	static public $type_mappings = array(
		self::BOOL      => 'Rdm_Descriptor_Type_Bool',
		self::CHAR      => 'Rdm_Descriptor_Type_Char',
		self::DATE      => 'Rdm_Descriptor_Type_Date',
		self::DATETIME  => 'Rdm_Descriptor_Type_DateTime',
		self::FLOAT     => 'Rdm_Descriptor_Type_Float',
		self::GENERIC   => 'Rdm_Descriptor_Type_Generic',
		self::INT       => 'Rdm_Descriptor_Type_Int',
		self::TEXT      => 'Rdm_Descriptor_Type_Text',
		self::TIMESTAMP => 'Rdm_Descriptor_Type_Timestamp',
		self::SERIALIZE => 'Rdm_Descriptor_Type_Serialize'
		);
	
	/**
	 * A list of local type mappings overriding all the other type mappings,
	 * only for this descriptor.
	 * 
	 * @var array(Rdm_type => class)
	 */
	public $local_type_mappings = array();
	
	/**
	 * Internal: The configuration instance used to create the
	 * configuration specific code.
	 * 
	 * @var Rdm_Config
	 */
	protected $config = null;
	
	/**
	 * The class this object describes.
	 * 
	 * @var string
	 */
	protected $class;
	
	/**
	 * The namespace of the described entity.
	 * 
	 * @var string
	 */
	protected $namespace;
	
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
	 * The change-tracking policy, currently IMPLICIT (default) or EXPLICIT.
	 * 
	 * @var int
	 */
	protected $change_tracking_policy = self::IMPLICIT;
	
	/**
	 * A list of the registered hooks.
	 * 
	 * @var array
	 */
	protected $hooks = array();
	
	/**
	 * A list containing all the loaded plugins. 
	 * 
	 * @var array
	 */
	protected $plugins = array();
	
	/**
	 * A list with hooked code which the plugins have added.
	 * 
	 * @var array
	 */
	protected $plugin_hooks = array();
	
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
	 * Internal: Sets the configuration object used by this descriptor.
	 * 
	 * @param  Rdm_Config
	 * @return void
	 */
	public function setConfig(Rdm_Config $config)
	{
		$this->config = $config;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the configuration instance used by this descriptor.
	 * 
	 * @return Rdm_Config
	 */
	public function getConfig()
	{
		return $this->config;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Internal: Returns the adapter used to create the database specific
	 * mapper code.
	 * 
	 * @return Rdm_Adapter
	 */
	public function getAdapter()
	{
		return $this->config->getAdapter();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the class name which this descriptor describes.
	 * 
	 * @throws Rdm_Descriptor_MissingValueException
	 * @param  boolean  If to prepend the namespace name, without prepended backslash
	 * @param  boolean  If to prepend a backslash to the namespace to make it
	 *                  fully qualified
	 * @return string
	 */
	public function getClass($include_namespace = false, $qualified = false)
	{
		if(empty($this->class))
		{
			$c = get_class($this);
			
			if($c != 'Rdm_Descriptor' && preg_match('/^([A-Za-z0-9\\\\]+)Descriptor$/i', $c, $r))
			{
				$this->setClass($r[1]);
			}
			else
			{
				throw new Rdm_Descriptor_MissingValueException('class name', '');
			}
		}
		
		if($include_namespace)
		{
			return $this->getNamespace(true, $qualified).$this->class;
		}
		else
		{
			return $this->class;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the namespace part of the class name, no slashes at the ends unless
	 * parameters specify so, returns an empty string on PHP < 5.3.
	 * 
	 * @param  boolean  If to append a backslash if there is a namespace
	 * @param  boolean  If to prepend a backslash, even if there isn't a namespace
	 *                  Ie. Create a fully qualified class name
	 * @return string
	 */
	public function getNamespace($append_backslash = false, $qualified = false)
	{
		if(version_compare(PHP_VERSION, '5.3.0', '<'))
		{
			return '';
		}
		elseif(empty($this->namespace))
		{
			return $qualified ? '\\' : '';
		}
		else
		{
			return ($qualified ? '\\' : '').$this->namespace.($append_backslash ? '\\' : '');
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if the described entity is located in a namespace.
	 * 
	 * @return boolean
	 */
	public function isNamespaced()
	{
		return ! empty($this->namespace);
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
		// split into namespace and class
		$ns = explode('\\', $class);
		$this->class = array_pop($ns);
		$this->namespace = trim(implode('\\', $ns), '\\');
		
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
	 * return Rdm_Util_Inflector::pluralize($this->getSingular());
	 * </code>
	 * 
	 * @return string
	 */
	public function getTable()
	{
		return empty($this->table) ? $this->table = Rdm_Util_Inflector::pluralize($this->getSingular()) : $this->table;
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
	 * Sets the policy for detecting changed objects.
	 * 
	 * Options:
	 *  * IMPLICIT: All fetched objects are automatically searched for changes
	 *  * EXPLICIT: Only objects specified by user are searched for changes
	 * 
	 * @param  int  Constant from Rdm_Descriptor
	 * @return self
	 */
	public function setChangeTrackingPolicy($value)
	{
		// TODO: Check the value of $value
		$this->change_tracking_policy = $value;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the policy for detecting changed objects.
	 * 
	 * @param  bool  If to return it as the name of a Rdm_UnitOfWork constant
	 * @return int|string
	 */
	public function getChangeTrackingPolicy($as_string = false)
	{
		if($as_string)
		{
			$cls = $this->isNamespaced() ? '\\' : '';
			$cls .= 'Rdm_UnitOfWork::';
			
			switch($this->change_tracking_policy)
			{
				case self::IMPLICIT:
					return $cls.'IMPLICIT';
				case self::EXPLICIT:
					return $cls.'EXPLICIT';
			}
		}
		else
		{
			return $this->change_tracking_policy;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the <Class>Collection class which is created by the
	 * Rdm_Builder_Collection class.
	 * 
	 * @param  boolean  If to prepend the namespace name
	 * @param  boolean  If to prepend a backslash to the namespace to make it
	 *                  fully qualified
	 * @return string
	 */
	public function getCollectionClassName($include_namespace = false, $qualified = false)
	{
		$c = $this->getClass().'Collection';
		
		if($include_namespace)
		{
			return $this->getNamespace(true, $qualified).$c;
		}
		else
		{
			return $c;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the <Class>BaseCollection class which is created by the
	 * Rdm_Builder_Collection class.
	 * 
	 * @param  boolean  If to prepend the namespace name
	 * @param  boolean  If to prepend a backslash to the namespace to make it
	 *                  fully qualified
	 * @return string
	 */
	public function getBaseCollectionClassName($include_namespace = false, $qualified = false)
	{
		$c = $this->getClass().'CollectionBase';
		
		if($include_namespace)
		{
			return $this->getNamespace(true, $qualified).$c;
		}
		else
		{
			return $c;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the <Class>CollectionFilter class which is created
	 * by the Rdm_Builder_CollectionFilter class.
	 * 
	 * @param  boolean  If to prepend the namespace
	 * @param  boolean  If to prepend a backslash to the namespace to make it
	 *                  fully qualified
	 * @return string
	 */
	public function getCollectionFilterClassName($include_namespace = false, $qualified = false)
	{
		$c = $this->getClass().'CollectionFilter';
		
		if($include_namespace)
		{
			return $this->getNamespace(true, $qualified).$c;
		}
		else
		{
			return $c;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the <Class>UnitOfWork class which is generated by
	 * the Rdm_Builder_UnitOfWork class.
	 * 
	 * @param  boolean  If to include the namespace
	 * @param  boolean  If to prepend a backslash to the namespace to make it
	 *                  fully qualified
	 * @return string
	 */
	public function getUnitOfWorkClassName($include_namespace = false, $qualified = false)
	{
		$c = $this->getClass().'UnitOfWork';
		
		if($include_namespace)
		{
			return $this->getNamespace(true, $qualified).$c;
		}
		else
		{
			return $c;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets a callable to call on a specific hook in the generated code.
	 * 
	 * @param  string
	 * @param  string|array		If the callable is missing, the hook name will be used
	 * @return self
	 */
	public function setHook($name, $callable = false)
	{
		if($callable === false)
		{
			$callable = $name;
		}
		
		$this->hooks[$name] = $callable;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a descriptor object to this descriptor.
	 * 
	 * @throws InvalidArgumentException
	 * @param  Rdm_Descriptor_Column|Rdm_Descriptor_PrimaryKey|Rdm_Descriptor_Relation|array
	 * @return self
	 */
	public function add($object)
	{
		if(is_array($object))
		{
			foreach($object as $obj)
			{
				$this->add($obj);
			}
			
			return $this;
		}
		
		// Is it of the correct type?
		if( ! ($object instanceof Rdm_Descriptor_Relation OR
		       $object instanceof Rdm_Descriptor_PrimaryKey OR
		       $object instanceof Rdm_Descriptor_Column))
		{
			// Nope
			throw new InvalidArgumentException(is_object($object) ? get_class($object) : gettype($object));
		}
		
		$object->setParentDescriptor($this);
		
		if($object instanceof Rdm_Descriptor_Relation)
		{
			// Add the unique relation id to the created relation
			$object->setIntegerIdentifier(self::calcRelationId($this, $object));
			
			$this->relations[$object->getProperty()] = $object;
		}
		elseif($object instanceof Rdm_Descriptor_PrimaryKey)
		{
			$this->primary_keys[$object->getProperty()] = $object;
		}
		else
		{
			$this->properties[$object->getProperty()] = $object;
		}
		
		return $this;
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
	 * @return array
	 */
	public function getRelations()
	{
		return $this->relations;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new column descriptor to be used by this descriptor.
	 * 
	 * @param  string  The column name
	 * @param  string|Rdm_Descriptor_TypeInterface The data type
	 * @param  int|false  The data type length
	 * @param  string  The property name of this column, defaults to $name
	 * @return Rdm_Descriptor_Column
	 */
	public function newColumn($name,
	                          $type = Rdm_Descriptor::GENERIC,
	                          $type_length = false,
	                          $property = false)
	{
		return new Rdm_Descriptor_Column($name, $type, $type_length, $property);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new primary key descriptor to be used by this descriptor.
	 * 
	 * @param  string  The column name
	 * @param  string|Rdm_Descriptor_TypeInterface The data type, object or
	 *                                             Rdm_Descriptor constant
	 * @param  int|false  The data type length
	 * @param  string  The property name of this column, defaults to $name
	 * @param  int     The primary key type, constant from Rdm_Descriptor
	 * @return Rdm_Descriptor_PrimaryKey
	 */
	public function newPrimaryKey($name,
	                              $type = Rdm_Descriptor::GENERIC,
	                              $type_length = false,
	                              $property = false,
	                              $primary_key_type = Rdm_Descriptor::AUTO_INCREMENT)
	{
		return new Rdm_Descriptor_PrimaryKey($name, $type, $type_length, $property, $primary_key_type);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new relation descriptor to be used by this descriptor.
	 * 
	 * @param  string  The relationship name
	 * @param  string  The relationship type constant from Rdm_Descriptor class
	 * @param  string  The name of the related class
	 * @param  string  The property name where the entities should be stored on
	 *                 the parent entities
	 * @param  string  The ON DELETE action constant from the Rdm_Descriptor class
	 * @param  array   A list of parameters to be sent to the
	 *                 Rdm_Descriptor_RelationInterface::setForeignKeys
	 * @return Rdm_Descriptor_Relation
	 */
	public function newRelation($name,
	                            $type = null,
	                            $related_class = null,
	                            $property = null,
	                            $on_delete = Rdm_Descriptor::NOTHING,
	                            $foreign_keys = null)
	{
		return new Rdm_Descriptor_Relation($name, $type, $related_class, $property, $on_delete, $foreign_keys);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a data type object which will generate the correct code for
	 * SQL queries and PHP.
	 * 
	 * @return string  The data type name
	 */
	public function dataType($type_name)
	{
		$types = array_merge(self::$type_mappings, $this->getAdapter()->type_mappings, $this->local_type_mappings);
		
		if(empty($types[$type_name]))
		{
			// TODO: Proper exception class
			throw new Exception('The data type object for the type "'.$type.'" is cannot be found.');
		}
		else
		{
			$c = $types[$type_name];
			
			return new $c();
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Applies a plugin to this object.
	 * 
	 * If the same plugin type is registered again, the earlier will be removed.
	 * 
	 * @param  Rdm_Plugin
	 * @return self
	 */
	public function applyPlugin(Rdm_Plugin $plugin_instance)
	{
		$class = get_class($plugin_instance);
		
		// remove an existing plugin with the same class
		foreach($this->plugins as $k => $p)
		{
			if($p === $plugin_instance)
			{
				// plugin already loaded
				return $this;
			}
			
			if(get_class($p) == $class)
			{
				$this->plugins[$k]->remove();
				unset($k);
			}
		}
		
		$plugin_instance->setDescriptor($this);
		$plugin_instance->init();
		
		$this->plugins[] = $plugin_instance;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of the plugin instances currently associated with this descriptor.
	 * 
	 * @return array
	 */
	public function getPlugins()
	{
		return $this->plugins;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Replaces an object with its decorator (ie. replacing the object with the same instance
	 * as the object in the decorator).
	 * 
	 * It is possible to decorate an existing decorator which is decorating a decorator
	 * which is decorating a ... a "normmal" object (to infinity, almost).
	 * <code>
	 * decorator -> decorator -> ... -> decorator -> object
	 * </code>
	 * 
	 * The thing you have to do is to always call addDecorator($decorator) after
	 * you have decorated an object which is going to be wrapped in another decorator:
	 * <code>
	 * $cols = $descriptor->getColumns();
	 * 
	 * $column = $cols['foobar']; // get the column associated with the foobar property
	 * 
	 * $d = Some_Decorator();
	 * $d->setDecoratedObject($column);
	 * 
	 * $descriptor->addDecorator($d);
	 * 
	 * $d2 = Some_other_decorator();
	 * $d2->setDecoratedObject($d);
	 * 
	 * $descriptor->addDecorator($d2);   // d2 replaces $d in the $decorator,
	 * // but $d2 decorates $d which in turn decorates $column
	 * </code>
	 * 
	 * @param  Rdm_Util_Decorator		A decorator decorating the object to replace
	 * @return bool
	 */
	public function addDecorator(Rdm_Util_Decorator $decorator)
	{
		// TODO: Support adding a chain of decorators without having to call addDecorator() for each decorator in the chain?
		$o = $decorator->getDecoratedObject();
		
		foreach(array('properties', 'relations', 'primary_keys') as $property)
		{
			foreach($this->$property as $k => $p)
			{
				if($p === $o)
				{
					$this->{$property}[$k] = $decorator;
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Removes the decorator passed as the second parameter, replaces it with
	 * the original (ie. decorated) object.
	 * 
	 * Can also remove decorators which are part of a decorator chain without
	 * disturbing the other decorators:
	 * <code>
	 * // decorator chain:
	 * // $a -> $b -> $c -> $column
	 * 
	 * $descriptor->removeDecorator($b);
	 * // result:
	 * // $a -> $c -> $column
	 * </code>
	 * 
	 * @param  Rdm_Util_Decorator
	 * @return bool
	 */
	public function removeDecorator(Rdm_Util_Decorator $decorator)
	{
		$o = $decorator->getDecoratedObject();
		
		foreach(array('properties', 'relations', 'primary_keys') as $property)
		{
			foreach($this->$property as $k => $p)
			{
				// store the parent decorator here
				$old = null;
				
				// iterate the decorator chain
				while($p instanceof Rdm_Util_Decorator && $p !== $decorator)
				{
					$old = $p;
					$p = $p->getDecoratedObject();
				}
				
				if($p === $decorator)
				{
					if(is_null($old))
					{
						$this->{$property}[$k] = $o;
					}
					else
					{
						$old->setDecoratedObject($o);
					}
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a new instance of a mapper builder.
	 * 
	 * @throws Rdm_Descriptor_MissingValue
	 * @return Rdm_Builder_Main
	 */
	public final function getBuilder()
	{
		// By now everything should be set
		
		// ensure that we have at least one primary key
		$pks = $this->getPrimaryKeys();
		if(empty($pks))
		{
			throw new Rdm_Descriptor_MissingValueException('primary key', $this->getClass());
		}
		
		$b = $this->createBuilder();
		
		// let all the plugins have their way with the builder
		foreach($this->plugins as $p)
		{
			$p->editBuilder($b);
		}
		
		return $b;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code invoking a specified hook.
	 * 
	 * Examples:
	 * <code>
	 * getHookCode('foo', '$obj', '$lol');
	 * // $obj->foo_method($lol);
	 * 
	 * getHookCode('bar', false, '$obj');
	 * // Foo::Bar_method($obj);
	 * </code>
	 * 
	 * foo_method() and bar_method() are specified by the hook.
	 * 
	 * Also performs validation before returning the generated code,
	 * see it as a compile-time error detection.
	 * 
	 * @param  string			The name of the hook
	 * @param  string|false		The object variable to invoke the hooked method
	 * 							(ie. the hooked method is situated ON the object 
	 * 							contained in the $object_var variable)
	 * 							If it is false, a static call is expected'
	 * @param  string			A list of the parameters, just concatenate it between the parenthesis
	 * @return string
	 */
	public function getHookCode($name, $object_var = false, $param_list = '')
	{
		if(isset($this->hooks[$name]))
		{
			if(is_array($this->hooks[$name]) && ! empty($this->hooks[$name]))
			{
				if(count($this->hooks[$name]) > 2)
				{
					throw new Rdm_Descriptor_Exception($this->getClass(), 'Hook: "'.$name.'", the array has too many values.');
				}
				
				if( ! (isset($this->hooks[$name][0]) && is_string($this->hooks[$name][0])))
				{
					throw new Rdm_Descriptor_Exception($this->getClass(), 'Hook: "'.$name.'", the first value in the array is not a string.');
				}
				
				if(isset($this->hooks[$name][1]) && ! is_string($this->hooks[$name][1]))
				{
					throw new Rdm_Descriptor_Exception($this->getClass(), 'Hook: "'.$name.'", the first value in the array is not a string.');
				}
			}
			elseif( ! is_string($this->hooks[$name]))
			{
				throw new Rdm_Descriptor_Exception($this->getClass(), 'Hook: "'.$name.'", the callable must be either a string or an array containing one or two strings.');
			}
			
			$hook = array();
			foreach((Array) $this->hooks[$name] as $c)
			{
				// remove potential troublemakers
				$hook[] = trim((String) $c, " ();=\t\n\r");
			}
			
			if(count($hook) == 1)
			{
				$hook = array_shift($hook);
			}
			
			// TODO: Add support for the following syntax:
			// setHook('event', 'Someclass::some_method'); and then it will be called like this on instance
			// hooks: Someclass::some_method($entity_instance)
			
			if($object_var === false)
			{
				// TODO: Move the check for is_callable avbove the check for __callStatic, what if an object has __callStatic but we want to call another object?
				
				// check if the hooks is a static method on the attached object
				// also take the __callStatic into account
				if(is_string($hook) && (is_callable(array($this->getClass(), $hook)) OR
					method_exists($this->getClass(), '__callStatic')))
				{
					return $this->getClass().'::'.$hook.'('.$param_list.');';
				}
				// check if it is a static method or a function
				elseif(is_callable($hook))
				{
					return implode('::', (Array)$hook).'('.$param_list.');';
				}
				else
				{
					throw new Rdm_Descriptor_Exception($this->getClass(), 'Callable supplied for hook "'.$name.'", callable "'.$hook.'".');
				}
			}
			else
			{
				// we need a string
				if(is_array($hook))
				{
					throw new Rdm_Descriptor_Exception($this->getClass(), 'The hook "'.$name.'" requires a method placed on the described class, not a static method placed on some other class.');
				}
				
				// check if it is a method on the object
				try
				{
					// fetch a reflection
					$ref = new ReflectionClass($this->getClass());
					
					if($ref->hasMethod($hook))
					{
						$m = $ref->getMethod($hook);
						
						// we need to be able to invoke it outside the class
						if( ! $m->isPublic())
						{
							throw new Rdm_Descriptor_Exception($this->getClass(), 'The "'.$this->getClass().'::'.$hook.'" method is not public, it cannot be used as a hook.');
						}
						elseif($m->isStatic())
						{
							throw new Rdm_Descriptor_Exception($this->getClass(), 'The "'.$this->getClass().'::'.$hook.'" method is static but a non-static method is required.');
						}
						else
						{
							return $object_var.'->'.$hook.'('.$param_list.');';
						}
					}
					// __call works too
					elseif($ref->hasMethod('__call'))
					{
						return $object_var.'->'.$hook.'('.$param_list.');';
					}
					else
					{
						throw new Rdm_Descriptor_Exception($this->getClass(), 'A method with the name "'.$hook.'" is required by a hook to be placed in the class "'.$this->getClass().'".');
					}
				}
				catch(ReflectionException $e)
				{
					// TODO: Proper error handling code, convert to a Rdm_Exception
					throw $e;
				}
			}
		}
		else
		{
			// No hook for this point
			return '';
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a piece of a statement which creates an unique identifier for a row.
	 * 
	 * Example of generated code:
	 * <code>
	 * // params: $data_var = '$row', $data_prefix_var = '$alias' (two pks)
	 * $row->{$alias.'__id'}.'*'.$row->{$alias.'__lang'}
	 * </code>
	 * 
	 * @param  string	The name of the variable holding an instance of StdClass, containing the row data.
	 * @param  string	The name of the variable holding a prefix for the column name
	 * @return string
	 */
	public function getUidCode($data_var, $data_prefix_var)
	{
		$arr = array();
		
		foreach($this->getPrimaryKeys() as $key)
		{
			$arr[] = $key->getFromDataCode($data_var, $data_prefix_var);
		}
		
		return implode('.\'*\'.', $arr);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a piece of a statement which creates a check if an object DOESN'T exists in a result row.
	 * 
	 * Example of generated code:
	 * <code>
	 * // params: $data_var = '$row', $data_prefix_var = '$alias' (two pks)
	 * is_null($row->{$alias.'__id'}) OR is_null($row->{$alias.'__lang'})
	 * </code>
	 * 
	 * @param  string	The name of the variable holding an instance of StdClass, containing the row data.
	 * @param  string	The name of the variable holding a prefix for the column name
	 * @return string
	 */
	public function getNotContainsObjectCode($data_var, $data_prefix_var)
	{
		$arr = array();
		
		foreach($this->getPrimaryKeys() as $key)
		{
			$arr[] = 'is_null('.$key->getFromDataCode($data_var, $data_prefix_var).')';
		}
		
		return implode(' OR ', $arr);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns code/data from a plugin hook.
	 * 
	 * @param  string
	 * @param  array
	 * @param  string
	 * @return string
	 */
	public function getPluginHook($hook_name, $parameters = array(), $default = '')
	{
		$hook_name = strtolower($hook_name);
		
		if(empty($this->plugin_hooks[$hook_name]))
		{
			return $default;
		}
		
		$str = '';
		foreach($this->plugin_hooks[$hook_name] as $callable)
		{
			$str .= call_user_func_array($callable, $parameters);
		}
		
		return $str;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Registers a callable with the plugin hooks system.
	 * 
	 * @param  string
	 * @param  callable
	 * @return void
	 */
	public function setPluginHook($hook_name, $callable)
	{
		$this->plugin_hooks[strtolower($hook_name)][] = $callable;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a code builder for this descriptor.
	 * 
	 * @return Rdm_Builder_Main
	 */
	protected function createBuilder()
	{
		return new Rdm_Builder_Main($this);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Calculates an id for the relationship, uses the class name + the relationship
	 * name to calculate a unique id.
	 * 
	 * @param  Rdm_Descriptor
	 * @param  Rdm_Descriptor_Relation
	 * @return int
	 */
	public static function calcRelationId(Rdm_Descriptor $desc, Rdm_Descriptor_Relation $rel)
	{
		$str = $desc->getClass().'::'.$rel->getName();
		$ret = 1;
		
		for($i = 0, $c = strlen($str); $i < $c; $i++)
		{
			$ret += ord($str[$i]);
		}
		
		return $ret;
	}
}



/* End of file Descriptor.php */
/* Location: ./lib/Rdm */