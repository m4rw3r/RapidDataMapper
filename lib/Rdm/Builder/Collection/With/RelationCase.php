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
		$this->relation_id   = $rel->getIntegerIdentifier();
		$this->is_first      = $first;
		
		$this->addPart('$c = new '.$rel->getRelatedDescriptor()->getClass().'Collection($this, '.$this->relation_id.');');
		
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