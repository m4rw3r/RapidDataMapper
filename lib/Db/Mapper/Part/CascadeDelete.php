<?php
/*
 * Created by Martin Wernståhl on 2010-01-15.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the cascadeDelete() method of a Db_Mapper descendant.
 */
class Db_Mapper_Part_CascadeDelete extends Db_CodeBuilder_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'cascadeDelete';
		$this->param_list = '$object';
		
		$has_cascade = false;
		
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->getOnDeleteAction() == Db_Descriptor::CASCADE)
			{
				$has_cascade = true;
			}
		}
		
		// We've got a cascade
		if($has_cascade)
		{
			$this->addPart('$ret = true;');
			
			$this->addPart(new Db_Mapper_Part_CascadeDelete_IsObject($descriptor));
			
			$this->addPart(new Db_Mapper_Part_CascadeDelete_IsQuery($descriptor));
		}
		
		if( ! $has_cascade)
		{
			$this->addPart('return true;');
		}
		else
		{
			$this->addPart('return $ret;');
		}
	}
}


/* End of file CascadeDelete.php */
/* Location: ./lib/Db/Mapper/Part */