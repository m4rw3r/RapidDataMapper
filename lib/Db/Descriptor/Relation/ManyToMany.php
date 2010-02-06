<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains the specific logic for the Many To Many relationship type.
 * 
 * TODO: Add methods for changing the foreign key mappings
 */
class Db_Descriptor_Relation_ManyToMany implements Db_Descriptor_RelationInterface
{
	/**
	 * A list of properties which will be used to restrict the related records.
	 * 
	 * @var array
	 */
	protected $extra_conds = array();
	
	/**
	 * Contains the foreign key mappings which maps the object to a row in the link table.
	 * 
	 * owning object prop name => link table column name.
	 * 
	 * @var array
	 */
	protected $foreign_keys_to_link = array();
	
	/**
	 * Contains the foreign key mappings which maps a row in the link table to the related object.
	 * 
	 * related object prop name => link table column name.
	 * 
	 * @var array
	 */
	protected $foreign_keys_from_link = array();
	
	/**
	 * The table linking the two objects together.
	 * 
	 * @var string
	 */
	protected $link_table;
	
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
	 * Returns the name of the table which is linking the objects together,
	 * 
	 * Default:
	 * 
	 * The default table name is made by taking the table names of the tables
	 * which contains the objects (eg. artists and tracks) and then putting
	 * them in alphabetical order, separated with a single underscore:
	 * artists_tracks.
	 * 
	 * @return string
	 */
	public function getLinkTable()
	{
		if(empty($this->link_table))
		{
			$ptable = $this->relation->getParentDescriptor()->getTable();
			$ftable = $this->relation->getRelatedDescriptor()->getTable();
			
			if(strcmp($ptable, $ftable) < 0)
			{
				$this->link_table = $ptable.'_'.$ftable;
			}
			else
			{
				$this->link_table = $ftable.'_'.$ptable;
			}
		}
		
		return $this->link_table;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the table name of the table which is linking the objects together.
	 * 
	 * @param  string
	 * @return Db_Descriptor_Relation	The parent object
	 */
	public function setLinkTable($table)
	{
		$this->link_table = $table;
		
		return $this->relation;
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
	
	public function isPlural()
	{
		return true;
	}
	
	// ------------------------------------------------------------------------
	
	public function getJoinRelatedCode($query_obj_var, $alias_of_linked_var)
	{
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $link_local_keys) = $this->getKeysToLink();
		list($link_foreign_keys, $foreign_keys) = $this->getKeysFromLink();
		
		// build foreign key conditions to link
		$local_cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $link_local_keys[$i];
			
			$local_cols[] = $db->protectIdentifiers($alias_of_linked_var.'.'.$lprop->getColumn()).' = '.
				$db->protectIdentifiers($alias_of_linked_var.'-_l_' . $this->getLinkTable().'.'.$fprop);
		}
		
		// build foreign key conditions from link to related
		$cols = array();
		$c = count($foreign_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $link_foreign_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$cols[] = $db->protectIdentifiers($alias_of_linked_var.'-_l_' . $this->getLinkTable().'.'.$lprop).' = '.
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
		
		$str = '$query->join[] = "LEFT JOIN ' .
			addcslashes($db->protectIdentifiers($db->dbprefix . $this->getLinkTable()), '"') . 
			' AS ' . addcslashes($db->protectIdentifiers('$alias_of_linked-_l_' . $this->getLinkTable()), '"');
		
		$str .= ' 
	ON ' . addcslashes(implode(' AND ', $local_cols), '"');
		
		$str .= '
LEFT JOIN ' . addcslashes($db->protectIdentifiers($db->dbprefix . $related->getTable()), '"') . 
			' AS '.addcslashes($db->protectIdentifiers('$alias_of_linked-' . $this->relation->getName()), '"');
		
		$str .= '
	ON ' . addcslashes(implode(' AND ', $cols), '"')."\";\n";
		
		$str .= $related->getPluginHook('relation.joinRelated.extra_code', array('$query', '$alias_of_linked-' . $this->relation->getName()))."\n";
		
		return $str.$columns;
	}
	
	// ------------------------------------------------------------------------
	
	public function getApplyRelatedConditionsCode($query_obj_var, $object_var)
	{
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $link_local_keys) = $this->getKeysToLink();
		list($link_foreign_keys, $foreign_keys) = $this->getKeysFromLink();
		
		// build foreign key conditions to link
		$local_cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $link_local_keys[$i];
			
			$local_cols[] = addcslashes($db->protectIdentifiers($related->getSingular().'-_l_' . $this->getLinkTable().'.'.$fprop), "'").' = \'.$this->db->escape('.$object_var.'->'.$lprop->getProperty().')';
		}
		
		// build foreign key conditions from link to related
		$cols = array();
		$c = count($foreign_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $link_foreign_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$cols[] = $db->protectIdentifiers($related->getSingular().'-_l_' . $this->getLinkTable().'.'.$lprop).' = '.
				$db->protectIdentifiers($related->getSingular().'.'.$fprop->getColumn());
		}
		
		// add extra conditions
		foreach($this->extra_conds as $k => $v)
		{
			$cols[] = $db->protectIdentifiers($related->getSingular().'.'.$k).' = '.$db->escape($v);
		}
		
		return $query_obj_var.'->join[] = \'LEFT JOIN '.
			addcslashes($db->protectIdentifiers($db->dbprefix . $this->getLinkTable()), "'") . 
			' AS ' . addcslashes($db->protectIdentifiers($related->getSingular().'-_l_'.$this->getLinkTable()), "'").'
	ON '.addcslashes(implode(' AND ', $cols), "'").'\';
'.$query_obj_var.'->where_prefix = \'' . 
			implode('.\'', $local_cols).'.\' AND (\';
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
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $link_local_keys) = $this->getKeysToLink();
		list($link_foreign_keys, $foreign_keys) = $this->getKeysFromLink();
		
		$str = '// The Many To Many relation '.$this->relation->getName().', relates to '.$related->getClass().'
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
			if(empty($related->__id) && ! Db::save($related))
			{
				// failed save
				continue;
			}
			
			';
		
		// TODO: Add the extra conditions if present
		
		// build foreign key conditions to link
		$local_cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $link_local_keys[$i];
			
			$cols[] = "'$fprop' => $object_var->".$lprop->getProperty();
		}
		
		// build foreign key conditions from link to related
		$c = count($foreign_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $link_foreign_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$cols[] = "'$lprop' => \$related->".$fprop->getProperty();
		}
		
		$str .= '$this->db->insert(\''.$this->getLinkTable().'\', array('.implode(', ', $cols).'));
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
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $link_local_keys) = $this->getKeysToLink();
		list($link_foreign_keys, $foreign_keys) = $this->getKeysFromLink();
		
		$str = '// The Many To Many relation '.$this->relation->getName().', relates to '.$related->getClass().'
if(isset('.$object_var.'->'.$this->relation->getProperty().'))
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
		$c = count($foreign_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $link_foreign_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$cols[] = addcslashes($db->protectIdentifiers($lprop), "'").' = \'.$this->db->escape($row->'.$fprop->getProperty().')';
		}
		
		$str .= '// group it to prevent the database to make faulty matches when using multiple primary keys
			$where[] = \'('.implode('.\' AND ', $cols).'.\')\';
		}
		
		';
		
		// filter by the owning object's primary keys
		$cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $link_local_keys[$i];
			
			$cols[] = addcslashes($db->protectIdentifiers($fprop), "'").' = \'.$this->db->escape('.$object_var.'->'.$lprop->getProperty().')';
		}
		
		$str .= '$this->db->query(\'DELETE FROM '.addcslashes($db->protectIdentifiers($db->dbprefix.$this->getLinkTable()), "'").' WHERE (\'.implode(\' OR \', $where).\') AND ' . implode('.\' AND ', $cols).');
	}
	
	foreach(Db_Util::array_odiff('.$object_var.'->'.$this->relation->getProperty().', $object->__loaded_rels[\''.$this->relation->getProperty().'\']) as $key => $related)
	{
		// check so they are of the correct type
		if($related instanceof '.$related->getClass().')
		{
			if(empty($related->__id) && ! Db::save($related))
			{
				// failed save
				continue;
			}
			
			';
		
		// loop the local columns to create a list with fks to populate/search for
		$cols = array();
		$where = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $link_local_keys[$i];
			
			$cols[] = "'$fprop' => $object_var->".$lprop->getProperty();
			
			$where[] = addcslashes($db->protectIdentifiers($fprop), "'") . ' = \'.$this->db->escape('.$object_var.'->'.$lprop->getProperty().')';
		}
		
		// TODO: Add the extra conditions if present
		
		// repeat for the second part of the link
		$c = count($foreign_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $link_foreign_keys[$i];
			$fprop = $foreign_keys[$i];
			
			$cols[] = "'$lprop' => \$related->".$fprop->getProperty();
			
			$where[] = addcslashes($db->protectIdentifiers($lprop), "'") . ' = \'.$this->db->escape($related->'.$fprop->getProperty().')';
		}
		
		$str .= 'if( ! $this->db->query(\'SELECT COUNT(1) FROM '.addcslashes($db->protectIdentifiers($db->dbprefix . $this->getLinkTable()), "'").' WHERE '.implode('.\' AND ', $where) . ')->val())
			{
				$this->db->insert(\''.$this->getLinkTable().'\', array('.implode(', ', $cols).'));
			}
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

	public function getUnlinkObjectRelationCode($object_var)
	{
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $link_local_keys) = $this->getKeysToLink();
		
		// filter by the owning object's primary keys
		$cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $link_local_keys[$i];
			
			$cols[] = addcslashes($db->protectIdentifiers($fprop), "'").' = \'.$this->db->escape('.$object_var.'->'.$lprop->getProperty().')';
		}
		
		return '$this->db->query(\'DELETE FROM '.addcslashes($db->protectIdentifiers($db->dbprefix.$this->getLinkTable()), "'").' WHERE ' . implode('.\' AND ', $cols).');';
	}
	
	// ------------------------------------------------------------------------

	public function getUnlinkQueryRelationCode($query_var)
	{
		$db = $this->relation->getParentDescriptor()->getConnection();
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		list($local_keys, $link_local_keys) = $this->getKeysToLink();
		
		// filter by the owning object's primary keys
		$cols = array();
		$c = count($local_keys);
		for($i = 0; $i < $c; $i++)
		{
			$lprop = $local_keys[$i];
			$fprop = $link_local_keys[$i];
			
			$cols[] = addcslashes($db->protectIdentifiers($db->dbprefix.$this->getLinkTable().'.'.$fprop.' = '.$local->getTable().'.'.$lprop->getColumn()), "'");
		}
		
		// Check if we need additional columns
		$pks = array();
		foreach($local->getPrimaryKeys() as $pk)
		{
			$pks[] = $pk->getColumn();
		}
		$fks = array();
		foreach($local_keys as $lk)
		{
			$fks[] = $lk->getColumn();
		}
		if(array_diff($fks, $fks))
		{
			// Primary keys are not sufficient for WHERE FILTER
			
			// Build the filter which will delete the objects determined by the subquery
			$filter = array();
			foreach($local->getPrimaryKeys() as $key)
			{
				$filter[] = $db->protectIdentifiers('n.'.$key->getColumn().' = o.'.$key->getColumn());
			}
			
			return '$this->db->query(\'DELETE '.addcslashes($db->protectIdentifiers($db->dbprefix.$this->getLinkTable()), "'").' FROM '.addcslashes($db->protectIdentifiers($db->dbprefix.$this->getLinkTable()), "'").',
(SELECT n.* FROM '.addcslashes($db->protectIdentifiers($local->getTable()), "'").' AS n,
(\'.'.$query_var.'->getSQL().\') AS o WHERE '.implode(' AND', $filter).')) AS '.addcslashes($db->protecIdentifiers($local->getTable()), "'").' WHERE ' . implode(' AND ', $cols).');';
		}
		else
		{
			// Primary keys are sufficient
			return '$this->db->query(\'DELETE '.addcslashes($db->protectIdentifiers($db->dbprefix.$this->getLinkTable()), "'").' FROM '.addcslashes($db->protectIdentifiers($db->dbprefix.$this->getLinkTable()), "'").',
(\'.'.$query_var.'->getSQL().\') AS '.addcslashes($db->protectIdentifiers($local->getTable()), "'").' WHERE ' . implode(' AND ', $cols).'\');';
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Returns a list of the keys to use linking the object to the link table.
	 * 
	 * parent table column => link table column.
	 * 
	 * @return array	array(Db_Descriptor_Column, string)
	 */
	protected function getKeysToLink()
	{
		$local = $this->relation->getParentDescriptor();
		
		if(empty($this->foreign_keys_to_link))
		{
			$this->foreign_keys_to_link = $this->relation->guessForeignKeyMappings($local);
		}
		
		$local_keys = $this->relation->getKeyObjects(array_keys($this->foreign_keys_to_link), $local);
		
		// TODO: Maybe use a "dummy object" for the keys in the link table?
		return array(array_values($local_keys), array_values($this->foreign_keys_to_link));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of the keys to use linking the related object to the link table.
	 * 
	 * link table column => related table column.
	 * 
	 * @return array	array(string, Db_Descriptor_Column)
	 */
	protected function getKeysFromLink()
	{
		$related = $this->relation->getRelatedDescriptor();
		
		if(empty($this->foreign_keys_from_link))
		{
			$this->foreign_keys_from_link = $this->relation->guessForeignKeyMappings($related);
		}
		
		$related_keys = $this->relation->getKeyObjects(array_keys($this->foreign_keys_from_link), $related);
		
		// TODO: Maybe use a "dummy object" for the keys in the link table?
		return array(array_values($this->foreign_keys_from_link), array_values($related_keys));
	}
}


/* End of file ManyToMany.php */
/* Location: ./lib/Db/Descriptor/Relation */