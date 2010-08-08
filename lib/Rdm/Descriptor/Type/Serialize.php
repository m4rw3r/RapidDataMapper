<?php
/*
 * Created by Martin Wernståhl on 2010-04-25.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Describes a type which serializes its contents into the database field.
 */
class Rdm_Descriptor_Type_Serialize extends Rdm_Descriptor_Type_Generic
{
	public function getSchemaDeclaration()
	{
		$db = $this->col->getParentDescriptor()->getAdapter();
		
		// TODO: user setting of type
		return $db->protectIdentifiers($this->col->getColumn()).' TEXT';
	}
	public function getSqlValueCode($source_code, $string_separator)
	{
		return $string_separator.'.$this->db->escape('.$this->getCastFromPhpCode($source_code).')';
	}
	public function getCastToPhpCode($source)
	{
		return 'unserialize('.$source.')';
	}
	public function getCastFromPhpCode($source)
	{
		return 'serialize('.$source.')';
	}
	public function getCollectionFilterClasses()
	{
		// Filters do not apply
		return array();
	}
}


/* End of file Serialize.php */
/* Location: ./lib/Rdm/Descriptor/Type */