<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection extends Rdm_Util_Code_ClassBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setClassName($desc->getClass().'Collection');
		$this->setExtends('Rdm_Collection');
		
		$this->addPart(new Rdm_Builder_Collection_Create($desc));
		$this->addPart(new Rdm_Builder_Collection_Persist($desc));
		$this->addPart(new Rdm_Builder_Collection_Delete($desc));
		$this->addPart(new Rdm_Builder_Collection_CreateFilterInstance($desc));
		$this->addPart(new Rdm_Builder_Collection_Populate($desc));
	}
}


/* End of file Collection.php */
/* Location: ./lib/Rdm/Builder */