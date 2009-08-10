<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the save() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Save extends Db_Mapper_Code_Method
{
	protected $descriptor;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->name = 'populateFindQuery';
		$this->param_list = '$object';
		
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
		// TODO: Add on_save hook
		
		// TODO: Add Belongs To relation code
		
		$this->addPart(new Db_Mapper_Part_Save_Insert($this->descriptor));
		
		$this->addPart(new Db_Mapper_Part_Save_Update($this->descriptor));
		
		// TODO: Add post_save hook
		
		$this->addPart('return true;');
	}
}


/* End of file save.php */
/* Location: ./lib/Db/Mapper/Part */