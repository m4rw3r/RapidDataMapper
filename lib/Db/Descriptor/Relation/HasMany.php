<?php
/*
 * Created by Martin Wernståhl on 2009-08-10.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains specific logic for the Has Many relationship type.
 */
class Db_Descriptor_Relation_HasMany implements Db_Descriptor_RelationInterface
{
	/**
	 * A list of properties which will be used to restrict the related records.
	 * 
	 * @var array
	 */
	protected $extra_conds = array();
	
	/**
	 * Contains the foreign key mappings.
	 * 
	 * @var array
	 */
	protected $foreign_keys = array();
	
	/**
	 * Contains the relation object.
	 * 
	 * @var Db_Descriptor_Relation
	 */
	protected $relation;
	
	function __construct(Db_Descriptor_Relation $rel)
	{
		$this->relation = $rel;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the foreign key mappings to use by this relation.
	 * 
	 * @param  array|string
	 * @param  array|string
	 * @return Db_Descriptor_Relation	The parent relation
	 */
	public function setForeignKeys($local_key, $foreign_key = false)
	{
		if(is_array($local_key) && ! is_numeric(key($local_key)))
		{
			$this->foreign_keys = $local_key;
		}
		elseif(is_array($local_key) && is_array($foreign_key) && is_numeric(key($local_key)) && is_numeric(key($foreign_key)))
		{
			$this->foreign_keys = array_combine($local_key, $foreign_key);
		}
		elseif(is_string($local_key) && is_string($foreign_key))
		{
			$this->foreign_keys[$local_key] = $foreign_key;
		}
		else
		{
			throw new UnexpectedArgumentException(gettype($foreign_key));
		}
		
		return $this->relation;
	}
	
	// ------------------------------------------------------------------------
	
	public function getJoinRelatedCode($query_obj_var, $alias_of_linked_var)
	{
		# code...
	}
	
	// ------------------------------------------------------------------------
	
	public function setExtraConditions($property_name, $value = null)
	{
		// TODO: More validation
		if(is_array($property_name))
		{
			$this->extra_conds = array_merge($extra_conds, $property_name);
		}
		else
		{
			$this->extra_conds[$property_name] = $value;
		}
		
		return $this->relation;
	}
	
	// ------------------------------------------------------------------------
	
	public function getSaveInsertRelationCode($object_var)
	{
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		$str = 'if( ! empty('.$object_var.'->'.$this->relation->getName().'))
{
	// prevent PHP from casting objects to arrays
	if(is_object($object->'.$this->relation->getName().'))
	{
		$object->'.$this->relation->getName().' = array($object->'.$this->relation->getName().');
	}
	
	// loop all the related objects
	foreach($object->'.$this->relation->getName().' as $key => $related)
	{
		// check so they are of the correct type
		if($related instanceof '.$related->getClass().')
		{
			';
		
		// assign properties for children
		$arr = array('// set the propert'.(count($foreign_keys) > 1 ? 'ies' : 'y'));
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$arr[] = '$related->'.$fprop->getProperty().' = $object->'.$lprop->getProperty().';';
		}
		$str .= implode("\n\t\t\t", $arr);
		
		$str .= '
			
			Db::save($related);
		}
		else
		{
			// unrelated, remove
			unset($object->'.$this->relation->getName().'[$key]);
		}
	}
	
	// save a reference, so we can compare on later updates
	$object->__loaded_rels['.$this->relation->getName().'] = $object->'.$this->relation->getName().';
}';
		
		return $str;
	}
	
	// ------------------------------------------------------------------------
	
	public function getPreSaveRelationCode($object_var)
	{
		return '';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of the keys to use when linking the objects together.
	 * 
	 * Return example:
	 * <code>
	 * array(
	 *     array(local_column objects),
	 *     array(foreign_column objects)
	 *      )
	 * </code>
	 * 
	 * @return array
	 */
	protected function getKeys()
	{
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		if(empty($this->foreign_keys))
		{
			$this->foreign_keys = $this->relation->guessForeignKeyMappings($local);
		}
		
		$local_keys = $this->relation->getKeyObjects(array_keys($this->foreign_keys), $local);
		$related_keys = $this->relation->getKeyObjects(array_values($this->foreign_keys), $related);
		
		return array(array_values($local_keys), array_values($related_keys));
	}
}


/* End of file HasMany.php */
/* Location: ./lib/Db/Descriptor/Relation */