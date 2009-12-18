<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
abstract class Db_Mapper
{
	/**
	 * The class this mapper is mapping.
	 * 
	 * @var string
	 */
	public $class = '';
	
	/**
	 * A list of the relations of this mapper, property => class.
	 * 
	 * @var array
	 */
	public $relations = array();
	
	/**
	 * A list of the properties and their corresponding column (property => column).
	 * 
	 * @var array
	 */
	public $properties = array();
	
	/**
	 * A list of the primary keys of the mapped object, (property => column).
	 * 
	 * @var array
	 */
	public $primary_keys = array();
	
	/**
	 * The database connection this mapper uses.
	 * 
	 * @var Db_Connection
	 */
	protected $db;
	
	public function __construct(Db_Connection $connection)
	{
		$this->db = $connection;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Do not allow cloning.
	 * 
	 * Only one mapper per class should exist.
	 * 
	 * @throws Db_Exception
	 */
	public final function __clone()
	{
		throw new Db_Exception('Cloning of Db_Connection instances are not allowed.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Do not allow serialization.
	 * 
	 * @throws Db_Exception
	 */
	public final function __sleep()
	{
		throw new Db_Exception('Serialization of Db_Connection objects are not allowed.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Do not allow unserialization.
	 * 
	 * @throws Db_Exception
	 */
	public final function __wakeup()
	{
		throw new Db_Exception('Unserialization of Db_Connection objects are not allowed.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the database connection.
	 * 
	 * @return Db_Connection
	 */
	public function getConnection()
	{
		return $this->db;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Creates a Db_MapperQuery object to fetch the mapped objects from the database.
	 * 
	 * @return Db_MapperQuery
	 */
	abstract protected function createMapperQuery();
	
	// ------------------------------------------------------------------------
	
	/**
	 * Creates a basic SELCT query for class this mapper maps to, can also find by PK or other conditions.
	 * 
	 * @param  string|array
	 * @param  string|array
	 * @return Db_Query_MapperSelect
	 */
	public function find($conditions = false, $values = false)
	{
		$query = $this->createMapperQuery();
		
		// do we have a filter?
		if($conditions !== false)
		{
			// only one?
			if($values === false)
			{
				// is it array(col => value)?
				if(is_array($conditions) && ! is_numeric(key($conditions)))
				{
					$query->where($conditions);
				}
				else
				{	
					// no just primary key
					if(count($conditions) != count($this->primary_keys))
					{
						throw new Db_Exception('Wrong number of primary keys specified when trying to find a '.$this->class.' object.');
					}
					
					// TODO: prefix primary key(s) with singular?
					$query->where(array_combine($this->primary_keys, (Array) $conditions));
					
					// limit to one
					return $query->getOne();
				}
			}
			else
			{
				$query->where($conditions, $values);
			}
			
			// return the result
			return $query->get();
		}
		else
		{
			return $query;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Joins the related objects attached to the relation with the name $relation_name.
	 * 
	 * @param  Db_Query_MapperSelect
	 * @param  string
	 * @param  string
	 * @return void
	 */
	abstract public function joinRelated($query, $relation_name, $alias_of_linked);
	
	// ------------------------------------------------------------------------

	/**
	 * Adds conditions to a query to fetch related objects which filters so it only
	 * returns the objects related to the $object parameter.
	 * 
	 * Example:
	 * 
	 * <code>
	 * // User relates to posts:
	 * $q = $post_model->populateFindQuery();
	 * $user_model->applyRelatedConditions($q, 'posts', $some_user_object);
	 * </code>
	 * 
	 * @todo   Is the alias also needed?
	 * 
	 * @param  Db_Query_MapperSelect
	 * @param  string
	 * @param  object
	 * @return void
	 */
	abstract public function applyRelatedConditions($query, $relation_name, $object);
	
	// ------------------------------------------------------------------------
	
	/**
	 * Loops the database result and sends each row to be objectified.
	 * 
	 * @param  Db_Result
	 * @param  array		A list of the aliases and their corresponding class
	 * @param  array		An associative array arranged as a tree with the aliases telling where a certain aliased object fits
	 * @param  string		The name of the alias to start building the path with
	 * @return array
	 */
	public function extract(Db_Result $result, array $mapped_objects, array $alias_paths, $main_alias)
	{
		$res = array();
		
		// remove the link for the object mapped to this record, it is there to prevent double loading
		unset($mapped_objects[$main_alias]);
		
		// get all mappers here, to avoid complexity problems
		$mappers = array();
		foreach($mapped_objects as $alias => $class)
		{
			$mappers[$alias] = Db::getMapper($class);
		}
		
		while($row = $result->next())
		{
			$this->objectify($res, $row, $main_alias, $mappers, $alias_paths);
		}
		
		return $res;
	}
	
	/**
	 * Converts a row to an object.
	 * 
	 * @param array		A reference to the associative array containing the related records of this type
	 * @param stdClass	The row data from the database
	 * @param string	The alias path (eg. "user" for the user.posts, and "user-posts" for the user.posts.user)
	 * @param array		An array of loaded mappers, key is the alias which is using the mapper
	 * @param array		An associative array tree containing names of loaded relations
	 */
	abstract public function objectify(&$res, $row, $alias, &$mappers, $alias_paths);
	
	/**
	 * Saves the object and the relations it has.
	 * 
	 * @param  object
	 * @return bool
	 */
	abstract public function save($object);
	
	/**
	 * Deletes the object, and calls the cascades is applicable.
	 * 
	 * @param  object
	 * @return bool
	 */
	abstract public function delete($object);
}


/* End of file Mapper.php */
/* Location: ./lib/Db */