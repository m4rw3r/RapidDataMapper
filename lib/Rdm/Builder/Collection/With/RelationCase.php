<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_With_RelationCase extends Rdm_Util_Code_Container
{
	protected $relation_id;
	
	protected $relation_name;
	
	protected $is_first;
	
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc, $first)
	{
		$this->relation_name = $rel->getName();
		$this->relation_id   = $rel->getConstantIdentifier();
		$this->is_first      = $first;
		
		$this->addPart('if(isset($this->with[\''.$rel->getName().'\']))
{
	return $this->with[\''.$rel->getName().'\'];
}');
		
		$this->addPart('// Create the nested collection, and nested alias is the relation name if it isn\'t a nested join
$c = new '.$rel->getRelatedDescriptor()->getCollectionClassName(true, true).'($this, new '.$rel->getRelationFilterClassName().', empty($this->parent) ? \''.$rel->getName().'\' : $this->table_alias.\'_'.$rel->getName().'\');');
		
		$this->addPart('$this->with[\''.$rel->getName().'\'] = $c;');
		
		$this->addPart('return $c;');
	}
	
	// ------------------------------------------------------------------------

	public function getName()
	{
		return $this->relation_name;
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$code = ($this->is_first ? '' : 'else').'if($relation_id == '.$this->relation_id.')
{
	'.self::indentCode(implode("\n\n", $this->content)).'
}';
		
		$this->content = array($code);
		
		return parent::__toString();
	}
}


/* End of file RelationCase.php */
/* Location: ./lib/Rdm/Builder/Collection/With */