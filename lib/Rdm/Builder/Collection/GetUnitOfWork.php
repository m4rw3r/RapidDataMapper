<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_GetUnitOfWork extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('getUnitOfWork');
		$this->setStatic(true);
		
		$this->addPart('return self::$unit_of_work;');
	}
}


/* End of file GetUnitOfWork.php */
/* Location: ./lib/Rdm/Builder/Collection */