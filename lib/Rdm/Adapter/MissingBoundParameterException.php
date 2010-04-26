<?php
/*
 * Created by Martin Wernståhl on 2009-08-24.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Exception for the event that bound parameter is missing
 */
class Rdm_Adapter_MissingBoundParameterException extends Exception implements Rdm_Exception
{
	/**
	 * The name of the missing parameter.
	 * 
	 * @var string
	 */
	protected $name;
	
	function __construct($name)
	{
		parent::__construct('Missing Bound Parameter "'.$name.'"');
		
		$this->name = $name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the parameter that is missing.
	 * 
	 * @return string
	 */
	public function getParameterName()
	{
		return $this->name;
	}
}


/* End of file MissingBoundParameterException.php */
/* Location: ./lib/Rdm/Adapter */