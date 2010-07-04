<?php
/*
 * Created by Martin Wernståhl on 2010-04-18.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Add extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('add');
		$this->setParamList('$object');
		$this->setPhpDoc('Adds a'.(in_array(strtolower(substr($desc->getClass(), 0, 1)), array('a', 'o', 'u', 'e', 'i', 'y')) ? 'n' : '').' '.$desc->getClass(true, true).' entity to this collection, this collection will be locked and
the entity will assume data which matches the filters of this collection.

@param  '.$desc->getClass(true, true).'
@return self');
		
		$this->addPart('if( ! $object instanceof '.$desc->getClass().')
{
	throw '.($desc->isNamespaced() ? '\\' : '') .'Rdm_Collection_Exception::expectingObjectOfClass(\''.addcslashes($desc->getClass(true, true), "'").'\');
}');
		
		$this->addPart('// Modify the object
if($this->_add($object))
{
	// Already in this collection
	return $this;
}');
		
		$this->addPart('// Add it to this collection\'s data
if( ! empty($object->__id))
{
	$this->contents[implode(\'|\', $object->__id)] = $object;
}
else
{
	'.$desc->getCollectionClassName().'::persist($object);
	$this->contents[] = $object;
}');
		
		$this->addPart('return $this;');
	}
}


/* End of file Fetch.php */
/* Location: ./lib/Rdm/Builder/Collection */