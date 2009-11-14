<?php
/*
 * Created by Martin Wernståhl on 2009-08-08.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * The main class builder for Db_Mapper descendants.
 */
class Db_Mapper_Builder extends Db_CodeBuilder_Class
{
	/**
	 * Starts the initialization of the structure for the whole composite.
	 * 
	 * @param Db_Descriptor
	 */
	public function __construct(Db_Descriptor $desc)
	{
		$this->name = 'Db_Compiled_'.$desc->getClass().'Mapper';
		$this->extends = 'Db_Mapper';
		
		$this->addPart(new Db_CodeBuilder_Property('class', $desc->getClass()));
		
		$this->addProperties($desc);
		
		$this->addMethods($desc);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the required properties to the mapper.
	 * 
	 * @return void
	 */
	protected function addProperties(Db_Descriptor $descriptor)
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
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the methods to the generated class.
	 * 
	 * @return void
	 */
	public function addMethods(Db_Descriptor $descriptor)
	{
		$this->addPart(new Db_Mapper_Part_Constructor($descriptor));
		
		$this->addPart(new Db_Mapper_Part_CreateMapperQuery($descriptor));
		
		$this->addPart(new Db_Mapper_Part_JoinRelated($descriptor));
		
		$this->addPart(new Db_Mapper_Part_ApplyRelatedConditions($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Objectify($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Save($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Delete($descriptor));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'mapper';
	}
}


/* End of file Builder.php */
/* Location: ./lib/Db/Mapper */