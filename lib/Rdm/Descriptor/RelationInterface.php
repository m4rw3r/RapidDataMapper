<?php
/*
 * Created by Martin Wernståhl on 2010-08-11.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
interface Rdm_Descriptor_RelationInterface
{
	
	// ------------------------------------------------------------------------

	/**
	 * 
	 * 
	 * @return 
	 */
	public function __construct(Rdm_Descriptor_Relation $rel);
	
	// ------------------------------------------------------------------------

	/**
	 * Tells if this relationship type is plural or not, ie. owns many or one.
	 * 
	 * @return boolean
	 */
	public function isPlural();
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of keys, with this side in the index 0 array, and the
	 * opposite side in the 1 index.
	 * 
	 * Example:
	 * <code>
	 * // a -> b, c -> d
	 * array(
	 *     array(Object a, Object c),
	 *     array(Object b, Object d)
	 *      );
	 * </code>
	 * 
	 * @return array(array(Rdm_Descriptor_Column), array(Rdm_Descriptor_Column))
	 */
	public function getKeys();
	
	// ------------------------------------------------------------------------

	/**
	 * Returns a list of code containers which are to be added to the
	 * UnitOfWork->establishRelationLinks() method.
	 * 
	 * @return array(Rdm_Util_Code_Container)
	 */
	public function getEstablishCodeParts();
	
	// ------------------------------------------------------------------------

	/**
	 * Returns the code part which will modify the object to match the RelationFilter.
	 * 
	 * @return Rdm_Util_Code_Container
	 */
	public function getModifyToMatchCode();
}

/* End of file RelationInterface.php */
/* Location: ./lib/Rdm/Descriptor */