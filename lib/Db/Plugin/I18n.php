<?php
/*
 * Created by Martin Wernståhl on 2009-10-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Internationalization plugin for RapidDataMapper.
 */
class Db_Plugin_I18n extends Db_Plugin
{
	/**
	 * Columns to internationalize.
	 * 
	 * @var array
	 */
	protected $columns = array();
	
	/**
	 * A list to keep track of the decorators we've added.
	 * 
	 * @var array
	 */
	protected $decorators = array();
	
	/**
	 * The name of the column containing the language key.
	 * 
	 * @var string
	 */
	protected $lang_key = 'lang';
	
	/**
	 * The column keeping track of which language is currently used.
	 * 
	 * @var Db_Descriptor_Column
	 */
	protected $lang_column = null;
	
	/**
	 * The suffix for the table alias when aliasing the table containing the translations.
	 * 
	 * @var string
	 */
	protected $alias_suffix = 'Translation';
	
	/**
	 * Prefix for the table containing the internationalization data.
	 */
	protected $table_prefix = '';
	
	/**
	 * Suffix for the table containing the internationalization data.
	 * 
	 * @var string
	 */
	protected $table_suffix = '_lang';
	
	/**
	 * Table containing the language data.
	 * 
	 * @var string
	 */
	protected $lang_table = false;
	
	/**
	 * Default language for the translated text.
	 * 
	 * @var string
	 */
	protected $default_language = 'en';
	
	/**
	 * @param  array	Associative array containing the settings
	 */
	function __construct(array $options)
	{
		if(empty($options['columns']))
		{
			throw new Db_Exception('Db_Plugin_I18n: Missing "columns" option in options given to constructor');
		}
		else
		{
			$this->columns = (Array)$options['columns'];
		}
		
		if( ! empty($options['default_language']))
		{
			$this->default_language = $options['default_language'];
		}
		
		if( ! empty($options['table_prefix']))
		{
			$this->table_prefix = $options['table_prefix'];
		}
		
		if( ! empty($options['table_suffix']))
		{
			$this->table_suffix = $options['table_suffix'];
		}
		
		if( ! empty($options['lang_table']))
		{
			$this->lang_table = $options['lang_table'];
		}
		
		if( ! empty($options['alias_suffix']))
		{
			$this->alias_suffix = $options['alias_suffix'];
		}
		
		$this->lang_key = empty($options['lang_key']) ? 'lang' : $options['lang_key'];
		
		if(in_array($this->lang_key, $this->columns))
		{
			throw new Db_Exception('Db_Plugin_I18n: The language id column "'.$this->lang_key.'" cannot be internationalized.');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function init()
	{
		// init lang table name
		if( ! $this->lang_table)
		{
			$this->lang_table = $this->table_prefix.$this->descriptor->getTable().$this->table_suffix;
		}
		
		// register extra code for the joinRelated() method
		$this->descriptor->setPluginHook('relation.joinRelated.extra_code', array($this, 'getJoinTranslationCode'));
		
		$columns = $this->descriptor->getColumns();
		$to_decorate = $this->columns;
		
		// get the language column and apply its decorator
		$col = false;
		foreach($columns as $column)
		{
			if($column->getColumn() == $this->lang_key)
			{
				if($column instanceof Db_Descriptor_PrimaryKey)
				{
					throw new Db_Exception('Db_Plugin_I18n: Cannot use primary key "'.$column->getColumn().'" as language key.');
				}
				
				$dec = new Db_Plugin_I18n_LangColumnDecorator($column, $this);
				
				$this->lang_column = $dec;
				
				$this->descriptor->addDecorator($dec);
				
				$this->decorators[] = $dec;
				
				$col = true;
				break;
			}
		}
		
		if( ! $col)
		{
			throw new Db_Exception('Db_Plugin_I18n: Cannot find language key column "'.$this->lang_key.'".');
		}
		
		// add decorators to columns
		foreach($columns as $column)
		{
			if(in_array($column->getColumn(), $to_decorate) &&
			 	! self::hasDecorator($column, 'Db_Plugin_Sluggable_Decorator'))
			{
				if($column instanceof Db_Descriptor_PrimaryKey)
				{
					throw new Db_Exception('Db_Plugin_I18n: Cannot translate primary key "'.$column->getColumn().'".');
				}
				
				$dec = new Db_Plugin_I18n_I18nColumnDecorator($column, $this);
				
				$this->descriptor->addDecorator($dec);
				
				$this->decorators[] = $dec;
				
				// remove the column name from the list, as we've covered it
				unset($to_decorate[array_search($column->getColumn(), $to_decorate)]);
			}
		}
		
		// do we still have columns to decorate?
		if($to_decorate)
		{
			throw new Db_Exception('Db_Plugin_I18n: Cannot find column "'.$this->descriptor->getClass().'::'.array_shift($to_decorate).'".');
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function remove()
	{
		foreach($this->decorators as $k => $d)
		{
			$this->descriptor->removeDecorator($d);
			
			unset($this->decorators[$k]);
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function editBuilder($builder)
	{
		$db = $this->descriptor->getConnection();
		
		$save_insert = new Db_Plugin_I18n_MapperPart_Save_Insert($this->descriptor, $this);
		$save_update = new Db_Plugin_I18n_MapperPart_Save_Update($this->descriptor, $this);
		
		if( ! $builder->addPart($save_insert, 'mapper.method_save', true))
		{
			throw new Db_Exception('Db_Plugin_I18n: Cannot replace the insert part of the save method.');
		}
		
		if( ! $builder->addPart($save_update, 'mapper.method_save', true))
		{
			throw new Db_Exception('Db_Plugin_I18n: Cannot replace the update part of the save method.');
		}
		
		if( ! $builder->addPart($this->getJoinTranslationCode('$this', $this->descriptor->getSingular(), '$mapper'), 'query.method___construct'))
		{
			throw new Db_Exception('Db_Plugin_I18n: Cannot add code to the constructor of the MapperQuery.');
		}
		
		$builder->getPart('query.method___construct')->render = true;
		
		$builder->addPart(new Db_Plugin_I18n_MapperPart_SetLang($this->descriptor), 'mapper');
		
		$builder->addPart(new Db_CodeBuilder_Property('language', $db->escape($this->default_language)), 'mapper');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the conditions that links the translated table together with the
	 * internationalized table.
	 * 
	 * @param  string	The name of the variable containing the query variable
	 * @param  string	The name of the variable containing the base_alias (empty to use no alias)
	 * @return string
	 */
	public function getJoinTranslationCode($query_var, $base_alias, $mapper_object = '$this')
	{
		$conds = array();
		$db = $this->descriptor->getConnection();
		
		foreach($this->descriptor->getPrimaryKeys() as $key)
		{
			$conds[] = $db->protectIdentifiers($base_alias.'.'.$key->getColumn().' = '.$base_alias.$this->alias_suffix.'.'.$key->getColumn());
		}
		
		$conditions = implode(' AND ', $conds);
		
		return $query_var.'->join[] = "LEFT JOIN '.addcslashes($db->protectIdentifiers($this->lang_table), '"').' AS '.addcslashes($db->protectIdentifiers($base_alias.$this->alias_suffix), '"').'
	ON '.$conditions.' AND '.addcslashes($db->protectIdentifiers($base_alias.$this->alias_suffix.'.'.$this->lang_column->getColumn()), '"').' = ".'.$mapper_object.'->language;';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the table storing the language data.
	 * 
	 * @return string
	 */
	public function getLangTable()
	{
		return $this->lang_table;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the suffix to use when aliasing internationalized columns.
	 * 
	 * @return string
	 */
	public function getAliasSuffix()
	{
		return $this->alias_suffix;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the language identifier to use by default.
	 * 
	 * @return string
	 */
	public function getDefaultLanguage()
	{
		return $this->default_language;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the column which is responsible for containing the language identifier.
	 * 
	 * @return Db_Descriptor_Column
	 */
	public function getLangColumn()
	{
		return $this->lang_column;
	}
}


/* End of file I18n.php */
/* Location: ./lib/Db/Plugin */