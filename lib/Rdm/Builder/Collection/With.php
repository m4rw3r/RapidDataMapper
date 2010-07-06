<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_With extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('with');
		$this->setParamList('$relation_id');
		
		$constants = array();
		$collection_classes = array();
		foreach($desc->getRelations() as $rel)
		{
			$collection_classes[] = $rel->getRelatedDescriptor()->getCollectionClassName(true, true);
			$constants[] = ucfirst($rel->getName());
		}
		
		$this->setPhpDoc('Joins this collection\'s results with related data from one of this collection\'s 
relations.

@param  int  A relation constant'.(empty($constants) ? '' : ', one of the following: '.implode(', ', $constants)).'
@return '.(empty($collection_classes) ? 'void' : implode('|', $collection_classes)));
		
		if(count($desc->getRelations()))
		{
			$first = true;
			
			foreach($desc->getRelations() as $r)
			{
				$this->addPart(new Rdm_Builder_Collection_With_RelationCase($r, $desc, $first));
				
				$first = false;
			}
			
			// TODO: Replace with a proper exception class
			$this->addPart('throw new '.($desc->isNamespaced() ? '\\' : '').'Exception(\'No matching relation can be found for the class "'.$desc->getClass().'"\');');
		}
		else
		{
			// TODO: Replace with a proper exception class
			$this->addPart('throw new '.($desc->isNamespaced() ? '\\' : '').'Exception(\'No relations can be found for the class "'.$desc->getClass().'"\');');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		return parent::__toString();
	}
}


/* End of file With.php */
/* Location: ./lib/Rdm/Builder/Collection */