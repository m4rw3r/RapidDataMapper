<?php
/*
 * Created by Martin Wernståhl on 2009-11-13.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class for building the compiled PHP code for caching.
 */
class Db_CompiledBuilder extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $desc)
	{
		$this->addPart(new Db_Mapper_Builder($desc));
		
		$this->addPart(new Db_MapperQuery_Builder($desc));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return '';
	}
}


/* End of file CompiledBuilder.php */
/* Location: ./lib/Db */