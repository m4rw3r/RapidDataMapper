<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Create extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->addPart(new Rdm_Builder_Collection_Create_Plain($desc));
		
		foreach($desc->getRelations() as $rel)
		{
			$this->addPart(new Rdm_Builder_Collection_Create_FromRelatedEntity($rel, $desc));
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'create';
	}
}


/* End of file Create.php */
/* Location: ./lib/Rdm/Builder/Collection */