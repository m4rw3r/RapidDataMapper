<?php
/*
 * Created by Martin Wernståhl on 2010-04-23.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Adapter_SQLite extends Rdm_Adapter
{
	/**
	 * Variable holding the last error caused by a query.
	 * 
	 * @var string
	 */
	protected $last_query_error = '';
	
	protected $IDENT_CHAR = '"';
	
	protected function getRequiredOptionKeys()
	{
		return array('file');
	}
	
	protected function connect()
	{
		// try to connect
		$conn = sqlite_open($this->options['file'], 0666, $this->last_query_error);
		
		if( ! $conn)
		{
			throw new Rdm_Adapter_ConnectionException($error());
		}
		else
		{
			return $conn;
		}
	}
	
	// ------------------------------------------------------------------------

	protected function setCharset($charset, $collation)
	{
		// TODO: Does SQLite have a way to set charset?
		
		return true;
	}
	
	// ------------------------------------------------------------------------

	protected function executeSql($sql)
	{
		// only raises warning if connection isn't connected, init_dbh() makes sure of that
		// and because execute_sql() is protected, we can ignore the checking for dbh here
		return sqlite_query($sql, $this->dbh, SQLITE_BOTH, $this->last_query_error);
	}
	
	// ------------------------------------------------------------------------

	protected function startTransaction()
	{
		return sqlite_exec('BEGIN TRANSACTION', $this->dbh);
	}
	
	// ------------------------------------------------------------------------

	protected function commitTransaction()
	{
		return sqlite_exec('COMMIT', $this->dbh);
	}
	
	// ------------------------------------------------------------------------

	protected function rollbackTransaction()
	{
		return sqlite_exec('ROLLBACK', $this->dbh);
	}
	
	// ------------------------------------------------------------------------

	public function error()
	{
		// dbh may not be loaded
		// if condition is very much faster than error suppression with @
		if( ! $this->dbh)
		{
			return "Database connection has not been established, error cannot be retrieved.";
		}
		else
		{
			// TODO: Check error messages, they seem to be lacking sometimes
			return ($e = sqlite_last_error($this->dbh)) . ": " . sqlite_error_string($e).$this->last_query_error;
		}
	}
	
	// ------------------------------------------------------------------------

	public function insertId()
	{
		// dbh may not be loaded
		// if condition is very much faster than error suppression with @
		// (about 11% when dbh exists, otherwise over 90%)
		if( ! $this->dbh)
		{
			return false;
		}
		else
		{
			return sqlite_last_insert_rowid($this->dbh);
		}
	}
    
	// ------------------------------------------------------------------------
    
	public function affectedRows()
	{
		// dbh may not be loaded
		// if condition is very much faster than error suppression with @
		if( ! $this->dbh)
		{
			return false;
		}
		else
		{
			return sqlite_changes($this->dbh);
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function version()
	{
		return sqlite_libversion();
	}

	// ------------------------------------------------------------------------

	public function escapeStr($str, $like = false)
	{
		$str = sqlite_escape_string($str);
		
		if($like)
		{
			// replace LIKE-wildcards
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}
		
		return $str;
	}
	
	// ------------------------------------------------------------------------
	
	function _listTables()
	{
		// TODO: Check for SQLite compliance
		return 'SHOW TABLES FROM '.$this->protectIdentifiers($this->database);
	}
	
	// ------------------------------------------------------------------------

	public function limitSqlQuery($str, $limit, $offset = false)
	{
		// TODO: Check for SQLite compliance
		return $str . "\nLIMIT " . ($offset != false ?  $offset . ', ' : '') . $limit;
	}
	
	// ------------------------------------------------------------------------

	public function escapeBoolean($bool)
	{
		// TODO: Check for SQLite compliance
		return $bool ? '1' : '0';
	}
	
	// ------------------------------------------------------------------------

	public function unescapeBoolean($bool)
	{
		// TODO: Check for SQLite compliance
		return $bool == '1' ? true : false;
	}
	
	// ------------------------------------------------------------------------

	public function getRandomKeyword()
	{
		// TODO: Check for SQLite compliance
		return 'RAND()';
	}
}


/* End of file SQLite.php */
/* Location: ./lib/Rdm/Adapter */