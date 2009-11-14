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
	 * The plugin object.
	 * 
	 * @var Db_Plugin_I18n
	 */
	protected $plugin;
	
	/**
	 * @param  Db_Descriptor_Column
	 */
	function __construct(Db_Descriptor_Column $destination, Db_Plugin_I18n $plugin)
	{
		$this->setDecoratedObject($destination);
		$this->plugin = $plugin;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the source column name (ie. as it looks in the final SQL), including the table alias.
	 * 
	 * @param  string
	 * @return string
	 */
	public function getSourceColumn($table_alias)
	{
		return $this->getDecoratedObject()->getSourceColumn($table_alias.$this->plugin->getAliasSuffix());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a fragment which selects the column and aliases it properly.
	 * 
	 * Needed because it has to call the local getSourceColumn().
	 * 
	 * @param  string			Passed through Db_Connection::protectIdentifiers()
	 * @param  string			Passed through Db_Connection::protectIdentifiers()
	 * @param  Db_Connection
	 * @return string
	 */
	public function getSelectCode($table, $alias, Db_Connection $db)
	{
		return $db->protectIdentifiers($this->getSourceColumn($table)).' AS '.$db->protectIdentifiers($alias.'_'.$this->getProperty());
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a piece of code which fetches the data from an instance of the described
	 * object and inserts it into an array, with the column name as key.
	 * 
	 * Only assign data if the value exists on the described object, otherwise
	 * do not assign anything to the $dest_var.
	 * 
	 * Example of generated code:
	 * <code>
	 * // params: $object_var = '$obj', $dest_var = '$data'
	 * isset($obj->id) && $data['PK_id'] = (Int) $obj->id;
	 * </code>
	 * 
	 * @param  string	The name of the variable holding an instance of the described object.
	 * @param  string	The name of the variable holding an associative array to assign the data to.
	 * @param  bool		If it is an update which the code is fetching data for
	 * @return string
	 */
	public function getFromObjectToDataCode($object_var, $dest_var, $is_update = false)
	{
		// only assign the columns which are allowed to be updated
		if(( ! $is_update && $this->isInsertable()) OR $is_update && $this->isUpdatable())
		{
			return 'isset('.$object_var.'->'.$this->getProperty().') && $lang_data[\''.$this->getColumn().'\'] = '.$this->getCastFromPhpCode($this->getFromObjectCode($object_var)).';';
		}
		else
		{
			return '';
		}
	}
}

/* End of file I18nColumnDecorator.php */
/* Location: ./lib/Db/Plugin/I18n */