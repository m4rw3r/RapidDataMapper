<?php
/*
 * Created by Martin Wernståhl on 2009-03-14.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Result-set object, can be iterated.
 */
abstract class Db_Result implements IteratorAggregate, Countable
{
	/**
	 * Database handle.
	 * 
	 * @var resource
	 */
	protected $dbh;
	
	/**
	 * The result resource.
	 * 
	 * @var resource
	 */
	protected $resource;
	
	/**
	 * Cache for the number of rows.
	 * 
	 * @var int
	 */
	protected $num_rows;
	
	// --------------------------------------------------------------------
	
	function __construct(&$dbh, &$resource)
	{
		$this->dbh =& $dbh;
		$this->resource =& $resource;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Frees the database result upon garbage collection.
	 */
	function __destruct()
	{
		$this->freeResult();
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the specified row.
	 *
	 * If the first row is asked for, use next()/next_array() for performance
	 * 
	 * @param  int
	 * @param  string	Type can be object, array or the class name of a custom object
	 * @param  mixed	Parameters sent as the first parameter of a custom object
	 * @return object|array
	 */
	public function row($num = 0, $type = 'object', $param = false)
	{
		if( ! $this->resource OR ! $this->count() OR ! $this->seek($num))
		{
			return false;
		}
		
		if($type != 'object' AND $type != 'array')
		{
			// $type is classname
			
			$row = $this->nextArray();
			
			// create custom object
			$ret = new $type($param);
			
			foreach($row as $key => $value)
			{
				if ( ! isset($ret->$key))
				{
					$ret->$key = $value;
				}
			}
		}
		elseif($type == 'object')
		{
			$ret = $this->next();
		}
		else
		{
			$ret = $this->nextArray();
		}
		
		return $ret;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns a row as an array.
	 * 
	 * @param  int  row number
	 * @return array
	 */
	public function rowArray($n = 0)
	{
		return $this->row($n, 'array');
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns an array with the results.
	 * 
	 * @param  string	'object', 'array' or a custom class name
	 * @param  mixed	Parameters for a custom object
	 * @return array
	 */
	public function result($type = 'object', $param = false)
	{
		$ret = array();
		$count = $this->count();
		$this->rewind();
		
		if($type == 'object')
		{
			for($i = 0; $i < $count; $i++)
			{
				$ret[] = $this->next();
			}
		}
		elseif($type == 'array')
		{
			for($i = 0; $i < $count; $i++)
			{
				$ret[] = $this->nextArray();
			}
		}
		else
		{
			// custom object
			
			while($row = $this->nextArray())
			{
				// create custom object
				$obj = new $type($param);
				
				foreach($row as $key => $value)
				{
					if ( ! isset($ret->$key))
					{
						$obj->$key = $value;
					}
				}
				
				$ret[] = $obj;
			}
		}
		
		return $ret;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Alias for result('array');
	 * 
	 * @return array
	 */
	public function resultArray()
	{
		return $this->result('array');
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * 	Returns the last row in the result set.
	 *
	 * @param  string
	 * @param  mixed
	 * @return object|array
	 */
	public function firstRow($type = 'object', $params = false)
	{
		return $this->row(0, $type, $params);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the last row in the result set.
	 *
	 * @param  string
	 * @param  mixed
	 * @return object|array
	 */
	public function lastRow($type = 'object', $params = false)
	{
		return $this->row($this->count() - 1, $type, $params);
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Returns the specified column value, defaults to the first column in the first row.
	 * 
	 * Useful in COUNT() statements
	 * 
	 * @param  int
	 * @param  int
	 * @return mixed
	 */
	public function val($num = 0, $row = 0)
	{
		$row = array_values($this->row($row, 'array'));
		
		return array_key_exists($num, $row) ? $row[$num] : false;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * IteratorAggregate method which makes it possible to use this object inside a foreach() loop.
	 * 
	 * Usage:
	 * <code>
	 * $obj instanceof Ot_Result
	 * foreach($obj as $row)
	 * {
	 *     // ...
	 * </code>
	 *
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->result());
	}
	
	// --------------------------------------------------------------------
	// --  ABSTRACT METHODS                                              --
	// --------------------------------------------------------------------
		
	/**
	 * Returns the number of rows in this result set.
	 *
	 * @return int
	 */
	public function count() { throw new Exception('Not Implemented!'); }
	/**
	 * Moves the result set pointer to the supplied position.
	 * 
	 * Returns false if it fails ($n < 0 OR $n >= num_rows()).
	 *
	 * @param  int
	 * @return bool
	 */
	abstract public function seek($n);
	/**
	 * Resets the internal pointer.
	 * 
	 * @return void
	 */
	public function rewind()
	{
		return $this->seek(0);
	}
	/**
	 * Clears the associated resource.
	 * 
	 * @return void 
	 */
	abstract public function freeResult();
	/**
	 * Returns the next row in the result set.
	 *
	 * Returns the data on the position of the result set pointer, then increases the pointer.
	 * 
	 * @return object
	 */
	abstract public function next();
	/**
	 * Returns the next row in the result set.
	 *
	 * Returns the data on the position of the result set pointer, then increases the pointer.
	 * 
	 * @return array
	 */
	abstract public function nextArray();
	/**
	 * Returns the field names and their metadata.
	 *
	 * Format:
	 * <code>
	 * 'field_name' => stdClass{
	 *     'name':        'field_name',
	 *     'type':        'field_datatype',
	 *     'length':      'field_length',  // eg. need to be 45 in the case of VARCHAR(45)
	 *     'unsigned':    true,
	 *     'primary_key': true,
	 *     'auto_inc':    false
	 *     'default':     'default_value'
	 * }
	 * </code>
	 *
	 * @return array
	 */
	abstract public function metadata();
}


/* End of file Result.php */
/* Location: ./lib/Db */