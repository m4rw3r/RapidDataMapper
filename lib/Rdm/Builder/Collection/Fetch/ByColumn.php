<?php
/*
 * Created by Martin Wernståhl on 2010-04-17.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Fetch_ByColumn extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor_Column $col, Rdm_Descriptor $desc)
	{
		$this->setMethodName('fetchBy'.ucfirst($col->getProperty()));
		$this->setStatic(true);
		
		$db = $desc->getAdapter();
		
		$this->setParamList('$'.$col->getProperty());
		$this->setPhpDoc('Fetches a single object from the database based on its '.$col->getProperty().' value.

@param  string  '.$col->getProperty().'
@return '.$desc->getClass().'|false');
		
		$this->addPart('$db = '.$desc->getFetchAdapterCode().';
$c = new '.$desc->getCollectionClassName().';');
		
		$this->addPart('$c->filters[] = \''.addcslashes($db->protectIdentifiers($col->getColumn()), "'").' = \'.$db->escape($'.$col->getProperty().');');
		
		$this->addPart('$c->populate();');
		
		$this->addPart('return current($c->contents);');
	}
}


/* End of file ByColumn.php */
/* Location: ./lib/Rdm/Builder/Collection/Fetch */