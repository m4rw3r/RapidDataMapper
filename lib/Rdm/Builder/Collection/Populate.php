<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Populate extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('populate');
		
		// TODO: Code
		
		$this->addPart('$this->is_populated = true;');
		$this->addPart('$this->is_locked = true;');
	}
}


/* End of file Populate.php */
/* Location: ./lib/Rdm/Builder/Collection */