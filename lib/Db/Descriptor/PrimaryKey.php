<?php
/*
 * Created by Martin Wernståhl on 2009-08-08.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * An object which describes a primary key.
 */
class Db_Descriptor_PrimaryKey extends Db_Descriptor_Column
{
	// Do not allow the primary key(s) to be updatable by default
	protected $updatable = false;
	
	/**
	 * Holds the type of Primary Key this is.
	 * 
	 * @var int
	 */
	protected $pk_type;
	
	/**
	 * The callable for the id
	 */
	protected $gen_callable;
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function getDataType()
	{
		if($this->pk_type == Db_Descriptor::AUTO_INCREMENT && stripos($this->data_type, 'int') === false)
		{
			// TODO: Warning about an int column needed for auto increment
			
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function getPkType()
	{
		return empty($this->pk_type) ? Db_Descriptor::AUTO_INCREMENT : $this->pk_type;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function setPkType($type, $param = false)
	{
		# code...
	}
	
	/**
	 * Special variant which also assigns to the "hidden" __id property.
	 */
	public function getFromDataToObjectCode($object_var, $data_var, $data_prefix_var)
	{
		return $object_var.'->'.$this->getProperty().' = '.$object_var.'->__id[\''.$this->getColumn().'\'] = '.$this->getCastToPhpCode($this->getFromDataCode($data_var, $data_prefix_var)).';';
	}
}


/* End of file PrimaryKey.php */
/* Location: ./lib/Db/Descriptor */