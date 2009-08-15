<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code responsible for dispatching loading of related objects.
 */
class Db_Mapper_Part_Objectify_LoadRelated extends Db_Mapper_CodeContainer
{
	protected $descriptor;
	
	function __construct(Db_Descriptor $desc)
	{
		$this->descriptor = $desc;
		
		$this->addContents();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Adds the default contents to this CodeContainer
	 * 
	 * @return void
	 */
	public function addContents()
	{
		foreach($this->descriptor->getRelations() as $rel)
		{
			// TODO: Make something more generic
			switch($rel->getType())
			{
				case Db_Descriptor::HAS_ONE:
				case Db_Descriptor::BELONGS_TO:
					$this->addPart('
case \''.$rel->getName().'\':
	$mappers[$alias.\'-' . $rel->getName() . '\']->objectify($res[$uid]->__loaded_rels[\'' . $rel->getName() . '\'], $row, $alias.\'-' . $rel->getName() . '\', $mappers, $alias_paths[\''.$rel->getName().'\']);

	// take the first one we get
	if( ! is_object($res[$uid]->'.$rel->getName().') && ! empty($res[$uid]->__loaded_rels[\''.$rel->getName().'\']))
	{
		$res[$uid]->'.$rel->getName().' = current($res[$uid]->__loaded_rels[\''.$rel->getName().'\']);
	}
	break;');
					break;
				default:
					$this->addPart('
case \''.$rel->getName().'\':
	$rel = $mappers[$alias.\'-'.$rel->getName().'\']->objectify($res[$uid]->'.$rel->getName().', $row, $alias.\'-'.$rel->getName().'\', $mappers, $alias_paths[\''.$rel->getName().'\']);

	$res[$uid]->__loaded_rels[\''.$rel->getName().'\'] = $res[$uid]->'.$rel->getName().';
	break;');
			}
		}
	}
	
	public function getName()
	{
		return 'load_related';
	}
	
	// ------------------------------------------------------------------------
	
	public function __toString()
	{
		$str = "foreach(array_keys(\$alias_paths) as \$k)\n{\n\tswitch(\$k)\n\t{";
		
		$str .= self::indentCode(self::indentCode("\n".implode("\n\n", $this->content)));
		
		return $str."\n\t}\n}";
	}
}


/* End of file NewObj.php */
/* Location: ./lib/Db/Mapper/Part/Objectify */