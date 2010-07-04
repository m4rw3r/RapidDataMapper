<?php
/*
 * Created by Martin Wernståhl on 2009-03-27.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * SQL-builder which builds and executes SELECT queries.
 */
class Rdm_Query_Select extends Rdm_Query_Abstract
{
	/**
	 * SELECT data.
	 * 
	 * @var array
	 */
	public $columns = array();
	
	/**
	 * FROM data.
	 * 
	 * @var array
	 */
	public $from = array();
	
	/**
	 * JOIN data.
	 * 
	 * @var array
	 */
	public $join = array();
	
	/**
	 * HAVING data.
	 * 
	 * @var array
	 */
	public $having = array();
	
	/**
	 * GROUP BY data.
	 * 
	 * @var array
	 */
	public $group_by = array();
	
	/**
	 * LIMIT data.
	 * 
	 * @var int|false
	 */
	public $limit = false;
	
	/**
	 * OFFSET data.
	 * 
	 * @var int|false
	 */
	public $offset = false;
	
	/**
	 * If we should append DISTINCT to SELECT.
	 * 
	 * @var bool
	 */
	public $distinct = false;
	
	/**
	 * If to only render the WHERE part enclosed in parenthesis.
	 * 
	 * @var bool
	 */
	protected $only_where = false;
	
	/**
	 * Counter which is used for dynamic aliases.
	 * 
	 * @var int
	 */
	protected $alias_counter = 1;
	
	function __construct($db_instance, $parent = false)
	{
		parent::__construct($db_instance, $parent);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Determines if DISTINCT selection should be used.
	 * 
	 * @param  bool
	 * @return self
	 */
	public function distinct($value = true)
	{
		$this->distinct = (Bool) $value;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a column.
	 * 
	 * @param  string|array|Rdm_Query_Select
	 * @param  string				Only used when not escaping
	 * @param  bool					If to prepend the dbprefix (false = yes)
	 * @return self
	 */
	public function column($columns, $table = false, $table_is_aliased = false)
	{
		// do not iterate Rdm_Query_Select objects
		if($columns instanceof Rdm_Query_Select)
		{
			$columns = array($columns);
		}
		// separate the columns if they need to have a table prepended
		elseif(is_string($columns) && $table && strpos($columns, ','))
		{
			$columns = array_map('trim', explode(',', $columns));
		}
		
		// table to prefix the column(s) with
		// no need to add table prefix if it already is an alias
		$t = $table ? ($table_is_aliased ? '' : $this->_instance->dbprefix) . $table . '.' : '';
		
		foreach((Array)$columns as $alias => $col)
		{
			// subquery
			if($col instanceof Rdm_Query_Select)
			{
				if(is_numeric($alias))
				{
					$this->columns[] = '(' . $col->__toString() . ')';
				}
				else
				{
					$this->columns[] = '(' . $col->__toString() . ')' . ' AS ' . $this->_instance->protectIdentifiers($alias);
				}
			}
			// nonescaped columns
			elseif( ! $this->escape)
			{
				if(is_numeric($alias))
				{
					$this->columns[] = $col;
				}
				else
				{
					$this->columns[] = $col . ' AS ' . $this->_instance->protectIdentifiers($alias);
				}
			}
			// normal
			else
			{
				if(is_numeric($alias))
				{
					$this->columns[] = $this->_instance->protectIdentifiers($t . $col);
				}
				else
				{
					$this->columns[] = $this->_instance->protectIdentifiers($t . $col) . ' AS ' . $this->_instance->protectIdentifiers($alias);
				}
				
			}
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Specifies which columns to fetch and from which table.
	 *
	 * Tables are not affected by escape(false)
	 * 
	 * @param  array|string|Rdm_Query_Select 
	 * @param  array|string
	 * @return self
	 */
	public function from($tables, $columns = false)
	{
		// do not iterate the Rdm_Query_Select, pass it on instead
		if($tables instanceof Rdm_Query_Select)
		{
			$tables = array($tables);
		}
		
		foreach((Array)$tables as $alias => $table)
		{
			if(is_numeric($alias))
			{
				$alias = 't' . $this->alias_counter++;
			}
			
			// subquery?
			if($table instanceof Rdm_Query_Select)
			{
				$this->from[] = '(' . $table->__toString() . ') AS ' . $this->_instance->protectIdentifiers($alias);
			}
			else
			{
				$this->from[] = $this->_instance->protectIdentifiers($this->_instance->dbprefix . $table) .
					' AS ' . $this->_instance->protectIdentifiers($alias);
			}
			
			if($columns === false)
			{
				// fetch all
				$this->columns[] = $this->_instance->protectIdentifiers($alias . '.*');
			}
			else
			{
				$this->column($columns, $alias, true);
			}
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a JOIN clause to the query.
	 *
	 * The table is not affected by escape(false)
	 *
	 * @see Rdm_Query_Abstract::where()
	 *
	 * @param  string|array|Rdm_Query_Select	NOTE: Only one table is joined, array is used to alias it
	 * @param  string|array 					Parameters like the Rdm_Query_Abstract::where() method (array style)
	 * @param  string|array
	 * @param  string							The join type (left, right, inner, outer etc.)
	 * @return self
	 */
	public function join($table, $condition, $columns = false, $type = 'left')
	{
		// don't want to iterate Rdm_Query_Select objects
		if( ! is_array($table))
		{
			$alias = 1; // numeric, to generate an alias
		}
		else
		{
			list($alias, $table) = each($table); // get vars from array($alias => $table)
		}
		
		// generate alias, if not present
		if(is_numeric($alias))
		{
			$alias = 't' . $this->alias_counter++;
		}
		
		// build the condition like the where() method does
		$cond = array();
		foreach((Array) $condition as $k => $v)
		{
			// move the parameters if $k is not a condition
			if(is_numeric($k))
			{
				$k = $v;
				$v = null;
			}
			
			$this->createCondition($k, $v, $cond);
		}
		
		// subquery
		if($table instanceof Rdm_Query_Select)
		{
			$table = '(' . $table->__toString() . ')';
		}
		else
		{
			$table = $this->_instance->protectIdentifiers($this->_instance->dbprefix . $table);
		}
		
		// build it!
		$this->join[] = strtoupper($type) . ' JOIN ' . $table . ' AS ' .
			$this->_instance->protectIdentifiers($alias) . ' ON ' . implode(' ', $cond);
		
		// select the columns
		if($columns === false)
		{
			// fetch all
			$this->columns[] = $this->_instance->protectIdentifiers($alias . '.*');
		}
		else
		{
			$this->column($columns, $alias, true);
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a condition to the having part of the query.
	 * 
	 * No protection of identifiers occur on the condition.
	 * This because things like COUNT(*) is usually used in HAVING.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return self
	 */
	public function having($condition, $value = null)
	{
		$pre = self::getLogicalOperator($condition, $this->having);
		
		if( ! $this->escape)
		{
			$this->having[] = $pre . $condition . ' ' . $value;
		}
		elseif( ! is_null($value))
		{
			if( ! self::hasCmpOperator($condition))
			{
				$condition .= ' =';
			}
			
			$this->having[] = $pre . $condition . ' ' . $this->_instance->escape($value);
		}
		else
		{
			$this->having[] = $pre . $condition;
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds columns to the group by clause.
	 *
	 * Everything is identifier-protected.
	 * 
	 * @param  string|array
	 * @param  string		Table/table-alias to prefix the columns with
	 * @param  bool			If to prepend the dbprefix to the table (false = yes)
	 * @return self
	 */
	public function groupBy($columns, $table = false, $table_is_aliased = false)
	{
		if(is_string($columns) && $table && strpos($columns, ','))
		{
			$columns = array_map('trim', explode(',', $columns));
		}
		
		// table to prefix the column(s) with
		// no need to add table prefix if it already is an alias
		$t = $table ? ($table_is_aliased ? '' : $this->_instance->dbprefix) . $table . '.' : '';
		
		foreach((Array)$columns as $col)
		{
			$this->group_by[] = $this->_instance->protectIdentifiers($t . $col);
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the limit and an optional offset for this query.
	 * 
	 * @param  int
	 * @param  int
	 * @return self
	 */
	public function limit($limit, $offset = false)
	{
		$this->limit = (Int) $limit;
		
		if($offset !== false)
		{
			$this->offset = (Int) $offset;
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the offset for this query.
	 *
	 * @param  int 
	 * @return self
	 */
	public function offset($offset)
	{
		$this->offset = (Int) $offset;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Counts all the rows returned by this query.
	 * 
	 * @param  string
	 * @return int|false
	 */
	public function count($column = false)
	{
		if($column === false)
		{
			// just count all the rows
			$column = '1';
		}
		elseif($this->escape)
		{
			// escape the string
			$column = $this->_instance->protectIdentifiers($column);
		}
		
		// save it, so we still can use this query object here
		$tmp = $this->columns;
		
		// construct the COUNT
		$this->columns = array('COUNT(' . $column . ')');
		
		// call
		$ret = $this->_instance->query($this->__toString());
		
		// reset the columns
		$this->columns = $tmp;
		
		// make sure that we return an int unless error
		if($ret === false)
		{
			return false;
		}
		else
		{
			return ($r = $ret->val()) !== false ? (Int) $r : false;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Executes the SELECT query.
	 * 
	 * @return Rdm_Result
	 */
	public function get()
	{
		$sql = $this->__toString();
		
		return $this->_instance->query($sql);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates the sql of this query object.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		if(empty($this->from))
		{
			throw Rdm_Query_BuilderException::missingFrom('SELECT');
		}
		
		$str = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '') . implode(', ', $this->columns) . "\nFROM " . implode(', ', $this->from);
		
		if( ! empty($this->join))
		{
			$str .= "\n" . implode("\n", $this->join);
		}
		
		if( ! empty($this->where))
		{
			$str .= "\nWHERE " . implode(' ', $this->where);
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
			$str = $this->_instance->limitSqlQuery($str, $this->limit, $this->offset);
		}
		
		return $str;
	}
}


/* End of file Select.php */
/* Location: ./lib/Rdm/Query */