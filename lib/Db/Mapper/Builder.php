<?php
/*
 * Created by Martin Wernståhl on 2009-08-08.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Db_Mapper_Builder extends Db_Mapper_CodeContainer
{
	/**
	 * The class descriptor.
	 * 
	 * @var Db_Descriptor
	 */
	protected $descriptor;
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct(Db_Descriptor $desc)
	{
		$this->descriptor = $desc;
		
		$this->addPart(new Db_Mapper_Code_Property('class', $desc->getClass()));
		
		$this->addProperties();
		
		$this->addMethods();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the required properties to the mapper.
	 * 
	 * @return void
	 */
	protected function addProperties()
	{
		// create a list of the relations
		$rel_arr = array();
		foreach($this->descriptor->getRelations() as $rel)
		{
			$rel_arr[$rel->getName()] = $rel->getRelatedClass();
		}
		$this->addPart(new Db_Mapper_Code_Property('relations', $rel_arr));
		
		// create a list of the properties
		$prop_arr = array();
		foreach($this->descriptor->getColumns() as $prop)
		{
			$prop_arr[$prop->getProperty()] = $prop->getColumn();
		}
		$this->addPart(new Db_Mapper_Code_Property('properties', $prop_arr));
		
		// create a list of the primary keys
		$pk_arr = array();
		foreach($this->descriptor->getPrimaryKeys() as $prop)
		{
			$pk_arr[$prop->getProperty()] = $prop->getColumn();
		}
		$this->addPart(new Db_Mapper_Code_Property('primary_keys', $pk_arr));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the methods to the generated class.
	 * 
	 * @return void
	 */
	public function addMethods()
	{
		$this->addPart(new Db_Mapper_Part_PopulateFindQuery($this->descriptor));
		
		$this->addPart(new Db_Mapper_Part_Objectify($this->descriptor));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'Db_Compiled_'.$this->descriptor->getClass().'Mapper';
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$str = 'class Db_Compiled_'.$this->descriptor->getClass().'Mapper extends Db_Mapper';
		
		return $str."\n{".self::indentCode("\n".implode("\n\n", $this->content))."\n}";
	}
}


/* End of file Builder.php */
/* Location: ./lib/Db/Mapper */