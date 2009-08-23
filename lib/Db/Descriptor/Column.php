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
	
	/**
	 * Tells if this column can be inserted.
	 * 
	 * @var bool
	 */
	protected $insrtable = true;
	
	/**
	 * Tells if this column can be updated.
	 * 
	 * @var bool
	 */
	protected $updatable = true;
	
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
		return empty($this->property) ? $this->getColumn() : $this->property;
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
	 * Returns true if this column can be used in an INSERT query.
	 * 
	 * @return bool
	 */
	public function isInsertable()
	{
		return $this->updatable;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets if this column can be used in an INSERT query.
	 * 
	 * @param  bool
	 * @return self
	 */
	public function setInsertable($value)
	{
		$this->insertable = $value;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if this column can be used in an UPDATE query.
	 * 
	 * @return bool
	 */
	public function isUpdatable()
	{
		return $this->updatable;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets if this column can be used in an UPDATE query.
	 * 
	 * @param  bool
	 * @return self
	 */
	public function setUpdatable($value)
	{
		$this->updatable = $value;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a fragment which selects the column and aliases it properly.
	 * 
	 * @param  string
	 * @param  string
	 * @param  Db_Connection
	 * @return string
	 */
	public function getSelectCode($table, $alias, Db_Connection $db)
	{
		return $db->protectIdentifiers($table.'.'.$this->getColumn()).' AS '.$db->protectIdentifiers($alias.'_'.$this->getProperty());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a piece of code which fetches the data from a result row and inserts it
	 * into an instance of the described object.
	 * 
	 * Usually the data always exists on the result object.
	 * 
	 * Example of generated code:
	 * <code>
	 * // params: $object_var = '$obj', $data_var = '$row', $data_prefix_var = '$alias'
	 * $obj->id = (Int) $row->{$alias.'__id'};
	 * </code>
	 * 
	 * @param  string	The name of the variable holding an instance of the described object.
	 * @param  string	The name of the variable holding an instance of StdClass, containing the row data.
	 * @param  string	The name of the variable holding a prefix for the column name
	 * @return string
	 */
	public function getFromDataToObjectCode($object_var, $data_var, $data_prefix_var)
	{
		return $object_var.'->'.$this->getProperty().' = '.$this->getCastToPhpCode($this->getFromDataCode($data_var, $data_prefix_var)).';';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a short piece of a statement which references the column value in the $data_var.
	 * 
	 * Example of generated code:
	 * <code>
	 * // params: $data_var = '$row', $data_prefix_var = '$alias'
	 * $row->{$alias.'__id'}
	 * </code>
	 * 
	 * @param  string	The name of the variable holding an instance of StdClass, containing the row data.
	 * @param  string	The name of the variable holding a prefix for the column name
	 * @return string
	 */
	public function getFromDataCode($data_var, $data_prefix_var)
	{
		return $data_var.'->{'.$data_prefix_var.'.\'_'.$this->getProperty().'\'}';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a piece of code which fetches the data from an instance of the described
	 * object and inserts it into an array, with the column name as key.
	 * 
	 * Only assign data if the value exists on the described object, otherwise
	 * do not assign anything to the $dest_var.
	 * 
	 * Example of generated code:
	 * <code>
	 * // params: $object_var = '$obj', $dest_var = '$data'
	 * isset($obj->id) && $data['PK_id'] = (Int) $obj->id;
	 * </code>
	 * 
	 * @param  string	The name of the variable holding an instance of the described object.
	 * @param  string	The name of the variable holding an associative array to assign the data to.
	 * @param  bool		If it is an update which the code is fetching data for
	 * @return string
	 */
	public function getFromObjectToDataCode($object_var, $dest_var, $is_update = false)
	{
		// only assign the columns which are allowed to be updated
		if(( ! $is_update && $this->isInsertable()) OR $is_update && $this->isUpdatable())
		{
			return 'isset('.$object_var.'->'.$this->getProperty().') && '.$dest_var.'[\''.$this->getColumn().'\'] = '.$this->getCastFromPhpCode($this->getFromObjectCode($object_var)).';';
		}
		else
		{
			return '';
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a short piece of a statement which references the property value in $object_var.
	 * 
	 * Example of generated code:
	 * <code>
	 * // params: $object_var = '$obj'
	 * $obj->foo
	 * </code>
	 * 
	 * @param  string
	 * @return string
	 */
	public function getFromObjectCode($object_var)
	{
		return $object_var.'->'.$this->getProperty();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code which does assignments to columns before insert and/or performs validation.
	 * 
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function getInsertPopulateColumnCode($data_var, $object_var)
	{
		// TODO: Add an option to generate columns with a callable (eg. dates)
		return '';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code which does assignments to columns after insert.
	 * 
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public function getInsertReadColumnCode($data_var, $object_var)
	{
		// TODO: Add the generated option
		return '';
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
		// TODO: Add datatype objects to allow easier additions?
		
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