<?php
/*
 * Created by Martin Wernståhl on 2010-07-17.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_GetUnitOfWorkInstance extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('getUnitOfWorkInstance');
		$this->setPhpDoc('Internal: Returns the UnitOfWork instance for this collection,
PHP 5.2 compatibility.

@return Rdm_UnitOfWork');
		
		$this->addPart('return self::$unit_of_work;');
	}
}


/* End of file GetUnitOfWorkInstance.php */
/* Location: ./lib/Rdm/Builder/Collection */