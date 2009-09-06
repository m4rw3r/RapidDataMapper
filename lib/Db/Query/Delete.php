<?php
/*
 * Created by Martin Wernståhl on 2009-04-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * SQL-builder which generates DELETE queries.
 */
class Db_Query_Delete extends Db_Query
{
	/**
	 * The JOIN data.
	 */
	public $join = array();
	
	/**
	 * The table(s) to delete.
	 * 
	 * @var array
	 */
	protected $table = array();
	
	/**
	 * The table(s) to get data from.
	 * 
	 * @var string|array
	 */
	protected $from = array();
	
	/**
	 * The LIMIT data.
	 * 
	 * @var int|false
	 */
	public $limit = false;
	
	/**
	 * Alias counter for subqueries wo. defined aliases.
	 * 
	 * @var int
	 */
	protected $alias_counter = 1;
	
	/**
	 * @param Db_Connection
	 * @param string|array
	 */
	function __construct($db_instance, $table)
	{
		parent::__construct($db_instance);
		
		// determine what aliases to delete
		foreach((Array)$table as $a => $t)
		{
			if(is_numeric($a))
			{
				$this->table[] = $this->_instance->dbprefix . $t;
			}
			else
			{
				$this->table[] = $a;
			}
		}
		
		$this->from($table);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds another table to match with, ie. a cross join.
	 * 
	 * @param  string|array
	 * @return self
	 */
	public function from($tables)
	{
		// do not iterate the Db_Query_Select, pass it on instead
		if($tables instanceof Db_Query_Select)
		{
			$tables = array($tables);
		}
		
		foreach((Array)$tables as $alias => $table)
		{
			// subquery?
			if($table instanceof Db_Query_Select)
			{
				// subqueries requires aliases
				if(is_numeric($alias))
				{
					$alias = 't' . $this->alias_counter++;
				}
				
				$this->from[] = '(' . $table->__toString() . ') AS ' . $this->_instance->protectIdentifiers($alias);
			}
			else
			{
				if(is_numeric($alias))
				{
					$this->from[] = $this->_instance->protectIdentifiers($this->_instance->dbprefix . $table);
				}
				else
				{
					$this->from[] = $this->_instance->protectIdentifiers($this->_instance->dbprefix . $table) .
						' AS ' . $this->_instance->protectIdentifiers($alias);
				}
			}
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds an alias which rows will be removed from.
	 * 
	 * NOTE:
	 * You MUST join the required alias to be able to delete from it.
	 * 
	 * @param  string|array
	 * @return self
	 */
	public function addDelete($alias)
	{
		$this->table = array_merge($this->table, (Array)$alias);
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a JOIN clause to the query.
	 *
	 * The table is not affected by escape(false)
	 *
	 * @see Db_Query::where()
	 *
	 * @param  string|array|Db_Query_Select		NOTE: Only one table is joined, array is used to alias it
	 * @param  string|array 					Parameters like the Db_Query::where() method (array style)
	 * @param  string							The join type (left, right, inner, outer etc.)
	 * @return self
	 */
	public function join($table, $condition, $type = 'left')
	{
		// don't want to iterate Db_Query_select objects
		if( ! is_array($table))
		{
			$alias = 1; // numeric, to generate an alias
		}
		else
		{
			list($alias, $table) = each($table); // get vars from array($alias => $table)
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
		if($table instanceof Db_Query_Select)
		{
			// subqueries requires aliases
			if(is_numeric($alias))
			{
				$alias = 't' . $this->alias_counter++;
			}
			
			$table = '(' . $table->__toString() . ')';
		}
		else
		{
			$table = $this->_instance->protectIdentifiers($this->_instance->dbprefix . $table);
		}
		
		if(is_numeric($alias))
		{
			$alias = '';
		}
		else
		{
			$alias = ' AS ' . $this->_instance->protectIdentifiers($alias);
		}
		
		// build it!
		$this->join[] = strtoupper($type) . ' JOIN ' . $table . $alias . ' ON ' . implode(' ', $cond);
		
		return $this;
	}

	// ------------------------------------------------------------------------
    
	/**
	 * Sets a LIMIT for how many rows which will be updated.
	 * 
	 * @param  int
	 * @return self
	 */
	public function limit($limit)
	{
		$this->limit = $limit;
    
		return $this;
	}
    
	// ------------------------------------------------------------------------
    
	/**
	 * Runs this UPDATE query.
	 * 
	 * @return int|false
	 */
	public function execute()
	{
		if(empty($this->where))
		{
			Db::log(Db::WARNING, 'Executing delete query without filter.');
		}
		
		$sql = $this->__toString();
    
		return $this->_instance->query($sql);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates the DELETE SQL query.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$str = 'DELETE';
		
		if(count($this->from) > 1 OR ! empty($this->join))
		{
			$str .= ' ' . $this->_instance->protectIdentifiers(implode(', ', $this->table));
		}
		
		$str .= "\nFROM " . implode(', ', $this->from);
		
		if( ! empty($this->join))
		{
			$str .= "\n" . implode("\n", $this->join);
		}
		
		if( ! empty($this->where))
		{
			$str .= "\nWHERE " . implode(' ', $this->where);
		}
		
		// do not allow order by or limit on multi deletes
		if(count($this->from) == 1 AND ! empty($this->from))
		{
			if( ! empty($this->order_by))
			{
				$str .= "\nORDER BY " . implode(', ', $this->order_by);
			}
        
			if($this->limit !== false)
			{
				$str = $this->_instance->_limit($str, $this->limit, 0);
			}
		}
		
		return $str;
	}
}


/* End of file delete.php */
/* Location: ./lib/query */