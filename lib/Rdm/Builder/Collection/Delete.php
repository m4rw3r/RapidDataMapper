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
		$this->setPhpDoc('Deletes an object of type '.$desc->getClass().' from the database.

@param  '.$desc->getClass().'
@return void');
		
		$this->addPart('if( ! $object instanceof '.$desc->getClass().')
{
	'.($desc->isNamespaced() ? '\\' : '') .'Rdm_Collection_Exception::expectingObjectOfType(\''.$desc->getClass().'\');
}');
		
		$this->addPart('self::$unit_of_work->addForDelete($object, implode(\'|\', $object->__id));');
	}
}


/* End of file Delete.php */
/* Location: ./lib/Rdm/Builder/Collection */