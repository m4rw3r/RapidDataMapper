<?php
/*
 * Created by Martin Wernståhl on 2010-07-17.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
interface Rdm_UnitOfWork_CustomOperationInterface
{
	/**
	 * Should return true if this custom operation still should be run.
	 * 
	 * This is because some things might have changed since the operation
	 * was registered with the UnitOfWork,
	 * 
	 * @return boolean
	 */
	public function isValid();
	
	// ------------------------------------------------------------------------

	/**
	 * Runs the special SQL operation code.
	 * 
	 * @param  Rdm_Adapter
	 * @return void
	 */
	public function performOperation(Rdm_Adapter $db);
}


/* End of file CustomOperationInterface.php */
/* Location: ./lib/Rdm/UnitOfWork */