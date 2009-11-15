<?php
/*
 * Created by Martin Wernståhl on 2009-05-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Specialized select query which can keep track of loaded classes.
 */
class Db_MapperQuery extends Db_Query_Select
{
	/**
	 * The linked mapper.
	 * 
	 * @var Db_Mapper
	 */
	protected $mapper;
	
	/**
	 * A list of the mapped objects, ie. their aliases and their classes.
	 * 
	 * key   = alias
	 * value = class name
	 *
	 * @var array
	 */
	public $mapped_objects = array();
	
	/**
	 * A tree-stack with related objects.
	 * 
	 * The keys are the relation-names (ie. the alias they are mapped to,
	 * but only the last part of it).
	 * 
	 * @var array
	 */
	public $alias_paths = array();
	
	/**
	 * The main object alias
	 * 
	 * The class name of the objects belonging to the linked mapper.
	 * 
	 * @var string
	 */
	public $main_object;
	
	/**
	 * Contains a list of columns which are used in PHP code (ie. strings in WHERE conditions).
	 * 
	 * Key corresponds to the $sql_columns property.
	 * 
	 * @var array
	 */
	public $php_columns = array();
	
	/**
	 * Contains a list of column names which are used in the SQL to send to the database.
	 * 
	 * Key corresponds to the $php_columns property.
	 * 
	 * @var array
	 */
	public $sql_columns = array();
	
	/**
	 * A prefix for the WHERE conditions, prepended once before the first condition.
	 * 
	 * Usually something like this:
	 * <code>
	 * "user_id = 12 AND ("
	 * </code>
	 * 
	 * @var string
	 */
	public $where_prefix = '';
	
	/**
	 * A suffix for the WHERE conditions, appended once after the last condition.
	 * 
	 * Usually an ending parenthesis (")").
	 * 
	 * @var string
	 */
	public $where_suffix = '';
	
	// ------------------------------------------------------------------------

	/**
	 * Populates the object with 
	 * 
	 * @param  Db_Mapper
	 * @param  string
	 */
	public function __construct($mapper)
	{
		$this->_instance = $mapper->getConnection();
		$this->mapper = $mapper;
	}
	
	// ------------------------------------------------------------------------
	
	public function join($table, $condition, $columns = false, $type = 'left')
	{
		return parent::join($table, $this->translateColumns($condition), $columns, $type);
	}
	
	// ------------------------------------------------------------------------
	
	public function where($condition = false, $value = null)
	{
		// multiple conditions
		if(is_array($condition))
		{
			foreach($condition as $k => $v)
			{
				if( ! is_numeric($k))
				{
					$this->createCondition($this->translateColumns($k), $v, $this->where);
				}
				else
				{
					$this->createCondition($this->translateColumns($v), null, $this->where);
				}
			}
			
			return $this;
		}
		
		// nested where part
		if($condition === false OR is_string($condition) && $or_part = preg_match('/^or\s*$/i', $condition))
		{
			if(isset($or_part) && $or_part && ! empty($this->where))
			{
				$this->where[] = 'OR';
			}
			elseif( ! empty($this->where))
			{
				$this->where[] = 'AND';
			}
			
			// new query which is limited to generating the where part
			$this->where[] = $part = new Db_MapperQuery_Where($this->_instance, $this);
			
			return $part;
		}
		else
		{
			$this->createCondition($this->translateColumns($condition), $value, $this->where);
			
			return $this;
		}
	}
	
	// ------------------------------------------------------------------------

	public function bindWhere($condition, $binds)
	{
		return parent::bindWhere($this->translateColumns($condition), $binds);
	}
	
	// ------------------------------------------------------------------------

	public function whereIn($column, $values)
	{
		return parent::whereIn($this->translateColumns($column), $values);
	}
	
	// ------------------------------------------------------------------------

	public function whereNotIn($column, $values)
	{
		return parent::whereNotIn($this->translateColumns($column), $values);
	}
	
	// ------------------------------------------------------------------------
	
	public function like($column, $value, $side = 'both')
	{
		return parent::like($this->translateColumns($column), $value, $side);
	}
	
	// ------------------------------------------------------------------------
	
	public function having($condition, $value = null)
	{
		return parent::having($this->translateColumns($condition), $value);
	}
	
	// ------------------------------------------------------------------------
	
	public function orderBy($column, $direction = '')
	{
		return parent::orderBy($this->translateColumns($column), $direction);
	}
	
	// ------------------------------------------------------------------------
	
	public function groupBy($columns, $table = false, $table_is_aliased = false)
	{
		return parent::groupBy($this->translateColumns($columns));
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Loads the related records with the names specified.
	 * 
	 * @param  string|array
	 * @return self
	 */
	public function related($related)
	{
		// TODO: Old code, rewrite?
		$related = (Array) $related;
		
		foreach($related as $rel)
		{
			$rel = explode('-', $rel);
			
			$mapper = $this->mapper;
			$old_alias = $this->main_object;
			$curr_alias =& $this->alias_paths;
			
			// make relations to related records
			foreach($rel as $relation)
			{
				if( ! isset($mapper->relations[$relation]))
				{
					throw new Db_Exception_MissingRelation("'".$mapper->class.'.'.$relation.'"');
				}
				
				if( ! isset($this->mapped_objects[$old_alias.'-'.$relation]))
				{
					$mapper->joinRelated($this, $relation, $old_alias);
				}
				
				// add to the relationship cascade
				$curr_alias[$relation] = array();
				
				// save the relationship class name
				$this->mapped_objects[$old_alias.'-'.$relation] = $mapper->relations[$relation];
				
				// go deeper
				$old_alias = $old_alias.'-'.$relation;
				$curr_alias =& $curr_alias[$relation];
				$mapper = Db::getMapper($mapper->relations[$relation]);
			}
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the result of this query.
	 * 
	 * @return array
	 */
	public function get()
	{
		return $this->_get();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns one result object object.
	 * 
	 * @return object|false
	 */
	public function getOne()
	{
		// TODO: Limit to one object from the db
		$ret = $this->_get();
		
		// we only fetch one object, don't return an array
		return empty($ret) ? false : array_shift($ret);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the raw database result object, no processing is made on it.
	 * 
	 * @return Db_Result
	 */
	public function getRaw()
	{
		return $this->_instance->query($this->__toString());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * A common method, doing the most work of the fetching.
	 * 
	 * @return array|object|false
	 */
	protected function _get()
	{
		// execute the query
		$ret = $this->_instance->query($this->__toString());
		
		// let the mapper convert the data into objects
		$ret = $this->mapper->extract($ret, $this->mapped_objects, $this->alias_paths, $this->main_object);
		
		return $ret;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		if(empty($this->from))
		{
			throw new Db_Exception_QueryIncomplete('Missing FROM part');
		}
		
		$str = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '') . implode(', ', $this->columns) . "\nFROM " . implode(', ', $this->from);
		
		if( ! empty($this->join))
		{
			$str .= "\n" . implode("\n", $this->join);
		}
		
		if( ! empty($this->where))
		{
			$str .= "\nWHERE ".$this->where_prefix.implode(' ', $this->where).$this->where_suffix;
		}
		elseif( ! empty($this->where_prefix) OR ! empty($this->where_suffix))
		{
			// TODO: Should the trim methods be here? Does it affect the SQL (except for removing parenthesis)?
			
			$str .= "\nWHERE ".preg_replace('/(?:\s+AND|\s+OR)?[\( ]*$/i', '', $this->where_prefix).' '.preg_replace('/^[\) ]*(?:AND\s+|OR\s+)?/i', '', $this->where_suffix);
		}
		
		if( ! empty($this->group_by))
		{
			$str .= "\nGROUP BY " . implode(', ', $this->group_by);
		}
		
		if( ! empty($this->having))
		{
			$str .= "\nHAVING " . implode(' ', $this->having);
		}

		if( ! empty($this->order_by))
		{
			$str .= "\nORDER BY " . implode(', ', $this->order_by);
		}

		if($this->limit !== false)
		{
			$str = $this->_instance->_limit($str, $this->limit, $this->offset);
		}
		
		return $str;
	}
	
	// ------------------------------------------------------------------------
	
	protected function translateColumns($string)
	{
		return str_ireplace($this->php_columns, $this->sql_columns, $string);
	}
}


/* End of file MapperSelect.php */
/* Location: ./lib/mapper/query */