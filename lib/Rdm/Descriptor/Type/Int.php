<?php
/*
 * Created by Martin Wernståhl on 2010-04-25.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Describes the integer data type.
 */
class Rdm_Descriptor_Type_Int extends Rdm_Descriptor_Type_Generic
{
	protected $length = 10;
	
	protected $allow_null = true;
	
	public function __construct($length = 10, $allow_null = true)
	{
		$this->length = $length;
		$this->allow_null = $allow_null;
	}
	public function getSchemaDeclaration()
	{
		$db = $this->col->getParentDescriptor()->getAdapter();
		
		// TODO: default value
		if($this->allow_null)
		{
			$default = '';
		}
		else
		{
			$default = ' NOT NULL';
		}
		
		return $db->protectIdentifiers($this->col->getColumn()).' INT('.$this->length.')'.$default;
	}
	/**
	 * No need to escape an INT.
	 */
	public function getSqlValueCode($source_code, $string_separator)
	{
		return $string_separator.'.'.$this->getCastFromPhpCode($source_code);
	}
	public function getCastToPhpCode($source)
	{
		if($this->allow_null)
		{
			return '(is_null('.$source.') ? null : (Int)'.$source.')';
		}
		else
		{
			return '(Int)'.$source;
		}
	}
	public function getCastFromPhpCode($source)
	{
		if($this->allow_null)
		{
			return '(is_null('.$source.') ? \'NULL\' : (Int)'.$source.')';
		}
		else
		{
			return '(Int)'.$source;
		}
	}
	public function getCollectionFilterClasses()
	{
		// TODO: Add more filter method generators
		return array(
			'Rdm_Builder_CollectionFilter_LessThan',
			'Rdm_Builder_CollectionFilter_GreaterThan',
			'Rdm_Builder_CollectionFilter_Equals'
			);
	}
}


/* End of file Int.php */
/* Location: ./lib/Rdm/Descriptor/Type */