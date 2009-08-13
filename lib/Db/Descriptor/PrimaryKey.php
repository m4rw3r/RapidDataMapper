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
	
	// let the default type be unsigned int
	protected $data_type_default = 'unsigned int';
	
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
		if($this->getPkType() == Db_Descriptor::AUTO_INCREMENT && stripos(strtolower($this->data_type), 'int') === false)
		{
			// TODO: Warning about an int column needed for auto increment
			return 'unsigned int';
		}
		else
		{
			return parent::getDataType();
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the primary key type.
	 * 
	 * @return int
	 */
	public function getPkType()
	{
		return empty($this->pk_type) ? Db_Descriptor::AUTO_INCREMENT : $this->pk_type;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the primary key type.
	 * 
	 * @return 
	 */
	public function setPkType($type, $param = false)
	{
		// TODO: Filter input
		$this->pk_type = $type;
		
		if($type == Db_Descriptor::CALL)
		{
			// TODO: Validate callable
			$this->gen_callable = $param;
		}
		
		return $this;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Special variant which also assigns to the "hidden" __id property.
	 */
	public function getFromDataToObjectCode($object_var, $data_var, $data_prefix_var)
	{
		return $object_var.'->'.$this->getProperty().' = '.$object_var.'->__id[\''.$this->getColumn().'\'] = '.$this->getCastToPhpCode($this->getFromDataCode($data_var, $data_prefix_var)).';';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Special variant which skips it if this is an auto incremental column.
	 */
	public function getFromObjectToDataCode($object_var, $dest_var)
	{
		return $this->getPkType() == Db_Descriptor::AUTO_INCREMENT ? '' : parent::getFromObjectToDataCode($object_var, $dest_var);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code which inserts the primary keys in the data variable before the insert is performed.
	 * 
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function getInsertPopulateColumnCode($data_var, $object_var)
	{
		switch($this->getPkType())
		{
			case Db_Descriptor::MANUAL:
				// Make sure that the column is set
				return 'if( ! isset('.$data_var.'[\''.$this->getColumn().'\']))
{
	throw new Db_Exception(\'Missing value in primary key property \'.get_class('.$object_var.').\'::'.$this->getProperty().'\');
}';
				break;
				
			default:
				return parent::getInsertPopulateColumnCode($data_var, $object_var);
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the auto increment value if auto increment is used.
	 * 
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function getInsertReadColumnCode($data_var, $object_var)
	{
		if($this->getPkType() == Db_Descriptor::AUTO_INCREMENT)
		{
			return $object_var.'->'.$this->getProperty().' = '.$data_var.'[\''.$this->getColumn().'\'] = $object->__id[\''.$this->getColumn().'\'] = $this->db->insert_id();';
		}
		elseif($this->getPkType() == Db_Descriptor::MANUAL)
		{
			return $object_var.'->'.$this->getProperty().' = '.$object_var.'->__id[\''.$this->getColumn().'\'] = '.$data_var.'[\''.$this->getColumn().'\'];';
		}
	}
}


/* End of file PrimaryKey.php */
/* Location: ./lib/Db/Descriptor */