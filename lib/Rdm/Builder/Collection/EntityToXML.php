<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Collection_EntityToXML extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor $desc)
	{
		$this->setMethodName('entityToXML');
		$this->setStatic(true);
		$this->setParamList('$object');
		
		$cols = array();
		foreach(array_merge($desc->getPrimaryKeys(), $desc->getColumns()) as $c)
		{
			$cols[] = "<{$c->getProperty()}>'.htmlspecialchars(".$c->getFetchFromObjectCode('$object').").'</{$c->getProperty()}>";
		}
		
		$cols = implode("\n\t", $cols);
		
		$this->addPart("return '<{$desc->getSingular()}>
	$cols
</{$desc->getSingular()}>';");
	}
}


/* End of file EntityToXML.php */
/* Location: ./lib/Rdm/Builder/Collection */