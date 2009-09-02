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
	 * Constant telling the Db_Descriptor_Relation that it should use a Belongs To relationship.
	 */
	const BELONGS_TO = 1;
	/**
	 * Constant telling the Db_Descriptor_Relation that it should use a Has One relationship.
	 */
	const HAS_ONE = 2;
	/**
	 * Constant telling the Db_Descriptor_Relation that it should use a Has Many relationship.
	 */
	const HAS_MANY = 3;
	/**
	 * Constant telling the Db_Descriptor_Relation that it should use a Many To Many (HABTM) relationship.
	 */
	const MANY_TO_MANY = 4;
	/**
	 * Constant telling Db_Descriptor_PrimaryKey that it automatically increments its value compared to the last on insert.
	 */
	const AUTO_INCREMENT = 5;
	/**
	 * Constant telling Db_Descriptor_PrimaryKey that it is assigned manually.
	 */
	const MANUAL = 6;
	/**
	 * Constant telling Db_Descriptor_PrimaryKey that it is generated by a method call.
	 */
	const CALL = 7;
	/**
	 * Constant telling Db_Descriptor_Relation that it should cascade ON DELETE.
	 */
	const CASCADE = 8;
	/**
	 * Constant telling Db_Descriptor_Relation that it should restrict ON DELETE.
	 */
	const RESTRICT = 8;
	
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
	 * The name of the database connection instance the described class maps to.
	 * 
	 * @var string|false
	 */
	protected $db_conn_name = false;
	
	/**
	 * The database connection instance the described class maps to.
	 * 
	 * @var Db_Connection
	 */
	protected $db_conn;
	
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
	 * Returns the name of the database connection which this object's described class maps to.
	 * 
	 * @return string
	 */
	public function getConnectionName()
	{
		return $this->db_conn_name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the name of the database connection this which object's described class maps to.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setConnectionName($name)
	{
		$this->db_conn_name = $name;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the database connection object which the described object maps to.
	 * 
	 * @return Db_Connection
	 */
	public function getConnection()
	{
		return empty($this->db_conn) ? Db::getConnection($this->getConnectionName()) : $this->db_conn;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the database connection object which the described object maps to.
	 * 
	 * @param  Db_Connection
	 * @return self
	 */
	public function setConnection(Db_Connection $conn)
	{
		$this->db_conn = $conn;
		
		return $this;
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
		$r->setParentDescriptor($this);
		
		return $r;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Applies a plugin to this object.
	 * 
	 * If the same plugin type is registered again, the earlier will be removed.
	 * 
	 * @param  Db_PluginInterface
	 * @return self
	 */
	public function applyPlugin(Db_Plugin $plugin_instance)
	{
		$class = get_class($plugin_instance);
		
		// remove an existing plugin with the same class
		foreach($this->plugins as $k => $p)
		{
			if(get_class($p) == $class)
			{
				$this->plugins[$p]->remove();
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
	 * Replaces an object with its decorator (ie. the object with the same instance
	 * as the object in the decorator).
	 * 
	 * @param  Db_Decorator		A decorator decorating the object to replace
	 * @return bool
	 */
	public function addDecorator(Db_Decorator $decorator)
	{
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
	 * @param  Db_Decorator
	 * @return bool
	 */
	public function removeDecorator($property, Db_Decorator $decorator)
	{
		$o = $decorator->getDecoratedObject();
		
		foreach(array('properties', 'relations', 'primary_keys') as $property)
		{
			foreach($this->$property as $k => $p)
			{
				if($p === $decorator)
				{
					$this->{$property}[$k] = $o;
					
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
	 * @throws Db_Exception_MissingPrimaryKey
	 * @return Db_Mapper_Builder
	 */
	public final function getBuilder()
	{
		// By now everything should be set
		
		// ensure that we have at least one primary key
		$pks = $this->getPrimaryKeys();
		if(empty($pks))
		{
			throw new Db_Exception_MissingPrimaryKey($this->getClass());
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
					throw new Db_Exception_InvalidCallable('Hook: "'.$name.'", the array has too many values.');
				}
				
				if( ! (isset($this->hooks[$name][0]) && is_string($this->hooks[$name][0])))
				{
					throw new Db_Exception_InvalidCallable('Hook: "'.$name.'", the first value in the array is not a string.');
				}
				
				if(isset($this->hooks[$name][1]) && ! is_string($this->hooks[$name][1]))
				{
					throw new Db_Exception_InvalidCallable('Hook: "'.$name.'", the first value in the array is not a string.');
				}
			}
			elseif( ! is_string($this->hooks[$name]))
			{
				throw new Db_Exception_InvalidCallable('Hook: "'.$name.'", the callable must be either a string or an array containing one or two strings.');
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
			
			if($object_var === false)
			{
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
					throw new Db_Exception_InvalidCallable('Callable supplied for hook "'.$name.'", callable "'.$hook.'".');
				}
			}
			else
			{
				// we need a string
				if(is_array($hook))
				{
					throw new Db_Exception_InvalidCallable('The hook "'.$name.'" requires a method placed on the described class, not a static method placed on some other class.');
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
							throw new Db_Exception_InvalidCallable('The "'.$this->getClass().'::'.$hook.'" method is not public, it cannot be used as a hook.');
						}
						elseif($m->isStatic())
						{
							throw new Db_Exception_InvalidCallable('The "'.$this->getClass().'::'.$hook.'" method is static but a non-static method is required.');
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
						throw new Db_Exception_InvalidCallable('A method with the name "'.$hook.'" is required by a hook to be placed in the class "'.$this->getClass().'".');
					}
				}
				catch(ReflectionException $e)
				{
					// TODO: Proper error handling code, convert to a Db_Exception
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
	 * Creates a code builder for this descriptor.
	 * 
	 * @return Db_Mapper_Builder
	 */
	protected function createBuilder()
	{
		return new Db_Mapper_Builder($this);
	}
}



/* End of file Descriptor.php */
/* Location: ./lib/Db */