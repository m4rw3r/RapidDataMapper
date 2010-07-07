<?php
/*
 * Created by Martin Wernståhl on 2010-07-07.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_UnitOfWork_Constructor extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('__construct');
		
		$this->addPart('$this->db = '.$desc->getCollectionClassName().'::$db;');
	}
}


/* End of file ProcessSingleChanges.php */
/* Location: ./lib/Rdm/Builder/UnitOfWork */