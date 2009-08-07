<?php
/*
 * Created by Martin Wernståhl on 2009-08-07.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A base class for the driver-specific database connections.
 */
abstract class Db_Connection
{
	
	/**
	 * The name of the configuration key used for the configuration.
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * The database hostname.
	 *
	 * @var string
	 */
	protected $hostname;
	
	/**
	 * The database username.
	 *
	 * @var string
	 */
	protected $username;
	
	/**
	 * The database password.
	 *
	 * @var string
	 */
	protected $password;
	
	/**
	 * The database name.
	 *
	 * @var string
	 */
	protected $database;
	
	/**
	 * The database prefix to use.
	 *
	 * @var string
	 */
	public $dbprefix = '';
	
	/**
	 * If to use persistent connections for database access.
	 *
	 * @var bool
	 */
	protected $pconnect;
	
	/**
	 * If to show error messages.
	 *
	 * @var bool
	 */
	public $db_debug = true;
	
	/**
	 * If to use caching.
	 *
	 * @var bool
	 */
	protected $cache_on = false;
	
	/**
	 * The database charset to use.
	 *
	 * @var string
	 */
	protected $cachedrv = 'file';
	
	/**
	 * The cache options.
	 *
	 * @var array
	 */
	protected $cacheopt = array();
	
	/**
	 * The database charset used by the connection.
	 *
	 * @var string
	 */
	protected $char_set = 'utf8';
	
	/**
	 * The database collation used by the connection.
	 *
	 * @var string
	 */
	protected $dbcollat = 'utf8_unicode_ci';
	
	/**
	 * If to redirect write queries and to which connection.
	 * 
	 * If this variable is not false, it will be treated as the
	 * name of the connection to redirect to.
	 * 
	 * @var false|string
	 */
	protected $redirect_write = false;
	
	/**
	 * The database handle.
	 * 
	 * @var resource
	 */
	protected $dbh;
	
	/**
	 * Stores the time for each query.
	 * If db_debug = true;
	 * 
	 * Links with the key to the queries array.
	 * 
	 * @var array
	 */
	public $query_times = array();
	
	/**
	 * Stores all the queries which has been run.
	 * If db_debug = true. 
	 *
	 * @var array
	 */
	public $queries = array();
	
	/**
	 * Stores the cache object used to cache queries.
	 *
	 * @var Db_Cache
	 */
	protected $cache_obj;
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @param string
	 * @param array
	 */
	public function __construct($conn_name, array $config)
	{
		foreach($config as $k => $v)
		{
			$this->$k = $v;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes the database handle.
	 * 
	 * @throws Db_Exception_ConnectionError
	 * 
	 * @return bool
	 */
	public function init_dbh()
	{
		if(is_null($this->dbh))
		{
			// not set, connect
			$this->dbh = $this->connect();
			
			if( ! $this->dbh)
			{	
				// failed connection, report
				Db::log(Ot_base::ERROR, 'Database connection error: "'.$this->error().'".');
				
				// yell at the coder
				throw new Ot_exception_ConnectError($this->error());
			}
			
			$this->set_charset($this->char_set, $this->dbcollat);
		}
		
		return true;
	}
	
	// --------------------------------------------------------------------
	// --  ABSTRACT METHODS                                              --
	// --------------------------------------------------------------------
	
	/**
	 * Connects to the database, using settings specified as properties in this object.
	 *
	 * Properties normally used:
	 * hostname
	 * username
	 * password
	 * pconnect (persistent connection? true/false)
	 * database (which database to select)
	 * 
	 * @return resource|false
	 */
	abstract protected function connect();
	/**
	 * Closes the database connection.
	 *
	 * Is it really needed? because PHP autocloses the connection
	 *
	 * @return void
	 */
	//abstract protected function close_conn();
	/**
	 * Sets the character set and collation to use for the connection.
	 *
	 * $this->dbh is loaded.
	 * 
	 * @param  string
	 * @param  string
	 * @return bool
	 */
	abstract protected function set_charset($charset, $collation);
	/**
	 * Returns the database version.
	 * 
	 * Uses a database query to check which database version is run.
	 * 
	 * @return string
	 */
	abstract public function version();
	/**
	 * Executes a chunk of SQL.
	 * 
	 * $this->dbh is loaded.
	 * 
	 * @param  string
	 * @return resource
	 */
	abstract protected function execute_sql($sql);
	/**
	 * Returns the error number and error message from the latest error.
	 *
	 * $this->dbh may not be loaded.
	 *
	 * FORMAT:
	 * err_num: message
	 * 
	 * @return string
	 */
	abstract public function error();
	/**
	 * Returns the id created by the Auto Increment column.
	 *
	 * $this->dbh may not be loaded.
	 * 
	 * @return int
	 */
	abstract public function insert_id();
	/**
	 * Returns the number of rows affected by the last query.
	 *
	 * $this->dbh may not be loaded.
	 * 
	 * @return int
	 */
	abstract public function affected_rows();
	/**
	 * Escapes a string for value usage in SQL.
	 * 
	 * $this->dbh may not be loaded.
	 * 
	 * @param  string
	 * @param  bool     If LIKE wildcards should be escaped (% and _)
	 * @return string
	 */
	abstract public function escape_str($str, $like = false);
	/**
	 * Returns a query which fetches the tables in the database.
	 *
	 * Preferably return the tables in the column TABLE_NAME.
	 * If the listing of tables isn't supported on this database, return an empty string.
	 * 
	 * @return string
	 */
	abstract protected function _list_tables();
	/**
	 * Creates the unique limit string for this driver.
	 *
	 * @param  string
	 * @param  int
	 * @param  int|bool
	 * @return string
	 */
	abstract public function _limit($str, $limit, $offset = false);
}


/* End of file Connection.php */
/* Location: ./lib/Db */