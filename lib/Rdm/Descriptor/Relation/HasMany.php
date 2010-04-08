<?php
/*
 * Created by Martin Wernståhl on 2009-08-10.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains specific logic for the Has Many relationship type.
 */
class Rdm_Descriptor_Relation_HasMany
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
	 * @var Rdm_Descriptor_Relation
	 */
	protected $relation;
	
	function __construct(Rdm_Descriptor_Relation $rel)
	{
		$this->relation = $rel;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the foreign key mappings to use by this relation.
	 * 
	 * @param  array|string
	 * @param  array|string
	 * @return Rdm_Descriptor_Relation	The parent relation
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
	
	public function isPlural()
	{
		return true;
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
	public function getKeys()
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
/* Location: ./lib/Rdm/Descriptor/Relation */