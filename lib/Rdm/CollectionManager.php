<?php
/*
 * Created by Martin Wernståhl on 2010-07-04.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Class which manages the Rdm_Collection classes and their code.
 * 
 * Example:
 * <code>
 * $config = new Rdm_Config();
 * // Configure...
 * 
 * // Map all classes in the Foo namespace using $manager:
 * $manager = new Rdm_Collection($config, 'Foo\\');
 * $manager->registerCollectionAutoloader();
 * </code>
 */
class Rdm_CollectionManager
{
	/**
	 * The configuration instance for this collection manager.
	 * 
	 * @var Rdm_Config
	 */
	protected $config = null;
	
	/**
	 * A list of collection names and their dependencies.
	 * 
	 * @var array(string => array)
	 */
	protected $dependencies = array();
	
	/**
	 * The object determining the order in which unit of works should run their
	 * operations.
	 * 
	 * @var Rdm_UnitOfWork_CommitOrderCalculator
	 */
	protected $commit_order_calculator = null;
	
	/**
	 * The prefix of the classes to be mapped by this CollectionManager.
	 * 
	 * @var false|string
	 */
	protected $class_prefix = false;
	
	// ------------------------------------------------------------------------

	/**
	 * Creates a new CollectionManager instance.
	 * 
	 * @param  Rdm_Config
	 * @param  string      A prefix of the classes which are to use this
	 *                     CollectionManager to map to the database,
	 *                     to map a namespace, just pass the name of the
	 *                     namespace minus preceding backslash, but with
	 *                     succeeding backslash (Foo\)
	 * @return Rdm_CollectionManager
	 */
	public function __construct(Rdm_Config $config, $class_prefix = false)
	{
		$this->config = $config;
		$this->class_prefix = $class_prefix;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the configuration which is used by this CollectionManager.
	 * 
	 * @return Rdm_Config
	 */
	public function getConfig()
	{
		return $this->config;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Initializes the RapidDataMapper ORM, so it will create <Entity>Collection
	 * classes when needed.
	 * 
	 * @param  boolean  If to register the Rdm_Collection::pushChanges() method to
	 *                  run on shutdown.
	 * @return void
	 */
	public function registerCollectionAutoloader($auto_push = true)
	{
		spl_autoload_register(array($this, 'createCollectionClass'));
		
		$auto_push && register_shutdown_function(array($this, 'pushChanges'));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Loads the specific collection classes for entity objects, pattern of their
	 * classnames are <EntityClass>Collection or <EntityClass>CollectionBase.
	 * 
	 * @param  string
	 * @return boolean
	 */
	public function createCollectionClass($class)
	{
		if(substr($class, -10) !== 'Collection' && substr($class, -14) !== 'CollectionBase')
		{
			return false;
		}
		
		// Check for the prefix, so we don't try to map classes in the wrong namespace
		if( ! empty($this->class_prefix) && strstr($class, $this->class_prefix) === false)
		{
			return false;
		}
		
		// Remove "Base" if present
		if(substr($class, -4) === 'Base')
		{
			$class = substr($class, 0, -4);
		}
		
		// Check for a cached file
		if($this->config->getCacheMappers())
		{
			$dir = $this->config->getMapperCacheDir();
			
			if(file_exists($dir.DIRECTORY_SEPARATOR.$class.'.php'))
			{
				include $dir.DIRECTORY_SEPARATOR.$class.'.php';
				
				if(class_exists($class))
				{
					// Attach this CollectionManager to the newly instantiated class
					$this->attachToChild($class);
					
					return true;
				}
			}
		}
		
		// Remove "Collection"
		$entity_class = substr($class, 0, -10);
		
		$desc = $this->config->getDescriptor($entity_class);
		
		try
		{
			// Build the new class
			$builder = $desc->getBuilder();
		}
		catch(Exception $e)
		{
			// Handle errors, we cannot just let exceptions pass through,
			// because then autoload falls back on the other autoloaders
			// and ignores our exception, ultimately resulting in a missing
			// class error
			
			// Call the exception handler directly instead
			
			// Get the exception handler, use the dummy as an impostor so
			// we can convince PHP to lend us the current exception handler
			$eh = set_exception_handler(array($this, 'dummy'));
			// We must kill the impostor before he is found out!
			restore_exception_handler();
			
			if( ! $eh)
			{
				// We got a fake!
				
				// Now we have to try to fool the buyer...
				$this->triggerExceptionError($e);
			}
			else
			{
				// Now we execute the stolen handler!
				call_user_func($eh, $e);
			}
			
			// Let's leave before it blows up!
			exit;
		}
		
		// Do we write a compiled file?
		if($this->config->getCacheMappers())
		{
			// write the precompiled file
			$res = @file_put_contents($this->config->getMapperCacheDir().'/'.$class.'.php', '<?php
/*
 * Generated by RapidDataMapper on '.date('Y-m-d H:i:s').'.
 * 
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

'.$builder->__toString());
			
			// did the write work?
			if( ! $res)
			{
				// we need to tell the user that he needs to make the folder writable,
				// therefore he will know why it is slow
				trigger_error(sprintf('RapidDataMapper: Cannot write to the "%s" directory, using eval() instead.', $this->config->getMapperCacheDir()), E_USER_WARNING);
				
				// eval the code in case it didn't get written
				eval($builder->__toString());
			}
			else
			{
				require $this->config->getMapperCacheDir().'/'.$class.'.php';
			}
		}
		else
		{
			eval($builder->__toString());
		}
		
		// Attach this CollectionManager to the newly instantiated class
		$this->attachToChild($class);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Attaches this CollectionManager to the supplied collection class.
	 * 
	 * @param  string
	 * @return void
	 */
	public function attachToChild($class)
	{
		call_user_func(array($class, 'setCollectionManager'), $this);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * This is a fake exception printer, it will raise a fatal error with the
	 * exception formatted as the default PHP printer.
	 * 
	 * @param  Exception
	 * @return void
	 */
	protected function triggerExceptionError($exception)
	{
		$message = 'Uncaught exception \''.get_class($exception).'\' with message \''.$exception->getMessage().'\' in '.$exception->getFile().'::'.$exception->getLine().'
Stack trace:
'.$exception->getTraceAsString().'
  thrown in '.$exception->getFile().' on line '.$exception->getLine().'
  faked';
		
		// Trigger the error
		trigger_error($message, E_USER_ERROR);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Registers a class name as a collection object, used by pushChanges() to
	 * get all collections' unit of work objects.
	 * 
	 * @param  string
	 * @param  array   A list of entity class names which $class_name depends on
	 * @return void
	 */
	public function registerCollectionClassName($class_name, array $dependencies = array())
	{
		$this->dependencies[$class_name] = $dependencies;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Pushes all changes to the database.
	 * 
	 * @return void
	 */
	public function pushChanges()
	{
		$db = $this->config->getAdapter();
		$units = array();
		
		if(empty($this->commit_order_calculator))
		{
			$this->commit_order_calculator = new Rdm_UnitOfWork_CommitOrderCalculator($this->dependencies);
		}
		else
		{
			// Update the dependencies
			$this->commit_order_calculator->dependencies = $this->dependencies;
		}
		
		// Get the unit of works from the loaded collections
		foreach($this->commit_order_calculator->calculate() as $c)
		{
			$units[] = call_user_func($c.'::getUnitOfWork');
		}
		
		if($db->transactionInProgress())
		{
			// We already have a transaction, do not create another
			
			$this->doPushes($units);
		}
		else
		{
			// Nope, create a new local transaction
			try
			{
				$db->transactionStart();
				
				$this->doPushes($units);
				
				// Done!
				$db->transactionCommit();
			}
			catch(Exception $e)
			{
				// Oops, error, reset objects now
				foreach($units as $u)
				{
					$u->reset();
				}
				
				// Rethrow
				throw $e;
			}
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Pushes the contents of the unit of works in the supplied list.
	 * 
	 * @param  array(Rdm_UnitOfWork)
	 * @return void
	 */
	public function doPushes(array $units)
	{
		foreach($units as $u)
		{
			$u->prepare();
		}
		
		// Insert and update parents before children
		foreach($units as $u)
		{
			$u->doInserts();
			$u->doUpdates();
		}
		
		// DELETE queries must be reversed because we need to remove the children
		// before the parent
		foreach(array_reverse($units) as $u)
		{
			$u->doDeletes();
		}
		
		// All done, now we clean up
		foreach($units as $u)
		{
			$u->cleanUp();
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Dummy method meant as a replacement for the current exception handler.
	 * 
	 * @return void
	 */
	public static function dummy()
	{
		// Intentionally left empty
	}
}


/* End of file CollectionManager.php */
/* Location: ./lib/Rdm */