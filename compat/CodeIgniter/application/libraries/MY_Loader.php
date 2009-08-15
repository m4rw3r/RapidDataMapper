<?php
/*
 * Created by Martin Wernståhl on 2009-04-18.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * An alternative loader for CodeIgniter, loading RapidDataMapper instead of the native database abstraction.
 */
class MY_Loader extends CI_Loader
{
	
	// ------------------------------------------------------------------------

	/**
	 * Loads the RapidDataMapper database abstraction.
	 * 
	 * @param  string
	 * @param  bool
	 * @param  bool
	 * @return Db_Connection|void
	 */
	public function database($params = '', $return = false, $active_record = false)
	{
		static $defined;
		
		if($defined !== true)
		{
			// load the RapidDataMapper base
			require_once APPPATH.'libraries/db.php';
			
			include APPPATH.'config/database.php';
			
			Db::setConnectionConfig($db);
			Db::setDefaultConnectionName($active_group);
			Db::setCompileMappers(isset($cache_mappers) ? $cache_mappers : false);
			if(isset($mapper_cache))
			{
				Db::setMapperCacheDir($mapper_cache);
			}
			
			Db::initAutoload();
			
			// let CodeIgniter handle the logging
			Db::attachLogger(array('MY_Loader', 'db_log_message'));
			
			// register a loader for the records
			spl_autoload_register(array('MY_Loader', 'load_record'));
			
			$defined = true;
		}
		
		// do we already have it instantiated?
		$CI = get_instance();
		if($return == false && isset($CI->db) && is_object($CI->db))
		{
			return false;
		}
		
		if($return)
		{
			return Db::getConnection($params);
		}
		
		$CI->db = '';
		
		$CI->db = Db::getConnection($params);
		
		$this->_ci_assign_to_models();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * A loader for the record objects.
	 * 
	 * @param  string
	 * @return void
	 */
	public static function load_record($class)
	{
		$class = strtolower($class);
		
		if(file_exists(APPPATH . 'records/'.$class.EXT))
		{
			require APPPATH . 'records/'.$class.EXT;
			
			return true;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Log adapter for RapidDataMapper, calls CodeIgniter's logging feature.
	 * 
	 * @param  mixed
	 * @param  string
	 * @return void
	 */
	public function db_log_message($severity, $message)
	{
		$severity = $severity == Db::WARNING ? 'warning' : 'error';
		
		log_message($severity, $message);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Not supported by RapidDataMapper.
	 *
	 * @return void
	 */
	public function dbutil()
	{
		show_error('CodeIgniter\' dbutil is not supported by RapidDataMapper, use the table abstraction ORM Tools provides instead');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Not supported by RapidDataMapper.
	 *
	 * @return void
	 */
	public function dbforge()
	{
		show_error('CodeIgniter\' dbforge is not supported by RapidDataMapper, use the table abstraction ORM Tools provides instead');
	}
}


/* End of file MY_Loader.php */
/* Location: ./compat/CodeIgniter/libraries */