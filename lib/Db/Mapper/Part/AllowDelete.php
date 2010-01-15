<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the allowDelete() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_AllowDelete extends Db_CodeBuilder_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'allowDelete';
		$this->param_list = '$object';
		
		$has_restrict = false;
		
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() == Db_Descriptor::RESTRICT)
			{
				$has_restrict = true;
			}
		}
		
		// We've got a restrict
		if($has_restrict)
		{
			$this->addPart('$allow = true;');
			
			$this->addPart(new Db_Mapper_Part_AllowDelete_IsObject($descriptor));
			
			// TODO: Add IsQuery which will be used by the cascade to check if the cascades has any restricts
		}
		
		if( ! $has_restrict)
		{
			$this->addPart('return true;');
		}
		else
		{
			$this->addPart('return $allow;');
		}
	}
}


/* End of file AllowDelete.php */
/* Location: ./lib/Db/Mapper/Part */