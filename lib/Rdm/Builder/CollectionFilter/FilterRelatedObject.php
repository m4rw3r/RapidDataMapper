<?php
/*
 * Created by Martin Wernståhl on 2010-04-14.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter_FilterRelatedObject extends Rdm_Util_Code_Container
{
	function __construct(Rdm_Descriptor $desc)
	{
		foreach($desc->getRelations() as $r)
		{
			$this->addPart(new Rdm_Builder_CollectionFilter_FilterRelatedObjectMethod($r, $desc));
			$this->addPart(new Rdm_Builder_CollectionFilter_FilterRelatedCollectionMethod($r, $desc));
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'filter_related_object';
	}
}


/* End of file FilterRelatedObject.php */
/* Location: ./lib/Rdm/Builder/CollectionFilter */