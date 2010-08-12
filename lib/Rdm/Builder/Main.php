<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Main extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		if($desc->isNamespaced())
		{
			$this->addPart('namespace '.$desc->getNamespace().';');
		}
		
		$this->addPart(new Rdm_Builder_CollectionBase($desc));
		
		$this->addPart(new Rdm_Builder_Collection($desc));
		
		$this->addPart(new Rdm_Builder_CollectionFilter($desc));
		$this->addPart(new Rdm_Builder_UnitOfWork($desc));
		
		foreach($desc->getRelations() as $rel)
		{
			$this->addPart(new Rdm_Builder_Relation($rel, $desc));
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'name';
	}
}


/* End of file Main.php */
/* Location: ./lib/Rdm/Builder */