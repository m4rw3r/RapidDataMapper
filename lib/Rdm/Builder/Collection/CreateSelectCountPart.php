<?php
/*
 * Created by Martin Wernståhl on 2010-04-24.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_CreateSelectCountPart extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('createSelectCountPart');
		
		$db = $desc->getAdapter();
		
		// TODO: Check if there is another syntax for multi-column DISTINCT
		$columns = array();
		foreach($desc->getPrimaryKeys() as $c)
		{
			$columns[] = '$this->table_alias.\'.'.addcslashes($db->protectIdentifiers($c->getColumn()), "'");
		}
		
		$this->addPart('return \'COUNT(DISTINCT \'.'.implode(' + \\\'|\\\' + \'.', $columns).')\';');
	}
}


/* End of file CreateSelectCountPart.php */
/* Location: ./lib/Rdm/Builder/Collection */