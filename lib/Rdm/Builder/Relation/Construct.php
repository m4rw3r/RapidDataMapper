<?php
/*
 * Created by Martin Wernståhl on 2010-04-14.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Relation_Construct extends Rdm_Util_Code_MethodBuilder
{
	public function __construct(Rdm_Descriptor_Relation $rel, Rdm_Descriptor $desc)
	{
		$this->setMethodName('__construct');
		$this->setParamList('$object = null, $alias = \'\', Rdm_Adapter $db = null');
		$this->setPhpDoc('Internal: Creates a new Relation filter which filters '.$desc->getClass().' by their
related '.$rel->getName().', this will put it in "reverse mode", if an $object is not present, "reverse mode" won\'t be triggered.

So for relating from '.$desc->getClass().' to '.$rel->getName().', ie. JOIN '.$rel->getName().' to
the '.$desc->getClass().' query, use new '.$rel->getRelationFilterClassName().';

To filter '.$desc->getClass().' by an already fetched '.$rel->getName().' object ('.$rel->getRelatedDescriptor()->getClass().'),
ie. WHERE '.$desc->getClass().'.key = $related_object->otherkey,
use new '.$rel->getRelationFilterClassName().'($related_object, \'alias for '.$desc->getClass().'\');

@internal
@param  '.$rel->getRelatedDescriptor()->getClass().'   The instance to filter by, if the entities to fetch
        '.str_repeat(' ', strlen($rel->getRelatedDescriptor()->getClass())).'   should relate to this specific instance, optional.
@param  string        The alias of the table containing '.$desc->getClass().' which should be filtered by
                      $object.
@param  Rdm_Adapter   The database instance used when escaping the data from the $object,
                      if omitted, then the default instance will be used.');
		
		$this->addPart('if( ! is_null($object))
{
	$this->reverse = true;
	$this->parent_object = $object;
}

$this->alias = $alias;
$this->db = is_null($db) ? '.$desc->getFetchAdapterCode().' : $db;');
	}
}


/* End of file Construct.php */
/* Location: ./lib/Rdm/Builder/Relation */