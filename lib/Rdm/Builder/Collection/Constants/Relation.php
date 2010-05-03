<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_RelationConstants extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->addPart('// Relation id constants');
		
		foreach($desc->getRelations() as $r)
		{
			$this->addPart('const '.ucfirst($r->getName()).' = '.$r->getIntegerIdentifier().';');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'constants';
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return count($this->content) > 1 ? implode("\n", $this->content) : '';
	}
}


/* End of file RelationConstants.php */
/* Location: ./lib/Rdm/Builder/Collection */