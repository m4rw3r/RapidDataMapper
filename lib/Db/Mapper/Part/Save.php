<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the save() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Save extends Db_CodeBuilder_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'save';
		$this->param_list = '$object';
		
		// HOOK: on_save
		$this->addPart($descriptor->getHookCode('on_save', '$object'));
		
		foreach($descriptor->getRelations() as $rel)
		{
			$this->addPart($rel->getPreSaveRelationCode('$object'));
		}
		
		$this->addPart(new Db_Mapper_Part_Save_Insert($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Save_Update($descriptor));
		
		// TODO: Maybe force all to exit and call post save? Then we have a common exit point, now we exit anywhere in the code added above
		
		// HOOK: post_save
		$this->addPart($descriptor->getHookCode('post_save', '$object'));
		
		$this->addPart('return true;');
	}
}


/* End of file save.php */
/* Location: ./lib/Db/Mapper/Part */