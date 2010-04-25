<?php
/*
 * Created by Martin Wernståhl on 2010-04-25.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Describes the basic data type which just passes PHP data directly.
 */
class Rdm_Descriptor_Type_Generic
{
	protected $col;
	
	protected $db;
	
	// ------------------------------------------------------------------------
	
	public function __construct(Rdm_Descriptor_Column $col, Rdm_Adapter $db)
	{
		$this->col = $col;
		$this->db  = $db;
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
			'Rdm_Builder_CollectionFilter_GreatherThan',
			'Rdm_Builder_CollectionFilter_Like',
			'Rdm_Builder_CollectionFilter_Equals'
			);
	}
}


/* End of file Generic.php */
/* Location: ./lib/Rdm/Descriptor/Type */