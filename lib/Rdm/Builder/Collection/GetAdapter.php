<?php
/*
 * Created by Martin Wernståhl on 2010-07-07.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_GetAdapter extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('getAdapter');
		$this->setPhpDoc('Returns the adapter which is registered with this collection,
used because of PHP 5.2 compatibility.

@return Rdm_Adapter');
		
		$this->addPart('return self::$db;');
	}
}


/* End of file GetAdapter.php */
/* Location: ./lib/Rdm/Builder/Collection */