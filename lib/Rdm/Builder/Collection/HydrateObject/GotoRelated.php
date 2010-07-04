<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_HydrateObject_GotoRelated extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->addPart('
foreach($this->with as $join_alias => $join)
{
	if($join->join_type == '.($desc->isNamespaced() ? '\\' : '').'Rdm_Descriptor::HAS_MANY OR $join->join_type == '.($desc->isNamespaced() ? '\\' : '').'Rdm_Descriptor::MANY_TO_MANY)
	{
		if(empty($e->$join_alias))
		{
			// Clone the object
			$e->$join_alias = clone $join;
			$e->$join_alias->relation->parent_object = $e;
			$e->$join_alias->setPopulated();
		}
		
		$join->hydrateObject($row, $e->$join_alias->contents, $map);
	}
	else
	{
		$hash = array();
		
		$join->hydrateObject($row, $hash, $map);
		
		if($hash)
		{
			$e->$join_alias = array_shift($hash);
		}
	}
}');
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'goto_related';
	}
}


/* End of file GotoRelated.php */
/* Location: ./lib/Rdm/Builder/Collection/HydrateObject */