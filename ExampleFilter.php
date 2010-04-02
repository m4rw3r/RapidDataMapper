<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class ExampleFilter extends Rdm_Collection_Filter
{
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __call($method, $value)
	{
		empty($this->filters) OR $this->filters[] = 'AND';
		
		$this->filters[] = $method.' = '.current($value);
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __toString()
	{
		return '('.implode(' ', $this->filters).')';
	}
}


/* End of file index.php */
/* Location: ./experimental/index.php */