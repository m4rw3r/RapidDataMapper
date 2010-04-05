<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter_FilterEqualMethod extends Rdm_Util_Code_MethodBuilder
{
	function __construct(Rdm_Descriptor_Column $column, Rdm_Descriptor $desc)
	{
		$this->setMethodName($column->getProperty());
		
		$this->addPart('return $this;');
	}
}


/* End of file FilterEqualMethod.php */
/* Location: ./lib/Rdm/Builder/CollectionFilter */