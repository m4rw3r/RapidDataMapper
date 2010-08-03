<?php
/*
 * Created by Martin Wernståhl on 2009-08-08.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * An object which describes a primary key.
 */
class Rdm_Descriptor_PrimaryKey extends Rdm_Descriptor_Column
{
	// Do not allow the primary key(s) to be updatable by default
	protected $updatable = false;
	
	/**
	 * Holds the type of Primary Key this is.
	 * 
	 * @var int
	 */
	protected $pk_type = Rdm_Descriptor::AUTO_INCREMENT;
	
	/**
	 * The callable for the id
	 */
	protected $gen_callable;
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new PrimaryKey object.
	 * 
	 * @param  string  The column name
	 * @param  string|Rdm_Descriptor_TypeInterface The data type, object or
	 *                                             Rdm_Descriptor constant
	 * @param  int|false  The data type length
	 * @param  string  The property name of this column, defaults to $name
	 * @param  int     The primary key type, constant from Rdm_Descriptor
	 * @return Rdm_Column
	 */
	public function __construct($name,
	                            $type = Rdm_Descriptor::GENERIC,
	                            $type_length = false,
	                            $property = false,
	                            $primary_key_type = Rdm_Descriptor::AUTO_INCREMENT)
	{
		// TODO: Filter input
		$this->column = $name;
		$this->data_type = empty($type) ? $type : Rdm_Descriptor::INT;
		$this->data_type_length = $type_length;
		$this->property = $property ? $property : $name;
		$this->pk_type = $primary_key_type;
	}
	
	// ------------------------------------------------------------------------
	
	public function isInsertable()
	{
		return $this->getPkType() == Rdm_Descriptor::AUTO_INCREMENT ? false : true;
	}
	
	// ------------------------------------------------------------------------
	
	public function getDataType()
	{
		if($this->getPkType() == Rdm_Descriptor::AUTO_INCREMENT && stripos(strtolower(is_object($this->data_type) ? $this->data_type->getSchemaDeclaration() : $this->data_type), 'int') === false)
		{
			$this->data_type = Rdm_Descriptor::INT;
		}
		
		return parent::getDataType();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the primary key type.
	 * 
	 * @return int
	 */
	public function getPkType()
	{
		return $this->pk_type;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the primary key type.
	 * 
	 * @param  int		From a Rdm_Descriptor constant
	 * @param  mixed	Parameter(s) to the key type
	 * @return self
	 */
	public function setPkType($type, $param = false)
	{
		// TODO: Filter input
		$this->pk_type = $type;
		
		if($type == Rdm_Descriptor::CALL)
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
		return $this->getAssignToObjectCode($object_var, $object_var.'->__id[\''.$this->getColumn().'\'] = '.$this->getCastToPhpCode($this->getFromDataCode($data_var, $data_prefix_var))).';';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Special variant which skips it if this is an auto incremental column.
	 */
	public function getFromObjectToDataCode($object_var, $dest_var, $is_update = false)
	{
		return $this->getPkType() == Rdm_Descriptor::AUTO_INCREMENT ? '' : parent::getFromObjectToDataCode($object_var, $dest_var, $is_update);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code which inserts the primary keys in the data variable before the insert is performed.
	 * 
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function getPreInsertCode($data_var, $object_var)
	{
		switch($this->getPkType())
		{
			case Rdm_Descriptor::MANUAL:
				// Make sure that the column is set
				return 'if( ! isset('.$data_var.'[\''.$this->getColumn().'\']))
{
	throw new Rdm_Exception(\'Missing value in primary key property \'.get_class('.$object_var.').\'::'.$this->getProperty().'\');
}';
				break;
				
			default:
				return parent::getPreInsertCode($data_var, $object_var);
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
	public function getPostInsertCode($object_var)
	{
		if($this->getPkType() == Rdm_Descriptor::AUTO_INCREMENT)
		{
			return $this->getAssignToObjectCode($object_var, $object_var.'->__id[\''.$this->getColumn().'\'] = $this->db->insertId()');
		}
		elseif($this->getPkType() == Rdm_Descriptor::MANUAL)
		{
			return $object_var.'->__id[\''.$this->getColumn().'\'] = '.$this->getFetchFromObjectCode($object_var);
		}
	}
}


/* End of file PrimaryKey.php */
/* Location: ./lib/Db/Descriptor */