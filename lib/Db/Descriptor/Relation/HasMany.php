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
	
	public function getJoinRelatedCode($query_obj_var, $alias_of_linked_var)
	{
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		// build foreign key conditions
		$cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$cols[] = $db->protectIdentifiers($alias_of_linked_var.'.'.$lprop->getColumn()).' = '.
				$db->protectIdentifiers($alias_of_linked_var.'-'.$this->relation->getName().'.'.$fprop->getColumn());
		}
		
		// add extra conditions
		foreach($this->extra_conds as $k => $v)
		{
			$cols[] = $db->protectIdentifiers($alias_of_linked_var.'-'.$this->relation->getName().'.'.$k).' = '.$db->escape($v);
		}
		
		// build column list
		$columns = $related->getSelectCode($alias_of_linked_var.'-'.$this->relation->getName(), $alias_of_linked_var.'-'.$this->relation->getName());
		
		// select
		$columns = $query_obj_var.'->columns[] = "'.addcslashes($columns, '"').'";';
		
		return $query_obj_var.'->join[] = "LEFT JOIN ' . 
			addcslashes($db->protectIdentifiers($db->dbprefix . $related->getTable()), '"') . 
			' AS ' . addcslashes($db->protectIdentifiers($alias_of_linked_var.'-' . $this->relation->getName()), '"') . '
	ON ' . addcslashes(implode(' AND ', $cols), '"')."\";\n".$columns;
	}
	
	// ------------------------------------------------------------------------
	
	public function getApplyRelatedConditionsCode($query_obj_var, $object_var)
	{
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		// build foreign key conditions
		$cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$cols[] = addcslashes($db->protectIdentifiers($related->getSingular().'.'.$fprop->getColumn()), "'").' = \'.$this->db->escape('.$object_var.'->'.$lprop->getProperty().')';
		}
		
		return $query_obj_var.'->where_prefix = \'' . 
			implode('.\'', $cols).'.\' AND (\';
'.$query_obj_var.'->where_suffix = \')\';';
	}
	
	// ------------------------------------------------------------------------
	
	public function getPreSaveRelationCode($object_var)
	{
		return '';
	}
	
	// ------------------------------------------------------------------------
	
	public function getSaveInsertRelationCode($object_var)
	{
		// TODO: Maybe use some Db_Mapper_CodeContainer here?
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		$str = '// The Has Many relation '.$this->relation->getName().', relates to '.$related->getClass().'
if( ! empty('.$object_var.'->'.$this->relation->getProperty().'))
{
	// prevent PHP from casting objects to arrays
	if(is_object('.$object_var.'->'.$this->relation->getProperty().'))
	{
		'.$object_var.'->'.$this->relation->getProperty().' = array('.$object_var.'->'.$this->relation->getProperty().');
	}
	
	// loop all the related objects
	foreach('.$object_var.'->'.$this->relation->getProperty().' as $key => $related)
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
			
			$arr[] = '$related->'.$fprop->getProperty().' = '.$object_var.'->'.$lprop->getProperty().';';
		}
		$str .= implode("\n\t\t\t", $arr);
		
		$str .= '
			
			Db::save($related);
		}
		else
		{
			// unrelated, remove
			unset('.$object_var.'->'.$this->relation->getProperty().'[$key]);
		}
	}
	
	// save a reference, so we can compare on later updates
	'.$object_var.'->__loaded_rels[\''.$this->relation->getProperty().'\'] = '.$object_var.'->'.$this->relation->getProperty().';
}';
		
		return $str;
	}
	
	// ------------------------------------------------------------------------
	
	public function getSaveUpdateRelationCode($object_var)
	{
		// TODO: Maybe use some Db_Mapper_CodeContainer here?
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $foreign_keys) = $this->getKeys();
		
		$str = '// The Has Many relation '.$this->relation->getName().', relates to '.$related->getClass()."\n";
		
		$str .= 'if(isset('.$object_var.'->'.$this->relation->getProperty().'))
{
	if(is_object('.$object_var.'->'.$this->relation->getProperty().'))
	{
		'.$object_var.'->'.$this->relation->getProperty().' = array('.$object_var.'->'.$this->relation->getProperty().');
	}
	
	// set comparable to default
	if( ! isset('.$object_var.'->__loaded_rels[\''.$this->relation->getProperty().'\']))
	{
		'.$object_var.'->__loaded_rels[\''.$this->relation->getProperty().'\'] = array();
	}
	
	// calculate what to remove (ie. check if the user has unset() anything)
	$to_del = Db_Util::array_odiff('.$object_var.'->__loaded_rels[\''.$this->relation->getProperty().'\'], '.$object_var.'->'.$this->relation->getProperty().');
	
	if( ! empty($to_del))
	{
		// construct a WHERE to only delete what we need to
		$where = array();
		
		foreach($to_del as $row)
		{
			';
		
		// create a filter for the primary keys
		$cols = array();
		foreach($related->getPrimaryKeys() as $key)
		{
			$cols[] = addcslashes($db->protectIdentifiers($key->getColumn()), "'") . ' = \'.$this->db->escape($row->__id[\''.$key->getColumn().'\'])';
		}
		
		$str .= '// group it to prevent the database to make faulty matches when using multiple primary keys
			$where[] = \'('.implode('.\' AND ', $cols).'.\')\';
		}
		
		';
		
		// null the foreign keys
		$set = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$set[] = $db->protectIdentifiers($fprop->getColumn()) . ' = NULL';
		}
		
		$str .= '$this->db->query(\'UPDATE ' . addcslashes($db->protectIdentifiers($related->getTable()), "'") . ' SET ' . addcslashes(implode(', ', $set), "'") . ' WHERE \'.implode(\' OR \', $where));
	}';
		
		$str .= '
	
	foreach(Db_Util::array_odiff('.$object_var.'->'.$this->relation->getProperty().', $object->__loaded_rels[\''.$this->relation->getProperty().'\']) as $key => $related)
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
			
			$arr[] = '$related->'.$fprop->getProperty().' = '.$object_var.'->'.$lprop->getProperty().';';
		}
		$str .= implode("\n\t\t\t", $arr);
		
		$str .= '
			
			Db::save($related);
		}
		else
		{
			// unrelated, remove
			unset('.$object_var.'->'.$this->relation->getProperty().'[$key]);
		}
	}
	
	// save a reference, so we can compare on later updates
	'.$object_var.'->__loaded_rels[\''.$this->relation->getProperty().'\'] = '.$object_var.'->'.$this->relation->getProperty().';
}';
		
		return $str;
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