<?php
/*
 * Created by Martin Wernståhl on 2009-04-14.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * SQL-builder which builds UPDATE queries.
 */
class Rdm_Query_Update extends Rdm_Query_Abstract
{
	/**
	 * The table(s) to update.
	 * 
	 * @var string|array
	 */
	protected $table;
	
	/**
	 * The LIMIT data.
	 * 
	 * @var int|false
	 */
	public $limit = false;
	
	/**
	 * The data to overwrite existing data.
	 *
	 * @var array
	 */
	public $data = array();
	
	/**
	 * @param Rdm_Connection
	 * @param string|array
	 */
	function __construct($db_instance, $table)
	{
		parent::__construct($db_instance);
		
		$this->table = $table;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the new value of a column.
	 *
	 * $column can be either the column name (string) or an associative
	 * array containing column => value pairs (then $value is ignored)
	 * 
	 * @param  string|array
	 * @param  string|Rdm_Query_select
	 * @return self
	 */
	public function set($column, $value = null)
	{
		if( ! is_null($value))
		{
			$column = array($column => $value);
		}
		
		if( ! is_array($column))
		{
			throw new InvalidArgumentException(gettype($column));
		}
		
		foreach($column as $k => $v)
		{
			if($v instanceof Rdm_Query_Select)
			{
				// need to limit to one row
				$v->limit(1);
				
				$this->data[$k] = '(' . $v->__toString() . ')';
			}
			elseif( ! $this->escape)
			{
				$this->data[$k] = $v;
			}
			else
			{
				$this->data[$k] = $this->_instance->escape($v);
			}
		}
		
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
		$sql = $this->getSQL();
		
		return $this->_instance->query($sql);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates the UPDATE SQL.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		if(empty($this->data))
		{
			throw new Rdm_Query_BuilderException('Missing data for update');
		}
		
		$str = 'UPDATE ' . $this->_instance->protectIdentifiers(implode(', ', array_map(array($this->_instance, 'prefix'), (Array)$this->table))) . "\nSET ";
        
		$set = array();
		foreach($this->data as $k => $v)
		{
			$set[] = $this->_instance->protectIdentifiers($k) . ' = ' . $v;
		}
        
		$str .= implode(', ', $set);
        
		if( ! empty($this->where))
		{
			$str .= "\nWHERE " . implode(' ', $this->where);
		}
		
		// do not allow ORDER BY and LIMIT on multi-table updates
		if(count($this->table) == 1)
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


/* End of file Update.php */
/* Location: ./lib/Rdm/Query */