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
		$this->name = 'save';
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
		// HOOK: on_save
		$this->addPart($this->descriptor->getHookCode('on_save', '$object'));
		
		foreach($this->descriptor->getRelations() as $rel)
		{
			$this->addPart($rel->getPreSaveRelationCode('$object'));
		}
		
		$this->addPart(new Db_Mapper_Part_Save_Insert($this->descriptor));
		
		$this->addPart(new Db_Mapper_Part_Save_Update($this->descriptor));
		
		// TODO: Maybe force all to exit and call post save? Then we have a common exit point, now we exit anywhere in the code added above
		
		// HOOK: post_save
		$this->addPart($this->descriptor->getHookCode('post_save', '$object'));
		
		$this->addPart('return true;');
	}
}


/* End of file save.php */
/* Location: ./lib/Db/Mapper/Part */