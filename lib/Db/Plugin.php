<?php
/*
 * Created by Martin Wernståhl on 2009-09-02.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A base class for plugins.
 */
abstract class Db_Plugin
{
	/**
	 * The descriptor which this plugin is associated with.
	 * 
	 * @var Db_Descriptor
	 */
	protected $descriptor;
	
	// ------------------------------------------------------------------------

	/**
	 * Sets which descriptor to use.
	 * 
	 * @param  Db_Descriptor
	 * @return void
	 */
	final public function setDescriptor(Db_Descriptor $desc)
	{
		$this->descriptor = $desc;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes the plugin, the descriptor property is set.
	 * 
	 * @return void
	 */
	public function init(){ }
	
	// ------------------------------------------------------------------------

	/**
	 * Provides a chance for the plugin to edit the builder before it renders the class.
	 * 
	 * @param  Db_Mapper_Builder
	 * @return void
	 */
	public function editBuilder($builder){ }
	
	// ------------------------------------------------------------------------

	/**
	 * Removes a plugin from the descriptor it is assigned to.
	 * 
	 * @return 
	 */
	public function remove(){ }
}


/* End of file Plugin.php */
/* Location: ./lib/Db */