<?php
/*
 * Created by Martin Wernståhl on 2009-09-05.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Adds an automatic slug conversion from one column to another.
 * 
 * Example:
 * <code>
 * $descriptor->add($descriptor->newColumn('title'));
 * $descriptor->add($descriptor->newColumn('slug'));
 * 
 * $descriptor->applyPlugin(new Db_Plugin_Sluggable(array('title' => 'slug')));
 * </code>
 */
class Db_Plugin_Sluggable extends Db_Plugin
{
	/**
	 * The callable to use when converting column values to slugs.
	 * 
	 * @var string
	 */
	protected $callable;
	
	/**
	 * The columns to apply the sluggable plugin to.
	 * 
	 * @var array
	 */
	protected $columns = array();
	
	/**
	 * A list to keep track of the decorators we've added.
	 * 
	 * @var array
	 */
	protected $decorators = array();
	
	/**
	 * @param  array			Contains an associative array with the $source_column => $slug_column
	 * @param  string|array		Contains the callable to call, must be public and static
	 */
	function __construct(array $columns, $callable = 'Db_Plugin_Sluggable::filterString')
	{
		// validate the callable
		if( ! is_callable($callable))
		{
			throw new Db_Exception('Db_Plugin_Sluggable: Erroneous callable format');
		}
		
		if( ! is_array($callable))
		{
			$callable = array($callable);
		}
		
		if(count($callable) == 2)
		{
			$ref = new ReflectionMethod(array_shift($callable), array_shift($callable));
			
			if( ! $ref->isStatic() OR ! $ref->isPublic())
			{
				throw new Db_Exception('Db_Plugin_Sluggable: Callable is not possible to call from the generated mapper.');
			}
			
			$callable = $ref->class.'::'.$ref->name;
		}
		elseif( ! function_exists($m = array_shift($callable)))
		{
			$ref = new ReflectionMethod($m);
			
			if( ! $ref->isStatic() OR ! $ref->isPublic())
			{
				throw new Db_Exception('Db_Plugin_Sluggable: Callable is not possible to call from the generated mapper.');
			}
			
			$callable = $ref->class.'::'.$ref->name;
		}
		elseif(function_exists($m))
		{
			$callable = $m;
		}
		else
		{
			throw new Db_Exception('Db_Plugin_Sluggable: Callable can not be found.');
		}
		
		if(version_compare(PHP_VERSION, '5.3', '>='))
		{
			// we may need to correct for namespaces:
			$callable = strpos($callable, '\\') === 0 ? $callable : '\\'.$callable;
		}
		
		// callable validation done
		$this->callable = $callable;
		
		// validate the columns array
		foreach($columns as $k => $v)
		{
			if(is_numeric($k))
			{
				throw new Db_Exception('Db_Plugin_Sluggable: The keys cannot be numeric in the columns parameter.');
			}
		}
		
		$this->columns = $columns;
	}
	
	// ------------------------------------------------------------------------
	
	public function init()
	{
		$columns = $this->descriptor->getColumns();
		
		// validate the destination columns
		$i = 0;
		foreach($columns as $column)
		{
			if(in_array($column->getColumn(), $this->columns))
			{
				$i++;
			}
		}
		
		if($i != count($this->columns))
		{
			throw new Db_Exception('Db_Plugin_Sluggable: Cannot find destination column(s).');
		}
		
		// add decorators to destination columns
		foreach($columns as $column)
		{
			if(in_array($column->getColumn(), $this->columns) &&
			 	! self::hasDecorator($column, 'Db_Plugin_Sluggable_Decorator'))
			{
				$source_name = array_search($column->getColumn(), $this->columns);
				$source_column = null;
				
				// find the source column
				foreach($columns as $c)
				{
					if($source_name == $c->getColumn())
					{
						$source_column = $c;
						break;
					}
				}
				
				if(is_null($source_column))
				{
					throw new Db_Exception('Db_Plugin_Sluggable: Cannot find source column "'.$source_name.'".');
				}
				
				$dec = new Db_Plugin_Sluggable_Decorator($column, $source_column, $this->callable);
				
				$this->descriptor->addDecorator($dec);
				
				$this->decorators[] = $dec;
			}
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function remove()
	{
		foreach($this->decorators as $k => $d)
		{
			$this->descriptor->removeDecorator($d);
			
			unset($this->decorators[$k]);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Converts everything except for [a-z0-9] to "-".
	 * 
	 * @param  string
	 * @return string
	 */
	public static function filterString($str)
	{
		$charset = 'UTF8';
		$str = strtolower(htmlentities($str, ENT_COMPAT, $charset));
		$str = preg_replace('/&(.)(acute|cedil|circ|lig|grave|ring|tilde|uml);/', "$1", $str);
		$str = preg_replace('/([^a-z0-9]+)/', '-', html_entity_decode($str, ENT_COMPAT, $charset));
		
		return trim($str, '-');
	}
}


/* End of file Sluggable.php */
/* Location: ./lib/Db/Plugin */