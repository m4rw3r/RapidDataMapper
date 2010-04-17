<?php
/*
 * Created by Martin Wernståhl on 2010-04-17.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_Fetch_ByPrimaryKey extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('fetchByPrimaryKey');
		$this->setStatic(true);
		
		$db = $desc->getAdapter();
		
		$params = array();
		$php_doc_params = array();
		$filter = array();
		foreach($desc->getPrimaryKeys() as $pk)
		{
			$params[] = '$'.$pk->getProperty();
			$php_doc_params[] = '@param  string  '.$pk->getProperty();
			$filter[] = addcslashes($db->protectIdentifiers($pk->getColumn()), "'").' = \'.$db->escape($'.$pk->getProperty().')';
		}
		
		$this->setParamList(implode(', ', $params));
		$this->setPhpDoc('Fetches a single object from the database based on its primary key.

'.implode("\n", $php_doc_params).'
@return '.$desc->getClass().'|false');
		
		$this->addPart('$db = '.$desc->getFetchAdapterCode().';
$c = new '.$desc->getCollectionClassName().';');
		
		$this->addPart('$c->filters[] = \''.implode('.\'', $filter).';');
		
		$this->addPart('$c->populate();');
		
		$this->addPart('return current($c->getContentReference());');
	}
}


/* End of file ByPrimaryKey.php */
/* Location: ./lib/Rdm/Builder/Collection/Fetch */