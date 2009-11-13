<?php
/*
 * Created by Martin Wernståhl on 2009-08-09.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Renders the code responsible for dispatching loading of related objects.
 */
class Db_Mapper_Part_Objectify_LoadRelated extends Db_CodeBuilder_Container
{
	function __construct(Db_Descriptor $descriptor)
	{
		foreach($descriptor->getRelations() as $rel)
		{
			if($rel->isPlural())
			{
				$this->addPart('
case \''.$rel->getName().'\':
$rel = $mappers[$alias.\'-'.$rel->getName().'\']->objectify($res[$uid]->'.$rel->getName().', $row, $alias.\'-'.$rel->getName().'\', $mappers, $alias_paths[\''.$rel->getName().'\']);

$res[$uid]->__loaded_rels[\''.$rel->getName().'\'] = $res[$uid]->'.$rel->getName().';
break;');
			}
			else
			{
				$this->addPart('
case \''.$rel->getName().'\':
	$mappers[$alias.\'-' . $rel->getName() . '\']->objectify($res[$uid]->__loaded_rels[\'' . $rel->getName() . '\'], $row, $alias.\'-' . $rel->getName() . '\', $mappers, $alias_paths[\''.$rel->getName().'\']);

	// take the first one we get
	if( ! is_object($res[$uid]->'.$rel->getName().') && ! empty($res[$uid]->__loaded_rels[\''.$rel->getName().'\']))
	{
		$res[$uid]->'.$rel->getName().' = current($res[$uid]->__loaded_rels[\''.$rel->getName().'\']);
	}
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