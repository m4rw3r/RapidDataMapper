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
	protected $name;
	
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
	 * 
	 * Links with the key to the queries array.
	 * 
	 * @var array
	 */
	public $query_times = array();
	
	/**
	 * Stores all the queries which has been run.
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
	
	/**
	 * The class name of the result objects.
	 * 
	 * @var string
	 */
	protected $result_object_class;
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @param string
	 * @param array
	 */
	public function __construct($conn_name, array $config)
	{
		$this->name = $conn_name;
		
		foreach($config as $k => $v)
		{
			$this->$k = $v;
		}
		
		$this->result_object_class = str_replace('_Connection', '', get_class($this)).'_Result';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the name of the configuration used to configure this connection object.
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes the database handle.
	 * 
	 * @throws Db_Exception_ConnectionError
	 * 
	 * @return bool
	 */
	public function initDbh()
	{
		if(is_null($this->dbh))
		{
			// not set, connect
			$this->dbh = $this->connect();
			
			if( ! $this->dbh)
			{	
				// failed connection, report
				Db::log(Db::ERROR, 'Database connection error: "'.$this->error().'".');
				
				// yell at the coder :P
				throw new Db_Exception_ConnectionError($this->error());
			}
			
			$this->setCharset($this->char_set, $this->dbcollat);
		}
		
		return true;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Runs the query and returns the result object.
	 * 
	 * @see Db_Connection::replace_binds()
	 * @throws Db_Exception_MissingBindParameter|Db_Exception_ConnectionError
	 * 
	 * @param  string
	 * @param  array
	 * @return Db_Result
	 */
	public function query($sql, $binds = array())
	{
		if(empty($sql))
		{
			Db::log(Db::ERROR, 'Invalid query, the query is empty');
			
			throw new Db_Exception_QueryError('Invalid query, the query is empty');
			
			return false;
		}
		
		if( ! empty($binds))
		{
			$sql = $this->replaceBinds($sql, $binds);
		}
		
		$is_write = $this->isWriteQuery($sql);
		
		// write query redirection
		if($this->redirect_write && $is_write)
		{
			return Db::getConnection($this->redirect_write)->query($sql);
		}
		
		// is cache on, and is it a read query?
		if($this->cache_on && ! $is_write)
		{
			// start cache
			$c = $this->getCache();
			
			try // ...getting data from cache
			{
				$ret = $c->fetch($sql);
				
				return $ret;
			}
			catch(Db_Exception_Cache_NoValue $e)
			{
				// just continue
			}
		}
		
		if( ! $this->initDbh())
		{
			return false;
		}
		
		// log
		$this->queries[] = $sql;
		$start = microtime(true);
		
		if( ! $resource = $this->executeSql($sql))
		{
			// failed query, log
			$query_times[] = false;
			Db::log(Db::ERROR, 'Query error: SQL: "' . $sql . '", error: ' . $this->error());
			
			throw new Db_Exception_QueryError('ERROR: '.$this->error().', SQL: "'.$sql.'"');
			
			return false;
		}
		
		if($this->cache_on && $is_write)
		{
			// TODO: push changes to the cache
		}
		
		$this->query_times[] = microtime(true) - $start;
		
		// is it a write query?
		if($is_write)
		{
			return $this->affectedRows();
		}
		
		// create result to return
		$class = $this->result_object_class;
		$result = new $class($this->dbh, $resource);
		
		if($this->cache_on && ! $is_write)
		{
			// write to the cache
			$this->getCache()->store($sql, $result->dump());
		}
		
		return $result;
	}
	
	// --------------------------------------------------------------------
	// --  SQL-BUILDER INTERFACING METHODS                               --
	// --------------------------------------------------------------------
	
	/**
	 * Creates a SELECT query object.
	 *
	 * @see Db_Query_Select 
	 *
	 * @param  string|array
	 * @return Db_Query_Select
	 */
	public function select($columns = false)
	{
		$q = new Db_Query_Select($this, false);
		
		if($columns)
		{
			$q->column($columns);
		}
		
		return $q;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Inserts data into the database, returns a query object if no data is supplied.
	 * 
	 * @see Ot_query_Insert
	 * @see Ot_query_Insert::set()
	 * 
	 * @param  string
	 * @param  array   Associative array with column => value, executes the query if present
	 * @return Db_Query_Insert|int|false
	 */
	public function insert($table, $data = false)
	{
		$ret = new Db_Query_Insert($this, $table);
		
		// if we have data, perform the insert
		if($data)
		{
			$ret->set($data);
			
			return $ret->execute();
		}
		else
		{
			return $ret;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Updates data in the database, returns a query object if no conditions are supplied.
	 *
	 * @see Db_Query_Update
	 * @see Db_Query_Update::set()
	 * @see Db_Query::where()
	 *
	 * @param  string|array 	Multiple tables can be updated with the same query
	 * @param  array    		Associative array with new data (sent to set())
	 * @param  mixed			Sent to ot_query_update::where()
	 * @return Db_Query_Update|int|false
	 */
	public function update($table, $data = false, $conditions = false)
	{
		$ret = new Db_Query_Update($this, $table);
		
		if($data)
		{
			$ret->set($data);
		}
		
		if($conditions)
		{
			$ret->where($conditions);
			
			return $ret->execute();
		}
		else
		{
			return $ret;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Deletes data in the database, returns a query object if no conditions are supplied.
	 *
	 * @see Db_Query_Delete
	 * @see Db_Query::where()
	 * 
	 * @param  string|array
	 * @param  mixed		Sent to Db_Query::where()
	 * @return Db_Query_Delete|int|false
	 */
	public function delete($table, $conditions = false)
	{
		$ret = new Db_Query_Delete($this, $table);
		
		if($conditions)
		{
			$ret->where($conditions);
			
			return $ret->execute();
		}
		else
		{
			return $ret;
		}
	}
	
	// --------------------------------------------------------------------
	// --  SQL UTILITY METHODS                                           --
	// --------------------------------------------------------------------

	/**
	 * Escapes the supplied value for usage in SQL queries.
	 * 
	 * @param  mixed
	 * @return string
	 */
	public function escape($value)
	{
		switch(gettype($value))
		{
			case 'NULL':
				$value = 'NULL';
				break;
				
			case 'boolean':
				$value = $value ? $this->BOOLEAN_CHARS[0] : $this->BOOLEAN_CHARS[1];
				break;
			
			case 'integer':
			case 'double':
				break;
			
			default:
				$value = '\''.$this->escapeStr($value).'\'';
				break;
		}
		
		return $value;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Protects all identifiers (ie. all words) in the supplied string.
	 * 
	 * @param  string
	 * @return string
	 */
	public function protectIdentifiers($item)
	{
		return trim(preg_replace('/([\w-\$]+)/', $this->IDENT_CHAR.'$1'.$this->IDENT_CHAR, $item));
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Prefixes the received string with the database prefix.
	 * 
	 * @param  string
	 * @return string
	 */
	public function prefix($str = '')
	{
		return $this->dbprefix . $str;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Determines if the query is a write query.
	 * 
	 * @param  string
	 * @return bool
	 */
	public function isWriteQuery($sql)
	{
		if( ! preg_match('/^\s*"?(INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|SET|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql))
		{
			return false;
		}
		
		return true;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Replaces all the bound parameters with the values in the bind array.
	 * 
	 * !ATTENTION!:
	 * Identifiers will not be protected in bound statement!
	 * Only the bound data will be escaped!
	 * Escaping of values for LIKE condition does not escape % and _ !
	 *
	 * @throws Db_Exception_MissingBindParameter
	 *
	 * @param  string
	 * @param  array
	 * @return string
	 */
	public function replaceBinds($sql, $binds = array())
	{
		$binds = (Array) $binds;
		
		// named binds
		if(preg_match_all('/:([\w]+)(?=\s|$)/', $sql, $matches))
		{
			$result = '';
			
			// replace the named binds
			foreach($matches[1] as $id)
			{
				if( ! isset($binds[$id]))
				{
					throw new Db_exception_MissingBindParameter($id);
				}
				
				// add the part before the name and then the escaped data
				$result .= strstr($sql, ':' . $id, true) . $this->escape($binds[$id]);
				// make $sql contain the next to match, this to prevent matching in the previously escaped data
				$sql = substr($sql, strpos($sql, ':' . $id) + strlen($id) + 1);
			}
			
			// assemble
			$res = $result . $sql;
		}
		// unnamed binds
		else
		{
			// split the condition
			$parts = explode('?', $sql);
			$c = count($parts) - 1;
			
			if($c > count($binds))
			{
				throw new Db_Exception_MissingBindParameter($c);
			}
			
			$res = '';
			
			// insert the binds
			for($i = 0; $i < $c; $i++)
			{
				$res .= $parts[$i] . $this->escape($binds[$i]);
			}
			
			// add the last part
			$res .= $parts[$i];
		}
		
		return $res;
	}
	
	// --------------------------------------------------------------------
	// --  CACHING METHODS                                               --
	// --------------------------------------------------------------------

	/**
	 * Activates caching.
	 * 
	 * @return self
	 */
	public function cacheOn()
	{
		$this->cache_on = true;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Deactivates caching.
	 * 
	 * @return self
	 */
	public function cacheOff()
	{
		$this->cache_on = false;
		
		return $this;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Returns the cache object.
	 * 
	 * @return Db_Cache
	 */
	public function getCache()
	{
		if( ! $this->cache_on)
		{
			return false;
		}
		
		if(isset($this->cache_obj))
		{
			return $this->cache_obj;
		}
		
		$class = 'Db_Cache_'.$this->cachedrv;
		
		$this->cache_obj = new $class($this->cacheopt);
		
		return $this->cache_obj;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets a caching object to use.
	 * 
	 * @return self
	 */
	public function setCache(Db_Cache $cache_object)
	{
		$this->cache_obj = $cache_object;
		
		return $this;
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
	abstract protected function setCharset($charset, $collation);
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
	abstract protected function executeSql($sql);
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
	abstract public function insertId();
	/**
	 * Returns the number of rows affected by the last query.
	 *
	 * $this->dbh may not be loaded.
	 * 
	 * @return int
	 */
	abstract public function affectedRows();
	/**
	 * Escapes a string for value usage in SQL.
	 * 
	 * $this->dbh may not be loaded.
	 * 
	 * @param  string
	 * @param  bool     If LIKE wildcards should be escaped (% and _)
	 * @return string
	 */
	abstract public function escapeStr($str, $like = false);
	/**
	 * Returns a query which fetches the tables in the database.
	 *
	 * Preferably return the tables in the column TABLE_NAME.
	 * If the listing of tables isn't supported on this database, return an empty string.
	 * 
	 * @return string
	 */
	abstract protected function _listTables();
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