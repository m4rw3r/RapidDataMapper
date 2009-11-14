<?php
/*
 * Created by Martin Wernståhl on 2009-11-14.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the __constructor() method of a Db_MapperQuery descendant.
 */
class Db_MapperQuery_Part_Constructor extends Db_CodeBuilder_Method
{
	/**
	 * Flag for the render method.
	 * 
	 * @var bool
	 */
	public $render = false;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->name = '__construct';
		$this->param_list = '$mapper';
		
		$this->addPart('parent::__construct($mapper);');
		
		// HOOK: on_find
		$code = $desc->getHookCode('on_find', false, '$this');
		
		// do we have code to add?
		if( ! empty($code))
		{
			$this->addCode($code);
			
			// switch render flag to on
			$render = true;
		}
		
		// TODO: Add autoloaded join-related handling code
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Modded variant that does selective rendering depending on if the $render var is set to true.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->render ? parent::__toString() : '';
	}
}


/* End of file Constructor.php */
/* Location: ./lib/Db/Mapper/Part */