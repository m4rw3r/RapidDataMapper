<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Delete extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('delete');
		$this->setStatic(true);
		$this->setParamList('$object');
	}
}


/* End of file Delete.php */
/* Location: ./lib/Rdm/Builder/Collection */