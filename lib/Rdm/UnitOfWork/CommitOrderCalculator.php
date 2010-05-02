<?php
/*
 * Created by Martin Wernståhl on 2010-04-18.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Commit order calculator which creates a list of which class to insert/update
 * first and to delete last.
 * 
 * This prevents database constraints from firing because a class which is
 * depended upon by others will be added first and deleted last.
 */
class Rdm_UnitOfWork_CommitOrderCalculator
{
	/**
	 * A list of dependencies, key depends on the list in the value.
	 * 
	 * @var array(string => array(string))
	 */
	public $dependencies = array();
	
	/**
	 * A temporary list of visited nodes.
	 * 
	 * @var array(string)
	 */
	protected $visited = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new commit order calculator.
	 * 
	 * @param  Initial list of dependencies, key => array(required_key)
	 */
	public function __construct(array $dependencies = array())
	{
		$this->dependencies = $dependencies;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Registers that $a depends on $b running before $a, if $b is not loaded,
	 * then $a will run anyway.
	 * 
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function registerDependency($a, $b)
	{
		empty($this->dependencies[$a]) && $this->dependencies[$a] = array();
		empty($this->dependencies[$b]) && $this->dependencies[$b] = array();
		
		in_array($b, $this->dependencies[$a]) OR $this->dependencies[$a][] = $b;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Registers a key without dependencies.
	 * 
	 * @param  string
	 * @return void
	 */
	public function register($a)
	{
		empty($this->dependencies[$a]) && $this->dependencies[$a] = array();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Depth first implementation of topological sorting, with visited stack.
	 * 
	 * @return array
	 */
	public function calculate()
	{
		$list = array();
		$this->visited = array();
		
		foreach(array_keys($this->dependencies) as $n)
		{
			$this->visit($n, $list, $n, array());
		}
		
		return $list;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Node visitor.
	 * 
	 * @return bool
	 */
	protected function visit($n, &$list, $root, $visited)
	{
		// Have we visited this node before?
		if(isset($this->visited[$n]))
		{
			// Yes, and have we visited it in this stack?
			return ! in_array($n, $visited);
		}
		
		// Set as visited, both in the current stack
		$this->visited[$n] = true;
		$visited[] = $n;
		
		
		foreach($this->dependencies[$n] as $dependency)
		{
			// Check if we have it loaded, because otherwise we can just continue
			// as $dependency is only is required to run earlier IF PRESENT
			if( ! empty($this->dependencies[$dependency]))
			{
				// Recurse:
				if( ! $this->visit($dependency, $list, $root, $visited) && $n != $root)
				{
					// We've visited it in this stack, cycle detected
					throw new Exception('Cycle detected from '.$n.' to '.$dependency);
				}
			}
		}
		
		$list[] = $n;
		
		return true;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Validates an order against the dependency list of this CommitOrderCalculator.
	 * 
	 * Used in debug purposes to validate generated order lists.
	 * 
	 * @throws Exception
	 * @param  array(string)
	 * @return void
	 */
	public function validate($list)
	{
		foreach($this->dependencies as $a => $array)
		{
			foreach($array as $b)
			{
				if(array_search($a, $list) < array_search($b, $list))
				{
					throw new Exception("$a depends on $b, ".array_search($a, $list).' < '.array_search($b, $list));
				}
			}
		}
	}
}


/* End of file CommitOrderCalculator.php */
/* Location: ./lib/Rdm/UnitOfWork */