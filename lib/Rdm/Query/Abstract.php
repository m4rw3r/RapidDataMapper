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
class Rdm_Query_Abstract
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
	 * @var Rdm_Adapter
	 */
	protected $_instance;
	
	/**
	 * The parent query, for nested queries/query parts.
	 * 
	 * @var Rdm_Query_Abstract|false
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
					$this->createCondition($k, $v, $this->where);
				}
				else
				{
					$this->createCondition($v, null, $this->where);
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
			$this->where[] = $part = new Rdm_Query_Abstract($this->_instance, $this);
			
			return $part;
		}
		else
		{
			$this->createCondition($condition, $value, $this->where);
			
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
		$pre = self::getLogicalOperator($condition, $this->where);
		
		$this->where[] = $pre . $this->_instance->bindParamters($condition, $binds);
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a WHERE IN to the WHERE part of the query.
	 * 
	 * @param  string
	 * @param  array|Rdm_Query_Select
	 * @return self
	 */
	public function whereIn($column, $values)
	{
		$pre = self::getLogicalOperator($column, $this->where);
		
		if($this->escape)
		{
			$column = $this->_instance->protectIdentifiers($column);
		}
		
		// create a list of values if there isn't a subquery
		if( ! $values instanceof Rdm_Query_Select)
		{
			$this->where[] = $pre . $column . ' IN (' . 
				implode(', ', array_map(array($this->_instance, 'escape'), $values)) . ')';
		}
		else
		{
			$this->where[] = $pre . $column . ' IN (' . $values->__toString() . ')';
		}
		
		return $this;
	}
 
	// ------------------------------------------------------------------------
    
	/**
	 * Adds a WHERE NOT IN to the WHERE part of the query.
	 * 
	 * @param  string
	 * @param  array|Rdm_Query_Select
	 * @return self
	 */
	public function whereNotIn($column, $values)
	{
		$pre = self::getLogicalOperator($column, $this->where);
    	
		if($this->escape)
		{
			$column = $this->_instance->protectIdentifiers($column);
		}
    
		// create a list of values if there isn't a subquery
		if( ! $values instanceof Rdm_Query_Select)
		{
			$this->where[] = $pre . $column . ' NOT IN (' . 
				implode(', ', array_map(array($this->_instance, 'escape'), $values)) . ')';
		}
		else
		{
			$this->where[] = $pre . $column . ' NOT IN (' . $values->__toString() . ')';
		}
    
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a IS NULL condition to the where part of the query.
	 * 
	 * @param  string
	 * @param  bool		If to filter by IS NULL or IS NOT NULL
	 * @return self
	 */
	public function whereIsNull($column, $yes = true)
	{
		$pre = self::getLogicalOperator($column, $this->where);
    	
		if($this->escape)
		{
			$column = $this->_instance->protectIdentifiers($column);
		}
		
		$this->where[] = $pre.$column.($yes ? ' IS NULL' : ' IS NOT NULL');
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a IS NOT NULL condition to the where part of the query.
	 * 
	 * @param  string
	 * @return self
	 */
	public function whereIsNotNull($column)
	{
		return $this->whereIsNull($column, false);
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
		$pre = self::getLogicalOperator($column, $this->where);
		
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
			$this->order_by = array($this->_instance->getRandomKeyword());
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
	 * @return Rdm_Query_Abstract
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
	 * Wrapper around __toString() to be used as "external" accessor for the SQL.
	 * 
	 * __toString() should only be used by Rdm_Query* classes
	 * 
	 * @return string
	 */
	public function getSql()
	{
		return $this->__toString();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if the string has a comparison operator.
	 * 
	 * @return bool
	 */
	public static function hasCmpOperator($str)
	{
		return preg_match('/[!=<>]\s*$/i', $str);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a SQL condition and appends it on the $list array.
	 * 
	 * This method creates:
	 * - Non-escaped where parts (uses only $condition, $value = null, call escape(false) to enable)
	 * - Column conditions / comparisons ($value = null, $condition is identifier protected)
	 * - Column-value comparisons ($condition is identifier protected and $value is escaped)
	 * - Subqueries, $condition is string, $value is Rdm_Query_Select
	 * 
	 * @param  mixed	The condition, operators are preserved (<, >, =, !)
	 * @param  mixed	The value to add
	 * @param  array
	 * @return void
	 */
	public function createCondition($condition, $value, &$list)
	{
		$pre = self::getLogicalOperator($condition, $list);
		
		// no escape
		if( ! $this->escape && is_null($value))
		{
			// add the raw sql
			$list[] = $pre . $condition;
		}
		// no escape, but with value
		elseif( ! $this->escape)
		{
			if($value instanceof Rdm_Query_Select)
			{
				$list[] = $pre.$condition.(self::hasCmpOperator($condition) ? '' : ' =').' ('.$value->__toString().')';
			}
			else
			{
				// add the raw sql
				$list[] = $pre.$condition.(self::hasCmpOperator($condition) ? '' : ' =').$value;
			}
		}
		// subquery
		elseif($value instanceof Rdm_Query_Select)
		{
			$list[] = $pre.$this->_instance->protectIdentifiers($condition).
				(self::hasCmpOperator($condition) ? '' : ' =') .' ('.$value->__toString().')';
		}
		// just a condition to filter
		elseif(is_null($value))
		{
			$list[] = $pre . $this->_instance->protectIdentifiers($condition);
		}
		// normal match
		else
		{
			$condition = $this->_instance->protectIdentifiers($condition);

			$list[] = $pre . $condition .
				(self::hasCmpOperator($condition) ? '' : ' =') . ' ' . $this->_instance->escape($value);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the operator to use when separating WHERE conditions.
	 *
	 * Removes the operator (only OR) from $str if present.
	 * Do this before using protectIdentifiers().
	 * 
	 * @param  string	A reference to the condition
	 * @param  array	The list to which the condition later will be added
	 * @return string	The operator which should be used when adding the condition
	 */
	public static function getLogicalOperator(&$str, &$list)
	{	
		// determine if there is an OR prepended, if so let it return OR
		// otherwise let it return AND
		// and if we don't have anything in $where, empty
		
		// remove the first occurrence of OR and store the count (ie. 1 if we have a replace)
		$str = preg_replace('/^\s*or\s/i', '', $str, 1, $c);
		
		if(empty($list))
		{
			return '';
		}
		
		return $c ? 'OR ' : 'AND ';
	}
}


/* End of file Abstract.php */
/* Location: ./lib/Rdm/Query */