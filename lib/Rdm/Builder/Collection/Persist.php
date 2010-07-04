<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Persist extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('persist');
		$this->setStatic(true);
		$this->setParamList('$object');
		$this->setPhpDoc('Persists a newly created '.$desc->getClass(true, true).' object in the database.

@param  '.$desc->getClass().'
@return void');
		
		$this->addPart('if( ! $object instanceof '.$desc->getClass().')
{
	throw '.($desc->isNamespaced() ? '\\' : '').'Rdm_Collection_Exception::expectingObjectOfClass(\''.$desc->getClass(true, true).'\');
}');
		
		$this->addPart('self::$unit_of_work->addNewEntity($object);');
	}
}


/* End of file Persist.php */
/* Location: ./lib/Rdm/Builder/Collection */