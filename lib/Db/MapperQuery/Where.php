<?php
/*
 * Created by Martin Wernståhl on 2009-11-14.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A special variant of Db_Query which converts PHP names to SQL names of the columns.
 */
class Db_MapperQuery_Where extends Db_Query
{
	
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
	
	// ------------------------------------------------------------------------
	
	function __construct($db_instance, $parent)
	{
		parent::__construct($db_instance, $parent);
		
		$this->php_columns = $parent->php_columns;
		$this->sql_columns = $parent->sql_columns;
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
		parent::bindWhere($this->translateColumns($condition), $binds);
	}
	
	// ------------------------------------------------------------------------

	public function whereIn($column, $values)
	{
		parent::whereIn($this->translateColumns($column), $values);
	}
	
	// ------------------------------------------------------------------------

	public function whereNotIn($column, $values)
	{
		parent::whereNotIn($this->translateColumns($column), $values);
	}
	
	// ------------------------------------------------------------------------
	
	public function like($column, $value, $side = 'both')
	{
		parent::like($this->translateColumns($column), $value, $side);
	}
	
	// ------------------------------------------------------------------------
	
	protected function translateColumns($string)
	{
		return str_ireplace($this->php_columns, $this->sql_columns, $string);
	}
}


/* End of file Where.php */
/* Location: ./lib/Db/MapperQuery */