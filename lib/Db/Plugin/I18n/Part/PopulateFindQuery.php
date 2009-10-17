<?php
/*
 * Created by Martin Wernståhl on 2009-10-17.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

class Db_Plugin_I18n_Part_PopulateFindQuery extends Db_Mapper_Code_Method
{
	function __construct(Db_Descriptor $descriptor, Db_Plugin_I18n $plugin)
	{
		$this->name = 'populateFindQuery';
		
		$db = $descriptor->getConnection();
		
		$this->addPart('$q = new Db_Query_MapperSelect($this, \''.$descriptor->getSingular().'\');');
		
		$columns = $descriptor->getSelectCode($descriptor->getSingular(), $descriptor->getSingular());
		
		$this->addPart('$q->columns[] = \''.addcslashes($columns, "'").'\';
$q->from[] = \''.addcslashes($db->protectIdentifiers($descriptor->getTable()), "'").' AS '.addcslashes($db->protectIdentifiers($descriptor->getSingular()), "'").'\';');
		
		$this->addPart($plugin->getJoinTranslationCode('$q', $descriptor->getSingular()));
		
		// HOOK: on_find
		$this->addPart($descriptor->getHookCode('on_find', false, '$q'));
		
		// TODO: Add autoloaded join-related handling code
		
		$this->addPart('return $q;');
	}
}


/* End of file PopulateFindQueryPart.php */
/* Location: ./lib/Db/Plugin/I18n */