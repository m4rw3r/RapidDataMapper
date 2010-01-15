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
		
		$this->addMethods($desc);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the methods to the generated class.
	 * 
	 * @return void
	 */
	public function addMethods(Db_Descriptor $descriptor)
	{
		$this->addPart(new Db_Mapper_Part_Properties($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Constructor($descriptor));
		
		$this->addPart(new Db_Mapper_Part_CreateMapperQuery($descriptor));
		
		$this->addPart(new Db_Mapper_Part_JoinRelated($descriptor));
		
		$this->addPart(new Db_Mapper_Part_ApplyRelatedConditions($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Objectify($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Save($descriptor));
		
		$this->addPart(new Db_Mapper_Part_Delete($descriptor));
		
		$this->addPart(new Db_Mapper_Part_AllowDelete($descriptor));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'mapper';
	}
}


/* End of file Builder.php */
/* Location: ./lib/Db/Mapper */