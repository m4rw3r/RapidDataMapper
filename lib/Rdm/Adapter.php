<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class which interacts with the database, connection specific code is provided
 * by subclasses.
 */
abstract class Rdm_Adapter
{
	/**
	 * The database prefix to use.
	 *
	 * @var string
	 */
	public $dbprefix = '';
	
	/**
	 * If to use caching.
	 *
	 * @var bool
	 */
	protected $cache_on = false;
	
	/**
	 * If to redirect write queries and to which connection instance.
	 * 
	 * @var Rdm_Adapter|false
	 */
	protected $redirect_write = false;
	
	/**
	 * The database handle.
	 * 
	 * @var resource
	 */
	protected $dbh;
	
	/**
	 * If a transaction is on.
	 * 
	 * @var bool
	 */
	protected $transaction = false;
	/**
	 * Stores all the queries which has been run.
	 *
	 * @var array('sql' => SQL, 'time' => time in seconds)
	 */
	public $queries = array();
	
	/**
	 * Stores the cache object used to cache queries.
	 *
	 * @var Rdm_Cache
	 */
	protected $cache_obj;
	
	/**
	 * The class name of the result objects.
	 * 
	 * @var string
	 */
	protected $result_object_class;
	
	/**
	 * The character to use as a delimiter for the SQL identifiers.
	 * 
	 * Overwrite in child class to provide another character.
	 * 
	 * @var string
	 */
	protected $IDENT_CHAR = '"';
	
	/**
	 * Specific type mappings for this database adapter.
	 * 
	 * Overrides Rdm_Descriptor::$type_mappings if they have the same key.
	 * 
	 * @var array(RDM_type => classname)
	 */
	public $type_mappings = array();
	
	/**
	 * Array containing all the options for the database connection.
	 * 
	 * @var array(string => string)
	 */
	protected $options = array();
	
	// ------------------------------------------------------------------------

	/**
	 * Constructor, validates the configuration options passed to the adapter instance.
	 * 
	 * @param  array(string => string)
	 */
	public function __construct(array $options)
	{
		if($req = array_diff($this->getRequiredOptionKeys(), array_keys($options)))
		{
			throw Rdm_Adapter_ConfigurationException::missingOptions($req);
		}
		
		// Merge with the default options
		$this->options = array_merge($this->getDefaultOptions(), $options);
		
		// Set properties
		$this->redirect_write = $this->options['redirect_write'];
		$this->dbprefix       = $this->options['dbprefix'];
		$this->cache_on       = $this->options['cache_on'];
		
		if(is_string($this->redirect_write) OR is_object($this->redirect_write) && ! $this->redirect_write instanceof self)
		{
			throw Rdm_Adapter_ConfigurationException::redirectWriteFaultyParameter($this->redirect_write);
		}
		
		$this->result_object_class = get_class($this).'_Result';
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Do not allow cloning.
	 * 
	 * Only one connection per configuration should exist.
	 * 
	 * @throws Exception
	 */
	public final function __clone()
	{
		throw new Exception('Cloning of Rdm_Adapter instances are not allowed.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Do not allow serialization.
	 * 
	 * @throws Exception
	 */
	public final function __sleep()
	{
		throw new Exception('Serialization of Rdm_Adapter objects are not allowed.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Do not allow unserialization.
	 * 
	 * @throws Exception
	 */
	public final function __wakeup()
	{
		throw new Exception('Unserialization of Rdm_Adapter objects are not allowed.');
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Sets the adapter which should be used to redirect all the write queries to,
	 * works like the option redirect_write.
	 * 
	 * @param  Rdm_Adapter|null  Null to remove the write redirection
	 * @return void
	 */
	public function setWriteAdapter(Rdm_Adapter $adapter = null)
	{
		$this->redirect_write = $adapter;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Initializes the database handle.
	 * 
	 * @throws Rdm_Adapter_ConnectionException
	 * @return bool
	 */
	public function initDbh()
	{
		if(is_null($this->dbh))
		{
			// Not set, connect
			$this->dbh = $this->connect();
			
			$this->setCharset($this->options['char_set'], $this->options['dbcollat']);
		}
		
		return true;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Runs the query and returns the result object.
	 * 
	 * @see Rdm_Adapter::bindParameters()
	 * @throws Rdm_Adapter_QueryException
	 * 
	 * @param  string
	 * @param  array
	 * @return Rdm_Adapter_Result
	 */
	public function query($sql, $parameters = array())
	{
		if(empty($sql))
		{
			throw Rdm_Adapter_QueryException::emptyQuery();
		}
		
		if( ! empty($parameters))
		{
			$sql = $this->bindParameters($sql, $parameters);
		}
		
		$is_write = $this->isWriteQuery($sql);
		
		// Write query redirection
		if($this->redirect_write && $is_write)
		{
			// $this->redirect_write is the database instance
			return $this->redirect_write->query($sql);
		}
		
		// Is cache on, and is it a read query?
		if($this->cache_on && ! $is_write)
		{
			// Start cache
			$c = $this->getCache();
			
			try // ...getting data from cache
			{
				$ret = $c->fetch($sql);
				
				return $ret;
			}
			catch(Rdm_Cache_NoValueException $e)
			{
				// Just continue
			}
		}
		
		is_null($this->dbh) && $this->initDbh();
		
		// Get query start time
		$start = microtime(true);
		
		if( ! $resource = $this->executeSql($sql))
		{
			// Failed query, log
			$this->queries[] = array('sql' => $sql, 'time' => false);
			
			throw Rdm_Adapter_QueryException::queryError($sql, $this->errorMsg(), $this->errorNo());
		}
		
		if($this->cache_on && $is_write)
		{
			// TODO: push changes to the cache
		}
		
		// Log the query
		$this->queries[] = array('sql' => $sql, 'time' => microtime(true) - $start);
		
		// Create result to return
		$class = $this->result_object_class;
		$result = new $class($this->dbh, $resource);
		
		// Is it a write query?
		if($is_write)
		{
			// Yes, only return the number of affected rows
			return $result->affectedRows();
		}
		
		if($this->cache_on && ! $is_write)
		{
			// Write to the cache
			$this->getCache()->store($sql, $result->dump());
		}
		
		return $result;
	}
	
	// --------------------------------------------------------------------
	// --  TRANSACTION METHODS                                           --
	// --------------------------------------------------------------------
	
	/**
	 * Starts a transaction.
	 * 
	 * @return bool
	 */
	public function transactionStart()
	{
		// Redirect transaction start to the write instance
		if($this->redirect_write)
		{
			return $this->redirect_write->transactionStart();
		}
		
		if($this->transaction)
		{
			throw new Rdm_Adapter_TransactionNestingException();
		}
		
		// Load database handle to be able to start a transaction
		is_null($this->dbh) && $this->initDbh();
		
		if($this->startTransaction())
		{
			$this->transaction = true;
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Commits a transaction.
	 * 
	 * @return bool
	 */
	public function transactionCommit()
	{
		// Redirect transaction commit to the write instance
		if($this->redirect_write)
		{
			return $this->redirect_write->transactionCommit();
		}
		
		is_null($this->dbh) && $this->initDbh();
		
		$this->transaction = false;
		
		return $this->commitTransaction();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Rolls back a transaction.
	 * 
	 * @return bool
	 */
	public function transactionRollback()
	{
		// Redirect transaction rollback to the write instance
		if($this->redirect_write)
		{
			return $this->redirect_write->transactionRollback();
		}
		
		is_null($this->dbh) && $this->initDbh();
		
		$this->transaction = false;
		
		return $this->rollbackTransaction();
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if a transaction already is in progress.
	 * 
	 * @return boolean
	 */
	public function transactionInProgress()
	{
		// Redirect transaction check to the write instance
		if($this->redirect_write)
		{
			return $this->redirect_write->transactionInProgress();
		}
		
		return $this->transaction;
	}
	
	// --------------------------------------------------------------------
	// --  SQL-BUILDER INTERFACING METHODS                               --
	// --------------------------------------------------------------------
	
	/**
	 * Creates a SELECT query object.
	 *
	 * @see Rdm_Query_Select 
	 *
	 * @param  string|array
	 * @return Rdm_Query_Select
	 */
	public function select($columns = false)
	{
		$q = new Rdm_Query_Select($this, false);
		
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
	 * @return Rdm_Query_Insert|int|false
	 */
	public function insert($table, $data = false)
	{
		$ret = new Rdm_Query_Insert($this, $table);
		
		// If we have data, perform the insert
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
	 * @see Rdm_Query_Update
	 * @see Rdm_Query_Update::set()
	 * @see Rdm_Query_Abstract::where()
	 *
	 * @param  string|array     Multiple tables can be updated with the same query
	 * @param  array            Associative array with new data (sent to set())
	 * @param  mixed            Sent to Rdm_Query_Abstract::where()
	 * @return Rdm_Query_Update|int|false
	 */
	public function update($table, $data = false, $conditions = false)
	{
		$ret = new Rdm_Query_Update($this, $table);
		
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
	 * @see Rdm_Query_Delete
	 * @see Rdm_Query_Abstract::where()
	 * 
	 * @param  string|array
	 * @param  mixed        Sent to Rdm_Query_Abstract::where()
	 * @return Rdm_Query_Delete|int|false
	 */
	public function delete($table, $conditions = false)
	{
		$ret = new Rdm_Query_Delete($this, $table);
		
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
				$value = $this->escapeBoolean($value);
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
		return (bool) preg_match('/^\s*"?(INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|SET|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Replaces all the bound parameters with the values in the bind array.
	 * 
	 * ! ATTENTION !:
	 * - Identifiers will not be protected in bound statement!
	 * - Only the bound data will be escaped!
	 * - Escaping of values for LIKE condition does not escape % and _ !
	 *
	 * @throws Rdm_Adapter_MissingBoundParameterException
	 *
	 * @param  string
	 * @param  array
	 * @return string
	 */
	public function bindParameters($sql, $parameters = array())
	{
		$parameters = (Array) $parameters;
		
		// Named parameters
		if(preg_match_all('/:([\w]+)(?=\s|$)/', $sql, $matches))
		{
			$result = '';
			
			// Replace the named parameters
			foreach($matches[1] as $id)
			{
				if( ! isset($parameters[$id]))
				{
					throw new Rdm_Adapter_MissingBoundParameterException($id);
				}
				
				// Add the part before the name and then the escaped data
				$result .= strstr($sql, ':' . $id, true) . $this->escape($parameters[$id]);
				// Make $sql contain the next to match, this to prevent matching in the previously escaped data
				$sql = substr($sql, strpos($sql, ':' . $id) + strlen($id) + 1);
			}
			
			// Assemble
			$res = $result . $sql;
		}
		// Unnamed bound parameters
		else
		{
			// Split the condition
			$parts = explode('?', $sql);
			$c = count($parts) - 1;
			
			if($c > count($parameters))
			{
				throw new Rdm_Adapter_MissingBoundParameterException($c);
			}
			
			$res = '';
			
			// Insert the parameters
			for($i = 0; $i < $c; $i++)
			{
				$res .= $parts[$i] . $this->escape($parameters[$i]);
			}
			
			// Add the last part
			$res .= $parts[$i];
		}
		
		return $res;
	}
	
	// --------------------------------------------------------------------
	// --  ABSTRACT METHODS                                              --
	// --------------------------------------------------------------------
	
	/**
	 * Returns a hash containing the default contents of the $this->options
	 * array.
	 * 
	 * @return array(string => mixed)
	 */
	protected function getDefaultOptions()
	{
		return array(
			'char_set'       => 'utf8',
			'dbcollat'       => 'utf8_unicode_ci',
			'redirect_write' => false,
			'dbprefix'       => '',
			'cache_class'    => '',  // TODO: Set the default cache class
			'cache_on'       => false,
			'cache_opts'     => array()
			);
	}
	/**
	 * Returns a list of the required keys which must go in the supplied options.
	 * 
	 * @return array(string)
	 */
	abstract protected function getRequiredOptionKeys();
	/**
	 * Connects to the database, using settings specified as properties in this object.
	 *
	 * Options normally used ($this->options):
	 * hostname
	 * username
	 * password
	 * pconnect (persistent connection? true/false)
	 * database (which database to select)
	 * 
	 * @throws Rdm_Adapter_ConnectionException
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
	 * Starts a transaction.
	 * 
	 * @return bool
	 */
	abstract protected function startTransaction();
	/**
	 * Commits a transaction.
	 * 
	 * @return bool
	 */
	abstract protected function commitTransaction();
	/**
	 * Rolls back a transaction.
	 * 
	 * @return bool
	 */
	abstract protected function rollbackTransaction();
	/**
	 * Returns the error message from the server.
	 * 
	 * $this->dbh may not be loaded.
	 *
	 * @return string
	 */
	abstract function errorMsg();
	/**
	 * Returns the error number from the server.
	 * 
	 * $this->dbh may not be loaded.
	 * 
	 * @return int
	 */
	abstract function errorNo();
	/**
	 * Returns the id created by the Auto Increment column.
	 *
	 * $this->dbh may not be loaded.
	 * 
	 * @return int
	 */
	abstract public function insertId();
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
	abstract public function limitSqlQuery($str, $limit, $offset = false);
	/**
	 * Returns the SQL keyword for random ordering.
	 * 
	 * @return string
	 */
	abstract public function getRandomKeyword();
	/**
	 * Converts a PHP boolean to a database specific boolean.
	 * 
	 * @param  bool
	 * @return mixed
	 */
	abstract public function escapeBoolean($bool);
	/**
	 * Converts a database boolean to a PHP boolean.
	 * 
	 * @param  mixed
	 * @return bool
	 */
	abstract public function unescapeBoolean($bool);
}


/* End of file Adapter.php */
/* Location: ./lib/Rdm */