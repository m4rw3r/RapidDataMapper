<?php
/*
 * Created by Martin Wernståhl on 2010-04-09.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Object handling filter generation for Rdm_Collection objects.
 */
interface Rdm_Collection_FilterInterface
{
	/**
	 * Ends the current filter block.
	 * 
	 * @return Rdm_Collection|Rdm_Collection_FilterInterface
	 */
	public function end();
	
	// ------------------------------------------------------------------------

	/**
	 * Returns true if this filter does not contain values which are impossible
	 * to determine the exact value of (eg. id > 34).
	 * 
	 * @return boolean
	 */
	public function canModifyToMatch();
	
	// ------------------------------------------------------------------------

	/**
	 * Modifies the supplied object so that it matches the filter values in this
	 * filter.
	 * 
	 * @return 
	 */
	public function modifyToMatch($object);
	
	// ------------------------------------------------------------------------

	/**
	 * Renders the filter's WHERE part.
	 * 
	 * @return string
	 */
	public function __toString();
}


/* End of file FilterInterface.php */
/* Location: ./lib/Rdm/Collection */