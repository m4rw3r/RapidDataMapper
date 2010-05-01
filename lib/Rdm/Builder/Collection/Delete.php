<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Delete extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('delete');
		$this->setStatic(true);
		$this->setParamList('$object');
		
		$this->addPart('if( ! $object instanceof '.$desc->getClass().')
{
	Rdm_Collection_Exception::expectingObjectOfType(\''.$desc->getClass().'\');
}');
		
		$this->addPart('self::$unit_of_work->addForDelete($object, implode(\'|\', $object->__id));');
	}
}


/* End of file Delete.php */
/* Location: ./lib/Rdm/Builder/Collection */