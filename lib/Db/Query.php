<?php
/*
 * Created by Martin Wernståhl on 2009-04-14.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * The base class for queries which filter data.
 * 
 * Contains basic functions for building the WHERE part of the query.
 */
class Db_Query
{
	/**
	 * WHERE data.
	 * 
	 * @var array
	 */
	public $where = array();
	
	/**
	 * ORDER BY data.
	 * 
	 * @var array
	 */
	public $order_by = array();
	
	/**
	 * Controls if escaping should be done or not.
	 * 
	 * @var bool
	 */
	protected $escape = true;
	
	/**
	 * The database driver instance.
	 * 
	 * @var Db_Connection
	 */
	protected $_instance;
	
	/**
	 * The parent query, for nested queries/query parts.
	 * 
	 * @var Db_Query|false
	 */
	protected $parent = false;
	
	function __construct($db_instance, $parent = false)
	{
		$this->_instance = $db_instance;
		$this->parent = $parent;
	}

	// ------------------------------------------------------------------------

	/**
	 * Determines if columns and where statements should be escaped.
	 * 
	 * @param  bool
	 * @return self
	 */
	public function escape($value = true)
	{
		$this->escape = (Bool) $value;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a part to the WHERE part of the query. 
	 *
	 * If the condition does not have an operator, "=" is added.
	 * 
	 * This function can do:
	 * - Nested where parts (in parenthesis, use where()->...->end()... or where('or')->...->end()->...)
	 * - Non-escaped where parts (uses only $condition, call escape(false) to enable)
	 * - Bound statements (use array in $value)
	 * - Column conditions / comparisons (leave $value empty, $condition is identifier protected)
	 * - Column-value comparisons ($condition is identifier protected and $value is escaped)
	 * 
	 * If an array is passed to condition, every key => value pair will be sent to where
	 * like this: where(key, value)   (and if key is missing: where(value))
	 * 
	 * @param  string				Operators like =, <, ! are preserved
	 * @param  string|array|null	Null to skip the value completely,
	 * 								just protects the identifiers in the condition.
	 * 								Array to use bound parameters
	 * @return self
	 */
	public function where($condition = false, $value = null)
	{
		// multiple conditions
		if(is_array($condition))
		{
			foreach($condition as $k => $v)
			{
				if( ! is_numeric($k))
				{
					$this->where($k, $v);
				}
				else
				{
					$this->where($v);
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
			$this->where[] = $part = new Db_Query($this->_instance, $this);
			
			return $part;
		}
		else
		{
			$pre = $this->getLogicalOperator($condition);
			
			// no escape
			if( ! $this->escape && is_null($value))
			{
				// add the raw sql
				$this->where[] = $pre . $condition;
			}
			// no escape, but with value
			elseif( ! $this->escape)
			{
				// add the raw sql
				$this->where[] = $pre . $condition . ($this->hasCmpOperator($condition) ? '' : ' =') . $value;
			}
			// bound statement
			elseif(is_array($value))
			{
				$this->where[] = $pre . $this->_instance->replaceBinds($condition, $value);
			}
			// subquery
			elseif($value instanceof Db_Query_Select)
			{
				$this->where[] = $pre . $this->_instance->protectIdentifiers($condition) .
					($this->hasCmpOperator($condition) ? '' : ' =') .' (' . $value . ')';
			}
			// just a condition to filter
			elseif(is_null($value))
			{
				$this->where[] = $pre . $this->_instance->protectIdentifiers($condition);
			}
			// normal match
			else
			{
				$condition = $this->_instance->protectIdentifiers($condition);

				$this->where[] = $pre . $condition .
					($this->hasCmpOperator($condition) ? '' : ' =') .' ' . $this->_instance->escape($value);
			}
			
			return $this;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a where part with binds.
	 * 
	 * You can also use where() to filter binds when you send it an array.
	 * 
	 * !ATTENTION!:
	 * Identifiers will not be protected in bound conditions!
	 * Only the bound data will be escaped!
	 * Escaping of values for LIKE condition does not escape % and _ !
	 *
	 * @param  string
	 * @param  mixed
	 * @return self
	 */
	public function bindWhere($condition, $binds)
	{
		$pre = $this->getLogicalOperator($condition);
		
		$this->where[] = $pre . $this->_instance->replaceBinds($condition, $binds);
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a WHERE IN to the WHERE part of the query.
	 * 
	 * @param  string
	 * @param  array|Db_Query_Select
	 * @return self
	 */
	public function whereIn($statement, $values)
	{
		$pre = $this->getLogicalOperator($statement);
		
		if($this->escape)
		{
			$statement = $this->_instance->protectIdentifiers($statement);
		}
		
		// create a list of values if there isn't a subquery
		if( ! $values instanceof Db_Query_Select)
		{
			$values = implode(', ', array_map(array($this->_instance, 'escape'), $values));
		}
		
		$this->where[] = $pre . $statement . ' IN (' . $values . ')';
		
		return $this;
	}
 
	// ------------------------------------------------------------------------
    
	/**
	 * Adds a WHERE NOT IN to the WHERE part of the query.
	 * 
	 * @param  string
	 * @param  array|Db_Query_Select
	 * @return self
	 */
	public function whereNotIn($statement, $values)
	{
		$pre = $this->getLogicalOperator($statement);
    	
		if($this->escape)
		{
			$statement = $this->_instance->protectIdentifiers($statement);
		}
    
		// create a list of values if there isn't a subquery
		if( ! $values instanceof Db_Query_Select)
		{
			$values = implode(', ', array_map(array($this->_instance, 'escape'), $values));
		}
    
		$this->where[] = $pre . $statement . ' NOT IN (' . $values . ')';
    
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a LIKE condition to the WHERE part of the query.
	 * 
	 * @param  string
	 * @param  string
	 * @param  string  Possible values: left, right and both
	 * @return self
	 */
	public function like($column, $value, $side = 'both')
	{
		$pre = $this->getLogicalOperator($column);
		
		// escape wildcards too
		$value = $this->_instance->escapeStr($value, true);
		
		switch($side)
		{
			case 'left':
				$value = '\'%' . $value . '\'';
				break;
			
			case 'right':
				$value = '\'' . $value . '%\'';
				break;
			
			default:
				$value = '\'%' . $value . '%\'';
				break;
		}
		
		$this->where[] = $pre . ($this->escape ? $this->_instance->protectIdentifiers($column) : $column) . ' LIKE ' . $value;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds columns to order by.
	 * 
	 * @param  string|array 	'random' results in random ordering
	 * @param  string			'asc' or 'desc'
	 * @return self
	 */
	public function orderBy($column, $direction = '')
	{
		if($this->escape == false)
		{
			$this->order_by[] = $column;
		}
		elseif(is_string($column) && strtolower($column) == 'random')
		{
			$this->order_by = array($this->_instance->RANDOM_KEYWORD);
		}
		else
		{
			if(($direction = strtoupper(trim($direction))) != '')
			{
				$direction = (in_array($direction, array('ASC', 'DESC'), true)) ? ' ' . $direction : ' ASC';
			}
		
			if(is_string($column) && strpos($column, ','))
			{
				$column = explode(',', $column);
			}
			
			foreach((Array)$column as $val)
			{
				$val = trim($val);
				$this->order_by[] = $this->_instance->protectIdentifiers($val) . $direction;
			}
		}
		
		return $this;
	}

	// ------------------------------------------------------------------------

	/**
	 * Ends the current nested query-part
	 * 
	 * @return Db_Query
	 */
	public function end()
	{
		return $this->parent;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Constructs a WHERE SQL fragment.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return '(' . implode(' ', $this->where) . ')';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the operator to use when separating WHERE conditions.
	 *
	 * Removes the operator (only OR) from $str if present.
	 * Do this before using protectIdentifiers().
	 * 
	 * @param  string
	 * @return string
	 */
	protected function getLogicalOperator(&$str)
	{	
		// determine if there is an OR prepended, if so let it return OR
		// otherwise let it return AND
		// and if we don't have anything in $where, empty
		
		// remove the first occurrence of OR and store the count (ie. 1 if we have a replace)
		$str = preg_replace('/^\s*or\s/i', '', $str, 1, $c);
		
		if(empty($this->where))
		{
			return '';
		}
		
		return $c ? 'OR ' : 'AND ';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if the string has a comparison operator.
	 * 
	 * @return bool
	 */
	protected function hasCmpOperator($str)
	{
		return preg_match('/[!=<>]\s*$/i', $str);
	}
}


/* End of file base.php */
/* Location: ./lib/query */