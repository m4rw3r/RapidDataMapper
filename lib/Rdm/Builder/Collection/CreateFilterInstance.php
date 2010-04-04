<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_CreateFilterInstance extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('createFilterInstance');
		
		$this->addPart('return new '.$desc->getClass().'CollectionFilter($this);');
	}
}


/* End of file CreateFilterInstance.php */
/* Location: ./lib/Rdm/Builder/Collection */