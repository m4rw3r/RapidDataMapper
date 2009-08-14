<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains specific logic for the Has One relationship type.
 */
class Db_Descriptor_Relation_HasOne extends Db_Descriptor_Relation_HasMany
{
	public function getSaveInsertRelationCode($object_var)
	{
		// TODO: Maybe use some Db_Mapper_CodeContainer here?
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		// check if we have a correct related object
		$str = 'if(isset('.$object_var.'->'.$this->relation->getName().') && '.$object_var.' instanceof '.$related->getClass().')
{
	';
		
		// assign properties for children
		$arr = array('// set the propert'.(count($foreign_keys) > 1 ? 'ies' : 'y'));
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $foreign_keys[$i];
		
			$arr[] = '$related->'.$fprop->getProperty().' = '.$object_var.'->'.$lprop->getProperty().';';
		}
		$str .= implode("\n\t", $arr);
	
		$str .= '
		
	Db::save($related);
}';
		
		return $str;
	}
	
	// ------------------------------------------------------------------------
	
	public function getSaveUpdateRelationCode($object_var)
	{
		// TODO: Maybe use some Db_Mapper_CodeContainer here?
		$db = $this->relation->getParentDescriptor()->getDatabaseConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		$str = '//  relates To One '.$related->getClass()."\n";
		
		$str .= 'if(isset('.$object_var.'->'.$this->relation->getName().') && '.$object_var.' instanceof '.$related->getClass().')
{
	';
		
		$set = array();
		$where = array();
		$if_conds = array();
		$assignments = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $foreign_keys[$i];
			
			// null foreign columns on other related objects
			$set[] = $db->protectIdentifiers($fprop->getColumn()) . ' = NULL';
			
			// filter to only related objects
			$where[] = addcslashes($db->protectIdentifiers($fprop->getColumn()), "'").' = \' . $this->db->escape($object->'.$lprop->getProperty().')';
			
			$if_conds[] = $object_var.'->'.$this->relation->getName().'->'.$fprop->getProperty().' != $object->'.$lprop->getProperty();
			
			// assignments to set the related object as a child object
			$assignments[] = $object_var.'->'.$this->relation->getName().'->'.$fprop->getProperty().' = $object->'.$lprop->getProperty().';';
		}
		
		$sql = '\'UPDATE '.addcslashes($db->protectIdentifiers($related->getTable()), "'").' SET ' . addcslashes(implode(', ', $set), "'") . ' WHERE ' . implode('.\' AND ', $where);
		
		$str .= 'if(empty('.$object_var.'->'.$this->relation->getName().') OR '.implode(' OR ', $if_conds).')
	{
		$this->db->query('.$sql.');
	}
	
	'.implode("\n", $assignments).'
	
	Db::save('.$object_var.'->'.$this->relation->getName().');
}';
		
		return $str;
	}
}


/* End of file HasMany.php */
/* Location: ./lib/Db/Descriptor/Relation */