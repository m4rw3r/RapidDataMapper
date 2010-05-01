<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_PushChanges extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('pushChanges');
		$this->setStatic(true);
		$this->setParamList('$private_push = false');
		$this->setPhpDoc('Pushes all changes to the database.

@param  boolean  If to only push changes for the '.$desc->getCollectionClassName().'
@return void');
		
		$this->addPart('if( ! $private_push)
{
	return parent::pushChanges();
}
else
{
	return self::$unit_of_work->commit();
}');
	}
}


/* End of file PushChanges.php */
/* Location: ./lib/Rdm/Builder/Collection */