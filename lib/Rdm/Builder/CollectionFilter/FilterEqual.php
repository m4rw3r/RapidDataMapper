<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter_FilterEqual extends Rdm_Util_Code_Container
{
	function __construct(Rdm_Descriptor $desc)
	{
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $c)
		{
			$this->addPart(new Rdm_Builder_CollectionFilter_FilterEqualMethod($c, $desc));
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'filter_equal';
	}
}


/* End of file FilterEqual.php */
/* Location: ./lib/Rdm/Builder/CollectionFilter */