<?php
/*
 * Created by Martin Wernståhl on 2010-05-06.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Descriptor loader which loads descriptor from files located in a specific
 * directory.
 * 
 * Usage:
 * <code>
 * Rdm_Config::addDescriptorLoader(array(new Rdm_Util_DescriptorLoader_File('./descriptor/folder'), 'load));
 * </code>
 * 
 * Default naming conventions:
 * 
 * - Class: {$class}Descriptor
 * - File: {$class}{$extension}
 * - $extension: ".php"
 * 
 * These can be changed by extending the class and replacing the mehtods
 * getFileName($class) and getDescriptorClassName($class).
 * $extension can be passed to the constructor.
 */
class Rdm_Util_DescriptorLoader_File
{
	/**
	 * The folder which should be searched for PHP files containing descriptors.
	 * 
	 * @var string
	 */
	protected $folder;
	
	/**
	 * The file extension for the files containing the descriptors.
	 * 
	 * @var string
	 */
	protected $extension = '.php';
	
	/**
	 * Creates a file descriptor loader loading descriptors from files named
	 * $class + $extension in the folder $folder.
	 * 
	 * @param  string
	 * @param  string
	 */
	function __construct($folder, $extension = '.php')
	{
		$this->folder = $folder;
		$this->extension = $extension;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Loads a descriptor for the supplied class.
	 * 
	 * @param  string
	 * @return Rdm_Descriptor|false
	 */
	public function load($class)
	{
		$file = $this->getFileName($class);
		$desc_class = $this->getDescriptorClassName($class);
		
		if(file_exists($this->folder.DIRECTORY_SEPARATOR.$file))
		{
			require $this->folder.DIRECTORY_SEPARATOR.$file;
			
			// Check if the class is really loaded
			if( ! class_exists($desc_class, false))
			{
				throw Rdm_Util_DescriptorLoader_Exception::fileMissingClass($this->folder.DIRECTORY_SEPARATOR.$file, $class, $desc_class);
			}
			
			return new $desc_class;
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates the file name of the file containing the descriptor for the entity
	 * $class.
	 * 
	 * @param  string
	 * @return string
	 */
	protected function getFileName($class)
	{
		return $class.$this->extension;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Creates the descriptor class name of the class which is located in the file
	 * which is supplied by $folder.$this->getFileName(), describing $class mappings.
	 * 
	 * @param  string
	 * @return string
	 */
	public function getDescriptorClassName($class)
	{
		return $class.'Descriptor';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Magic method for PHP 5.3 so it is possible to pass the object directly
	 * to the Rdm_Config::addDescriptorLoader() method.
	 * 
	 * @param  string
	 * @return Rdm_Descriptor|false
	 */
	public function __invoke($class)
	{
		return $this->load($class);
	}
}


/* End of file File.php */
/* Location: ./lib/Rdm/Util/DescriptorLoader */