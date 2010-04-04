<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter extends Rdm_Util_Code_ClassBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setClassName($desc->getClass().'CollectionFilter');
		$this->setExtends('Rdm_Collection_Filter');
	}
}


/* End of file CollectionFilter.php */
/* Location: ./lib/Rdm/Builder */