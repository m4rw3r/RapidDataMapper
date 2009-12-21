<?php
/*
 * Created by Martin Wernståhl on 2009-11-13.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the properties of a Db_MapperQuery descendant.
 */
class Db_MapperQuery_Part_Properties extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $desc)
	{
		$db = $desc->getConnection();
		
		$this->addPart(new Db_CodeBuilder_Property('main_object', $desc->getSingular()));
		
		$this->addPart(new Db_CodeBuilder_Property('columns', array($desc->getSelectCode($desc->getSingular(), $desc->getSingular()))));
		
		$this->addPart(new Db_CodeBuilder_Property('from', array($db->protectIdentifiers($db->dbprefix.$desc->getTable()).' AS '.$db->protectIdentifiers($desc->getSingular()))));
		
		// get the column names which the PHP variants should be translated into to create correct SQL
		$php_names = array();
		$sql_names = array();
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $col)
		{
			$p = $col->getLocalColumn($desc->getSingular());
			$s = $col->getSourceColumn($desc->getSingular());
			
			// smaller optimization, makes for fewer replaces
			if(strtolower($p) != strtolower($s))
			{
				$php_names[] = $p;
				$sql_names[] = $s;
			}
		}
		
		$this->addPart(new Db_CodeBuilder_Property('php_columns', $php_names));
		
		$this->addPart(new Db_CodeBuilder_Property('sql_columns', $sql_names));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'properties';
	}
}


/* End of file Properties.php */
/* Location: ./lib/Db/MapperQuery/Part */