<?php
/*
 * Created by Martin Wernståhl on 2009-12-18.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains the code generation for the properties of a Db_Mapper descendant.
 */
class Db_Mapper_Part_Properties extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $descriptor)
	{
		// create a list of the relations
		$rel_arr = array();
		foreach($descriptor->getRelations() as $rel)
		{
			$rel_arr[$rel->getName()] = $rel->getRelatedClass();
		}
		$this->addPart(new Db_CodeBuilder_Property('relations', $rel_arr));
		
		// create a list of the properties
		$prop_arr = array();
		foreach($descriptor->getColumns() as $prop)
		{
			$prop_arr[$prop->getProperty()] = $prop->getColumn();
		}
		$this->addPart(new Db_CodeBuilder_Property('properties', $prop_arr));
		
		// create a list of the primary keys
		$pk_arr = array();
		foreach($descriptor->getPrimaryKeys() as $prop)
		{
			$pk_arr[$prop->getProperty()] = $prop->getColumn();
		}
		$this->addPart(new Db_CodeBuilder_Property('primary_keys', $pk_arr));
		
		// create the default count string
		$db = $descriptor->getConnection();
		$count_str = array();
		foreach($descriptor->getPrimaryKeys() as $pk)
		{
			$count_str[] = $db->protectIdentifiers($descriptor->getSingular().'.'.$pk->getColumn());
		}
		$this->addPart(new Db_CodeBuilder_Property('count_str', 'distinct '.implode(' + \'\' + ', $count_str)));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'properties';
	}
}


/* End of file Properties.php */
/* Location: ./lib/Db/Mapper/Part */