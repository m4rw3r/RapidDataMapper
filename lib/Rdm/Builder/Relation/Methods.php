<?php
/*
 * Created by Martin Wernståhl on 2010-04-09.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation_Methods extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->addPart('/**
 * Internal: Sets the aliases to use by this relation filter to create the ON filter
 * between the two tables.
 * 
 * @internal
 * @param  string
 * @param  string
 */
public function setAliases($alias, $parent_alias)
{
	$this->alias = $alias;
	$this->parent_alias = $parent_alias;
}

/**
 * Internal: Returns true if this relation filter can modify a related object.
 * 
 * @internal
 * @return boolean
 */
public function canModifyToMatch()
{
	return ! empty($this->parent_object);
}');
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'methods';
	}
}


/* End of file Methods.php */
/* Location: ./lib/Rdm/Builder/Relation */