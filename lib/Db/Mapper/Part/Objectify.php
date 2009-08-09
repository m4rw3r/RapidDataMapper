<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the Objectify() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Objectify extends Db_Mapper_Code_Method
{
	protected $descriptor;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->name = 'objectify';
		$this->param_list = '&$res, $row, $alias, array &$mappers, array $alias_paths';
		
		$this->descriptor = $desc;
		
		$this->addContents();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the default contents of this method.
	 * 
	 * @return void
	 */
	public function addContents()
	{
		$this->addPart('if('.$this->descriptor->getNotContainsObjectCode('$res', '$alias').')
{
	return null;
}');
		
		$this->addPart('$uid = '.$this->descriptor->getUidCode('$res', '$alias').';');
		
		$this->addPart(new Db_Mapper_Part_Objectify_NewObj($this->descriptor));
		
		// TODO: Add relation handling code
	}
}


/* End of file Objectify.php */
/* Location: ./lib/Db/Mapper/Part */