<?php
/*
 * Created by Martin Wernståhl on 2009-03-13.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Db_Driver_Mysql_Connection extends Db_Connection
{
	
	protected $IDENT_CHAR = '`';
	
	protected function connect()
	{
		// try to connect
		$conn = mysql_connect($this->hostname, $this->username, $this->password, $this->pconnect);
		
		if( ! $conn)
		{
			throw new Db_Exception_ConnectionError($this->error());
		}
		else
		{
			// do we have a connection and can we select the database?
			if(mysql_select_db($this->database, $conn))
			{
				return $conn;
			}
			else
			{
				throw new Db_Exception_ConnectionError('Cannot select database "'.$this->database.'".');
			}
		}
	}
	
	// ------------------------------------------------------------------------

	protected function setCharset($charset, $collation)
	{
		// only raises warning if connection isn't connected, init_dbh() makes sure of that
		// and because execute_sql() is protected, we can ignore the checking for dbh here
		
		// set charset on client side
		mysql_set_charset($charset, $this->dbh);
		
		// set charset server side
		return mysql_query("SET NAMES '".$this->escapeStr($charset)."' COLLATE '".$this->escapeStr($collation)."'", $this->dbh);
	}
	
	// ------------------------------------------------------------------------

	protected function executeSql($sql)
	{
		// only raises warning if connection isn't connected, init_dbh() makes sure of that
		// and because execute_sql() is protected, we can ignore the checking for dbh here
		return mysql_query($sql, $this->dbh);
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
			return mysql_errno($this->dbh) . ": " . mysql_error($this->dbh);
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
			return mysql_insert_id($this->dbh);
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
			return mysql_affected_rows($this->dbh);
		}
	}
	
	// ------------------------------------------------------------------------
	
	public function version()
	{
		return $this->query("SELECT version() AS ver")->val();
	}

	// ------------------------------------------------------------------------

	public function escapeStr($str, $like = false)
	{
		if(function_exists('mysql_real_escape_string') && is_resource($this->dbh))
		{
			// escape with regard to charset
			$str = mysql_real_escape_string($str, $this->dbh);
		}
		elseif(function_exists('mysql_escape_string'))
		{
			// escape for MySQL
			$str = mysql_escape_string($str);
		}
		else
		{
			// generic
			$str = addslashes($str);
		}
		
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
		return 'SHOW TABLES FROM '.$this->protectIdentifiers($this->database);
	}
	
	// ------------------------------------------------------------------------

	public function _limit($str, $limit, $offset = false)
	{
		return $str . "\nLIMIT " . ($offset != false ?  $offset . ', ' : '') . $limit;
	}
}


/* End of file db.php */
/* Location: ./lib/drivers/mysql */