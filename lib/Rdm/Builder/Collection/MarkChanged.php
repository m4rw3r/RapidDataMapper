<?php
/*
 * Created by Martin Wernståhl on 2010-07-16.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_MarkChanged extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('markChanged');
		$this->setStatic(true);
		$this->setParamList('$object');
		$this->setPhpDoc('Marks a changed '.$desc->getClass(true, true).' object for inclusion in the next push.

@param  '.$desc->getClass().'
@return void');
		
		$this->addPart('if( ! $object instanceof '.$desc->getClass().')
{
	throw '.($desc->isNamespaced() ? '\\' : '').'Rdm_Collection_Exception::expectingObjectOfClass(\''.$desc->getClass(true, true).'\');
}');
		
		$key = array();
		foreach($desc->getPrimaryKeys() as $pk)
		{
			$key[] = '$object->'.$pk->getProperty();
		}
		
		$this->addPart('self::$unit_of_work->markEntityAsChanged($object, '.implode('.\'|\'.', $key).');');
	}
}


/* End of file MarkChanged.php */
/* Location: ./lib/Rdm/Builder/Collection */