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
	public $class = '';
	
	public $relations = array();
	
	public $properties = array();
	
	public $primary_keys = array();
	
	/**
	 * The database connection this mapper uses.
	 * 
	 * @var Db_Connection
	 */
	protected $db;
	
	function __construct(Db_Connection $connection)
	{
		$this->db = $connection;
	}
	
	/**
	 * Creates a Db_Query_MapperSelect object to fetch the mapped objects from the database.
	 * 
	 * @return Db_Query_MapperSelect
	 */
	abstract protected function populateFindQuery();
	
	/**
	 * Creates a basic SELCT query for class this mapper maps to, can also find by PK or other conditions.
	 * 
	 * @param  string|array
	 * @param  string|array
	 * @return Db_Query_MapperSelect
	 */
	public function find($conditions = false, $values = false)
	{
		$query = $this->populateFindQuery();
		
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
						throw new Db_Exception_Mapper_WrongNumberOfPks('Wrong number of primary keys specified when trying to find a '.$this->class.' object.');
					}
					
					$query->where(array_combine($this->primary_keys, (Array) $conditions));
					
					// limit to one
					return $query->get_one();
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
}


/* End of file Mapper.php */
/* Location: ./lib/Db */