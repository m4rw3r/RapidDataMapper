<?php
/*
 * Created by Martin Wernståhl on 2009-10-17.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Db_Plugin_I18n_I18nColumnDecorator extends Db_Decorator
{
	/**
	 * The suffix for the table alias for the translated table.
	 * 
	 * @var string
	 */
	protected $alias_suffix;
	
	/**
	 * @param  Db_Descriptor_Column
	 */
	function __construct(Db_Descriptor_Column $destination, $alias_suffix)
	{
		$this->setDecoratedObject($destination);
		$this->alias_suffix = $alias_suffix;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a fragment which selects the column and aliases it properly.
	 * 
	 * @param  string			Passed through Db_Connection::protectIdentifiers()
	 * @param  string			Passed through Db_Connection::protectIdentifiers()
	 * @param  Db_Connection
	 * @return string
	 */
	public function getSelectCode($table, $alias, Db_Connection $db)
	{
		return $db->protectIdentifiers($table.$this->alias_suffix.'.'.$this->getColumn()).' AS '.$db->protectIdentifiers($alias.'_'.$this->getProperty());
	}
}

/* End of file I18nColumnDecorator.php */
/* Location: ./lib/Db/Plugin/I18n */