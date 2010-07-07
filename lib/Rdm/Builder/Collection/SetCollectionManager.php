<?php
/*
 * Created by Martin Wernståhl on 2010-07-07.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_SetCollectionManager extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('setCollectionManager');
		$this->setStatic(true);
		$this->setParamList(($desc->isNamespaced() ? '\\' : '').'Rdm_CollectionManager $manager');
		$this->setPhpDoc('Registers a CollectionManager for this collection.

@param  Rdm_CollectionManager
@return void');
		
		$this->addPart('self::$config = $manager->getConfig();
self::$db = $manager->getConfig()->getAdapter();');
		
		$dependencies = array();
		foreach($desc->getRelations() as $rel)
		{
			// Only belongs to relations depend on something
			if($rel->getType() === Rdm_Descriptor::BELONGS_TO)
			{
				$dependencies[] = var_export($rel->getRelatedDescriptor()->getCollectionClassName(true), true);
			}
		}
		
		$this->addPart('$manager->registerCollectionClassName(\''.$desc->getCollectionClassName(true).'\', array('.implode(', ', $dependencies).'));');
		
		$this->addPart($desc->getCollectionClassName().'::setUnitOfWork(new '.$desc->getUnitOfWorkClassName().');');
	}
}


/* End of file SetCollectionManager.php */
/* Location: ./lib/Rdm/Builder/Collection */