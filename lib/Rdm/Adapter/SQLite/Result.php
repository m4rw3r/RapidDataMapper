<?php
/*
 * Created by Martin Wernståhl on 2010-04-23.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

class Rdm_Adapter_SQLite_Result extends Rdm_Adapter_Result
{
	public function count()
	{
		return isset($this->num_rows) ? $this->num_rows : $this->num_rows = @sqlite_num_rows($this->resource);
	}
	
	public function affectedRows()
	{
		// $this->dbh may not be loaded
		// If condition is very much faster than error suppression with @
		if( ! $this->dbh)
		{
			return false;
		}
		else
		{
			return sqlite_changes($this->dbh);
		}
	}
	
	public function seek($n)
	{
		return ($n >= $this->count()) ? false : sqlite_seek($this->resource, $n);
	}
	
	public function freeResult()
	{
		if (is_resource($this->resource))
		{
			$this->resource = FALSE;
		}
	}
	
	public function next()
	{
		return sqlite_fetch_object($this->resource);
	}

	public function nextHash()
	{
		return sqlite_fetch_array($this->resource, SQLITE_ASSOC);
	}
	
	public function nextArray()
	{
		return sqlite_fetch_array($this->resource, SQLITE_NUM);
	}
	
	public function metadata()
	{
		// TODO: Implement SQLite column metadata reader
		return array();
	}
}

/* End of file Result.php */
/* Location: ./lib/Rdm/Adapter/SQLite */