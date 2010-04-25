<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_CollectionFilter_Like extends Rdm_Util_Code_MethodBuilder
{
	function __construct(Rdm_Descriptor_Column $column, Rdm_Descriptor $desc)
	{
		$property = $column->getProperty();
		$db       = $desc->getAdapter();
		
		$this->setMethodName($property.'Like');
		$this->setParamList("$$property");
		
		$col = addcslashes($db->protectIdentifiers($column->getColumn()), "'");
		
		$this->addPart("empty(\$this->filters) OR \$this->filters[] = 'AND';
\$this->filters[] = \$this->table_alias.'.$col LIKE '.\$this->db->escape(\$$property, true);
\$this->is_dynamic = true;");
		
		// TODO: Validation for type
		// TODO: Set data so we can repopulate objects inserted into the collection using Rdm_Collection->add()
		
		$this->addPart('return $this;');
	}
}


/* End of file Like.php */
/* Location: ./lib/Rdm/Builder/CollectionFilter */