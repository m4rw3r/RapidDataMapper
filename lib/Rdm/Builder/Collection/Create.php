<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Create extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('create');
		$this->setStatic(true);
		$this->setPhpDoc('@return '.$desc->getCollectionClassName());
		
		$this->addPart('return new '.$desc->getCollectionClassName().';');
	}
}


/* End of file Create.php */
/* Location: ./lib/Rdm/Builder/Collection */