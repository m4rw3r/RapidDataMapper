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
		self::initRDM();
		
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
	 * A loader for the data objects and their descriptors.
	 * 
	 * @param  string
	 * @return void
	 */
	public static function load_data_object($class)
	{
		$class = strtolower($class);
		
		if(file_exists(APPPATH . 'data_model/'.$class.EXT))
		{
			require APPPATH . 'data_model/'.$class.EXT;
			
			return true;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Not supported by RapidDataMapper.
	 *
	 * @return void
	 */
	public function dbutil()
	{
		show_error('CodeIgniter\' dbutil is not supported by RapidDataMapper, use the table abstraction RapidDataMapper provides instead');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Not supported by RapidDataMapper.
	 *
	 * @return void
	 */
	public function dbforge()
	{
		show_error('CodeIgniter\' dbforge is not supported by RapidDataMapper, use the table abstraction RapidDataMapper provides instead');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the original Ci database instance.
	 * 
	 * @return CI_DB
	 */
	public function database_original()
	{
		static $instance;
		
		if($instance)
		{
			return $instance;
		}
		
		// Do we even need to load the database class?
		if( ! class_exists('CI_DB'))
		{
			require_once(BASEPATH.'database/DB'.EXT);
		}
		
		return DB('', true);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes the RapidDataMapper class.
	 * 
	 * @return void
	 */
	public static function initRDM()
	{
		static $defined;
		
		if($defined !== true)
		{
			// load the RapidDataMapper base
			require_once APPPATH.'libraries/Db.php';
			
			include APPPATH.'config/database.php';
			
			Db::setConnectionConfig($db);
			Db::setDefaultConnectionName($active_group);
			Db::setCompileMappers(isset($cache_mappers) ? $cache_mappers : false);
			Db::setMapperCacheDir(APPPATH.'mappercache');
			
			Db::initAutoload();
			
			// register a loader for the data objects and their descriptors
			spl_autoload_register(array('MY_Loader', 'load_data_object'));
			
			$defined = true;
		}
	}
}


/* End of file MY_Loader.php */
/* Location: ./compat/CodeIgniter/libraries */