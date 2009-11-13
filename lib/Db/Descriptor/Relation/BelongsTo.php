<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains specific logic for the Belongs To relationship type.
 */
class Db_Descriptor_Relation_BelongsTo extends Db_Descriptor_Relation_HasOne
{
	// ------------------------------------------------------------------------
	
	public function getPreSaveRelationCode($object_var)
	{
		// TODO: Maybe use some Db_CodeBuilder_Container here?
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		$str = 'if(isset('.$object_var.'->'.$this->relation->getProperty().') && '.$object_var.'->'.$this->relation->getProperty().' instanceof '.$related->getClass().')
{
	// we have a related parent, try to save it
	// to assure that we have an ok primary key
	Db::save('.$object_var.'->'.$this->relation->getProperty().');
	
	';
		
		// establish relation
		$arr = array('// set the propert' . (count($local_keys) > 1 ? 'ies' : 'y'));
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$arr[] = $object_var.'->'.$lprop->getProperty().' = '.$object_var.'->'.$this->relation->getProperty().'->'.$fprop->getProperty().';';
		}
		
		$str .= implode("\n\t", $arr);
		
		$str .= '
}';
		
		return $str;
	}
	
	// ------------------------------------------------------------------------
	
	public function getSaveInsertRelationCode($object_var)
	{
		return '';
	}
	
	// ------------------------------------------------------------------------
	
	public function getSaveUpdateRelationCode($object_var)
	{
		return '';
	}
	
	// ------------------------------------------------------------------------
	
	protected function getKeys()
	{
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		if(empty($this->foreign_keys))
		{
			// create the links from the related table to this table, then flip them so they go the other way
			// ie. foreign key -> primary key
			$this->foreign_keys = array_flip($this->relation->guessForeignKeyMappings($related));
		}
		
		$local_keys = $this->relation->getKeyObjects(array_keys($this->foreign_keys), $local);
		$related_keys = $this->relation->getKeyObjects(array_values($this->foreign_keys), $related);
		
		return array(array_values($local_keys), array_values($related_keys));
	}
}


/* End of file BelongsTo.php */
/* Location: ./lib/Db/Descriptor/Relation */