<?php
/*
 * Created by Martin Wernståhl on 2010-04-05.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Collection_DeleteContentsOfCollectionOperation implements Rdm_UnitOfWork_CustomOperationInterface
{
	/**
	 * The collection which contents should be deleted.
	 * 
	 * @var Rdm_Collection
	 */
	protected $coll;
	
	/**
	 * The SELECT column string
	 * 
	 * @var string
	 */
	protected $select_code;
	
	/**
	 * The DELETE query, with missing (subquery) after "IN ".
	 * 
	 * @var string
	 */
	protected $delete_query;
	
	/**
	 * 
	 * 
	 * @param  Rdm_Collection  The collection whose contents are to be deleted
	 * @param  string  The columns which are to be selected to get the primary key
	 * @param  string  The incomplete delete query which should have a subquery added
	 *                 after an IN
	 */
	public function __construct(Rdm_Collection $coll, $select_code, $delete_query)
	{
		$this->coll = $coll;
		$this->select_code = $select_code;
		$this->delete_query = $delete_query;
	}
	
	// ------------------------------------------------------------------------
	
	public function isValid()
	{
		return ! $this->coll->isEmpty();
	}
	
	// ------------------------------------------------------------------------
	
	public function performOperation(Rdm_Adapter $db)
	{
		// Get the SELECT query
		$q = $this->coll->createSelectQuery();
		$q = $q[0];
		
		// Replace the SELECT part
		$q = substr($q, strpos($q, "\nFROM"));
		$q = 'SELECT '.$this->select_code.$q;
		
		// Add that as a subquery
		$q = $this->delete_query.'('.$q.')';
		
		// Execute
		$db->query($q);
	}
}


/* End of file DeleteContentsOfCollectionOperation.php */
/* Location: ./lib/Rdm/Collection */