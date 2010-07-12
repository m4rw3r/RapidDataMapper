<?php
/*
 * Created by Martin Wernståhl on 2010-04-18.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Remove extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('remove');
		$this->setParamList('$object');
		$this->setPhpDoc('Removes a'.(in_array(strtolower(substr($desc->getClass(), 0, 1)), array('a', 'o', 'u', 'e', 'i', 'y')) ? 'n' : '').' '.$desc->getClass(true, true).' entity from this collection, this collection will be locked and
the entity will TODO

@param  '.$desc->getClass(true, true).'
@return self');
		
		$this->addPart('if( ! $object instanceof '.$desc->getClass().')
{
	throw '.($desc->isNamespaced() ? '\\' : '') .'Rdm_Collection_Exception::expectingObjectOfClass(\''.addcslashes($desc->getClass(true, true), "'").'\');
}');
		
		$this->addPart('// Modify the object
if($this->_remove($object))
{
	// Already in this collection
	return $this;
}');
		
		$this->addPart('return $this;');
	}
}


/* End of file Remove.php */
/* Location: ./lib/Rdm/Builder/Collection */