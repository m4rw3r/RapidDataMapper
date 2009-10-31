<?php
/*
 * Created by Martin Wernståhl on 2009-04-13.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * SQL-builder which generates INSERT queries.
 * 
 * TODO: Make it extend Db_Query?
 */
class Db_Query_Insert
{
	/**
	 * The data to insert.
	 * 
	 * @var array
	 */
	protected $rows = array();
	
	/**
	 * If to escape the values.
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
	
	function __construct($db_instance, $table)
	{
		$this->_instance = $db_instance;
		
		$this->rows = array(array());
		$this->table = $table;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Switches escaping on and off
	 * 
	 * @param  bool
	 * @return self
	 */
	public function escape($val = true)
	{
		$this->escape = (Bool) $val;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the value of a specific column or a set of columns in the latest row to be inserted.
	 *
	 * If no row has been added before, a new one will be created.
	 * 
	 * $column can be either the column name (string) or an associative
	 * array containing column => value pairs (then $value is ignored)
	 * 
	 * @param  string|array
	 * @param  string|Db_Query_Select
	 * @return self
	 */
	public function set($column, $value = null)
	{
		if( ! is_null($value))
		{
			$column = array($column => $value);
		}
		
		$current =& $this->rows[count($this->rows) - 1];
		
		foreach($column as $k => $v)
		{
			$this->columns[$k] = true;
			
			if($v instanceof Db_Query_Select)
			{
				// need to limit to one row
				$v->limit(1);
				
				$current[$k] = '(' . $v->__toString() . ')';
			}
			elseif( ! $this->escape)
			{
				$current[$k] = $v;
			}
			else
			{
				$current[$k] = $this->_instance->escape($v);
			}
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds a row to be inserted, creating a multi insert.
	 * 
	 * @param  array   Associate array with column => data
	 * @return self
	 */
	public function add($data = false)
	{
		$this->rows[] = array();
		
		if($data)
		{
			$this->set($data);
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Executes the INSERT query.
	 * 
	 * @return int|false
	 */
	public function execute()
	{
		return $this->_instance->query($this->getSQL());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Wrapper around __toString() to be used as "external" accessor for the SQL.
	 * 
	 * __toString() should only be used by Db_Query* classes
	 * 
	 * @return string
	 */
	public function getSQL()
	{
		return $this->__toString();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates the INSERT SQL string.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		if(empty($this->columns))
		{
			throw new Db_Exception_QueryIncomplete('Columns missing in INSERT statement');
		}
		
		$columns = $this->_instance->protectIdentifiers(implode(', ', array_keys($this->columns)));
		
		$str = 'INSERT INTO ' . $this->_instance->protectIdentifiers($this->_instance->dbprefix . $this->table) . " ($columns)\nVALUES ";
		
		$values = array();
		
		foreach($this->rows as $row)
		{
			$data = array();
			
			// may occur if add was called before any data was assigned to the first row
			if(empty($row))
			{
				continue;
			}
			
			foreach(array_keys($this->columns) as $k)
			{
				if(isset($row[$k]))
				{
					$data[] = $row[$k];
				}
				else
				{
					$data[] = 'NULL';
				}
			}
			
			$values[] = '(' . implode(', ', $data) . ')';
		}
		
		$str .= implode(', ', $values);
		
		return $str;
	}
}


/* End of file insert.php */
/* Location: ./lib/query */