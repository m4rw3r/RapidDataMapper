<?php
/*
 * Created by Martin Wernståhl on 2010-04-17.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Fetch extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->addPart(new Rdm_Builder_Collection_Fetch_ByPrimaryKey($desc));
		
		/*foreach(array_merge($desc->getColumns(), $desc->getPrimaryKeys()) as $c)
		{
			$this->addPart(new Rdm_Builder_Collection_Fetch_ByColumn($c, $desc));
		}*/
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'fetch';
	}
}


/* End of file Fetch.php */
/* Location: ./lib/Rdm/Builder/Collection */