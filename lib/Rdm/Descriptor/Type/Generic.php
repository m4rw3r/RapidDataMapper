<?php
/*
 * Created by Martin Wernståhl on 2010-04-25.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Describes the basic data type which just passes PHP data directly.
 */
class Rdm_Descriptor_Type_Generic implements Rdm_Descriptor_TypeInterface
{
	protected $col;
	
	// ------------------------------------------------------------------------
	
	public function setColumn(Rdm_Descriptor_Column $col)
	{
		$this->col = $col;
	}
	
	// ------------------------------------------------------------------------
	
	public function getShemaDeclaration()
	{
		return false;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a fragment which selects the column properly, no need to alias
	 * the column as it is loaded by position.
	 * 
	 * @param  string  Code/variable for accessing a variable
	 * @param  string  The separator which starts and ends the PHP string
	 *                 into which this select code will be inserted
	 *                 (" or ')
	 * @return string  PHP code fragment which contains the SELECT part for
	 *                 the column using this datatype object.
	 *                 Can be inserted in a string using $string_separator
	 *                 for end of the string
	 */
	public function getSelectCode($table_variable, $string_separator)
	{
		$db = $this->col->getParentDescriptor()->getAdapter();
		
		// $table.'.
		$str1 = $table_variable.'.'.$string_separator.'.';
		// `foo`
		$str2 = $db->protectIdentifiers($this->col->getColumn());
		
		// Prevent faulty PHP code
		return $str1.addcslashes($str2, $string_separator);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a PHP fragment for generating the "column = value" SQL.
	 * 
	 * The code must be able to be placed between a "=" and a ";",
	 * and it is already in string context at the start.
	 * 
	 * @param  string  The php code which fetches the data from the object
	 * @param  string  The string separator, " or '
	 * @return string
	 */
	public function getSqlValueCode($source_code, $string_separator)
	{
		return $string_separator.'.$this->db->escape('.$this->getCastFromPhpCode($source_code).')';
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Returns the code which will convert the data into a PHP type, should be
	 * possible to put between = and ;.
	 * 
	 * @param  string  The code which fetches the data which comes from the db field
	 * @return string  Code which fetches the data from the db field and then
	 *                 converts it into a PHP type
	 */
	public function getCastToPhpCode($source)
	{
		return $source;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code which will convert the data into a database type, should
	 * be possible to put between = and ;.
	 * 
	 * @param  string  The code which fetches the data from the PHP object
	 * @return string  Code which fetches the data from the PHP object and
	 *                 converts it to the database representation
	 */
	public function getCastFromPhpCode($source)
	{
		return $source;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of classes which should be added to the CollectionFilter
	 * builder for this data type.
	 * 
	 * @return array(string)
	 */
	public function getCollectionFilterClasses()
	{
		// TODO: Add more filter method generators
		return array(
			'Rdm_Builder_CollectionFilter_LessThan',
			'Rdm_Builder_CollectionFilter_GreaterThan',
			'Rdm_Builder_CollectionFilter_Like',
			'Rdm_Builder_CollectionFilter_Equals'
			);
	}
}


/* End of file Generic.php */
/* Location: ./lib/Rdm/Descriptor/Type */