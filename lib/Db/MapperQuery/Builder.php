<?php
/*
 * Created by Martin Wernståhl on 2009-11-13.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class for building the specialized MapperQuery descendants.
 */
class Db_MapperQuery_Builder extends Db_CodeBuilder_Class
{
	function __construct(Db_Descriptor $desc)
	{
		$this->name = 'Db_Compiled_'.$desc->getClass().'MapperQuery';
		$this->extends = 'Db_MapperQuery';
		
		$this->addPart(new Db_MapperQuery_Part_Properties($desc));
		
		$this->addPart(new Db_MapperQuery_Part_Constructor($desc));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'query';
	}
}


/* End of file Builder.php */
/* Location: ./lib/Db/MapperQuery */