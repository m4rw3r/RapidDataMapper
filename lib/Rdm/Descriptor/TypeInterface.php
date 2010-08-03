<?php
/*
 * Created by Martin Wernståhl on 2010-08-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Interface for type declaring classes.
 */
interface Rdm_Descriptor_TypeInterface
{
	/**
	 * Sets the column object which this type object will generate code for.
	 * 
	 * NOTE:
	 * This method will be called each time the type object is invoked,
	 * so it is recommended to just store the $col object inside a property
	 * to be able to use it in the other methods.
	 * 
	 * @param  Rdm_Descriptor_Column
	 * @return void
	 */
	public function setColumn(Rdm_Descriptor_Column $col);
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the datatype length of this data type.
	 * 
	 * @param  int|false  False equals default
	 * @return void
	 */
	public function setLength($value);
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the schema declaration for this type, including column name,
	 * length and assorted flags.
	 * 
	 * Using the object set using setColumn() to get the name etc.
	 * 
	 * @return string|false  The SQL row declaring the parent column set by
	 *                       setColumn(), false if no code is specified
	 */
	public function getShemaDeclaration();
	
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
	public function getSelectCode($table_variable, $string_separator);
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a PHP fragment for generating the value part of "column = value" SQL.
	 * 
	 * The code must be able to be placed between a "=" and a ";",
	 * and it is already in string context at the start and should end with a
	 * closing $string_separator or a non-string part.
	 * 
	 * @param  string  The php code which fetches the data from the object
	 * @param  string  The string separator, " or '
	 * @return string
	 */
	public function getSqlValueCode($source_code, $string_separator);
	
	// ------------------------------------------------------------------------
	
	/**
	 * Returns the code which will convert the data into a PHP type, should be
	 * possible to put between = and ;.
	 * 
	 * @param  string  The code which fetches the data which comes from the db field
	 * @return string  Code which fetches the data from the db field and then
	 *                 converts it into a PHP type
	 */
	public function getCastToPhpCode($source);
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code which will convert the data into a database type, should
	 * be possible to put between = and ;.
	 * 
	 * @param  string  The code which fetches the data from the PHP object
	 * @return string  Code which fetches the data from the PHP object and
	 *                 converts it to the database representation
	 */
	public function getCastFromPhpCode($source);
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of classes which should be added to the CollectionFilter
	 * builder for this data type.
	 * 
	 * @return array(string)
	 */
	public function getCollectionFilterClasses();
}

/* End of file TypeInterface.php */
/* Location: ./lib/Rdm/Descriptor */