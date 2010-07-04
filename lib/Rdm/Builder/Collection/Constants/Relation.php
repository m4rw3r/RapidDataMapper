<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Constants_Relation extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		if( ! count($desc->getRelations()))
		{
			// Nothing to do
			return;
		}
		
		$this->addPart('// Relation id constants
');
		
		foreach($desc->getRelations() as $r)
		{
			$this->addPart('/**
 * Constant representing the relation '.$r->getName().' between '.$desc->getClass().' and '.$r->getRelatedDescriptor()->getClass(true, true).'.
 */');
			$this->addPart('const '.ucfirst($r->getName()).' = '.$r->getIntegerIdentifier().';');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'constants_relation';
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return count($this->content) > 1 ? implode("\n", $this->content) : '';
	}
}


/* End of file Relation.php */
/* Location: ./lib/Rdm/Builder/Collection/Constants */