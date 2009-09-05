<?php
/*
 * Created by Martin Wernståhl on 2009-09-05.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A decorator which checks if the source column is changed, and if so it gets
 * its content and filters it through the callable to finally assign it to the column of the decorated object.
 */
class Db_Plugin_Sluggable_Decorator extends Db_Decorator
{
	/**
	 * The source column.
	 * 
	 * @var Db_Descriptor_Column
	 */
	protected $source;
	
	/**
	 * The callable which converts the title-ish column data to an url-friendly string.
	 * 
	 * @var string
	 */
	protected $callable;
	
	/**
	 * @param  Db_Descriptor_Column
	 * @param  Db_Descriptor_Column
	 * @param  string
	 */
	function __construct(Db_Descriptor_Column $destination, Db_Descriptor_Column $source, $callable)
	{
		$this->setDecoratedObject($destination);
		$this->source = $source;
		$this->callable = $callable;
	}
	
	/**
	 * Overridden method of Db_Descriptor_Column::getFromObjectToDataCode().
	 * 
	 * It will add specific logic which converts the source column value to an
	 * url-friendly value.
	 * 
	 * @see Db_Descriptor_Column::getFromObjectToDataCode()
	 * @param  string
	 * @param  string
	 * @param  bool
	 * @return string
	 */
	public function getFromObjectToDataCode($object_var, $dest_var, $is_update = false)
	{
		// only assign the columns which are allowed to be updated
		if(( ! $is_update && $this->isInsertable()) OR $is_update && $this->isUpdatable())
		{
			return 'Db::isChanged('.$object_var.', '.$this->source->getProperty().') && '.$dest_var.'[\''.$this->getColumn().'\'] = '.$this->callable.'('.$this->source->getCastFromPhpCode($this->source->getFromObjectCode($object_var)).');';
		}
		else
		{
			return '';
		}
	}
}

/* End of file Decorator.php */
/* Location: ./lib/Db/Plugin/Sluggable */