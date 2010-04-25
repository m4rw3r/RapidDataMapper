<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter extends Rdm_Util_Code_ClassBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setClassName($desc->getCollectionFilterClassName());
		$this->setExtends('Rdm_Collection_Filter');
		
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $col)
		{
			foreach($col->getDataTypeObject()->getCollectionFilterClasses() as $class)
			{
				$this->addPart(new $class($col, $desc));
			}
		}
		
		$this->addPart(new Rdm_Builder_CollectionFilter_FilterRelatedObject($desc));
	}
}


/* End of file CollectionFilter.php */
/* Location: ./lib/Rdm/Builder */