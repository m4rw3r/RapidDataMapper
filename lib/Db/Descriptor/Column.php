<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class describing a column mapped to a class.
 */
class Db_Descriptor_Column
{
	/**
	 * The name of the column this object describes.
	 * 
	 * @var string
	 */
	protected $column;
	
	/**
	 * The property name in the class the described column maps to.
	 * 
	 * @var string
	 */
	protected $property;
	
	/**
	 * The data type of the described column.
	 * 
	 * @var string
	 */
	protected $data_type;
	
	/**
	 * The (maximum) length of the described column.
	 * 
	 * @var int
	 */
	protected $data_length;
	
	/**
	 * The default data type for described columns.
	 * 
	 * @var string
	 */
	protected $data_type_default = 'varchar';
	
	/**
	 * The default length of the data type in described columns.
	 * 
	 * @var int
	 */
	protected $data_length_default = 255;
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the column name this object describes.
	 * 
	 * @throws Db_Exception_Descriptor_MissingColumnName
	 * @return string
	 */
	public function getColumn()
	{
		if(empty($this->column))
		{
			throw new Db_Exception_Descriptor_MissingColumnName();
		}
		
		return $this->column;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the column this object describes.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setColumn($col)
	{
		$this->column = $col;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the property name of the column this object describes.
	 * 
	 * @return string
	 */
	public function getProperty()
	{
		return empty($this->property) ? $this->column : $this->property;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the property this descriptor describes.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setProperty($prop)
	{
		$this->property = $prop;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the database column type of the described column.
	 * 
	 * If not set, this method uses the value of $this->data_type_default.
	 * 
	 * @return string
	 */
	public function getDataType()
	{
		return empty($this->data_type) ? $this->data_type_default : $this->data_type;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the database column type of the column this object describes.
	 * 
	 * @param  string
	 * @return self
	 */
	public function setDataType($type)
	{
		$this->data_type = $type;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the data length for the column this object describes.
	 * 
	 * If not set, this method uses the value of $this->data_length_default.
	 * 
	 * @return int
	 */
	public function getDataLength()
	{
		return empty($this->data_length) ? $this->data_length_default : $this->data_length;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the one-liner to convert the database value to a PHP value.
	 * 
	 * Source is a string referring to a PHP variable in the generated code.
	 * Ie. a string like "$foo" or "$obj->foo" (no quotes).
	 * 
	 * The result should be able to be placed between "=" and ";".
	 * 
	 * @param  string
	 * @return string
	 */
	public function getCastToPhpCode($source)
	{
		switch($this->getDataType())
		{
			case 'integer':
				return '(Int) '.$source;
			// TODO: Add more datatypes
			default:
				return $source;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the one-liner to convert the PHP value to a database value.
	 * 
	 * Source is a string referring to a PHP variable in the generated code.
	 * Ie. a string like "$foo" or "$obj->foo" (no quotes).
	 * 
	 * The result should be able to be placed between "=" and ";".
	 * 
	 * @param  string
	 * @return string
	 */
	public function getCastFromPhpCode($source)
	{
		switch($this->getDataType())
		{
			case 'integer':
				return '(Int) '.$source;
			// TODO: Add more datatypes
			default:
				return $source;
		}
	}
}

/* End of file Column.php */
/* Location: ./lib/Db/Descriptor */