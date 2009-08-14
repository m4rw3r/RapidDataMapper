<?php
/*
 * Created by Martin Wernståhl on 2009-08-10.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * An interface enforcing that the relation handlers have the correct methods.
 * 
 * TODO: Add more required methods
 */
interface Db_Descriptor_RelationInterface
{
	/**
	 * Just a constructor to make the object have a relation object (so it has something to grab data from).
	 */
	public function __construct(Db_Descriptor_Relation $rel);
	
	/**
	 * Adds extra conditions for this relation, ie. conditions which must be satisfied to allow a join.
	 * 
	 * These conditions will be added to the ON clause of the JOIN.
	 * 
	 * @param  string|array				The column name to filter by
	 * @param  string|int|double
	 * @return Db_Descriptor_Relation	The parent descriptor which was passed to the constructor
	 */
	public function setExtraConditions($property_name, $value = null);
	
	/**
	 * Returns the code which will add the join code to the designated query object.
	 * 
	 * Example code:
	 * <code>
	 * $q->join[] = "LEFT JOIN `users` AS `$alias_of_linked-user` ON `$alias_of_linked`.`id` = `$alias_of_linked-user`.`group_id`";
	 * $q->columns[] = "`$alias_of_linked-user`.`title` AS `$alias_of_linked-user__title`, `$alias_of_linked-user`.`group_id` AS `$alias_of_linked-user__group_id`";
	 * </code>
	 * 
	 * @param string
	 * @param string
	 * @param object|string
	 */
	public function getJoinRelatedCode($query_obj_var, $alias_of_linked_var);
	
	/**
	 * Returns the code which will establish a relationship between an object which will be saved and others.
	 * 
	 * @param  string
	 * @return object|string
	 */
	public function getPreSaveRelationCode($object_var);
	
	/**
	 * Returns the code which will establish a relationship between an object which is being inserted and the others.
	 * 
	 * @param  string
	 * @return object|string
	 */
	public function getSaveInsertRelationCode($object_var);
	
	/**
	 * Returns the code which will establish a relationship between an object which is being updated and the others.
	 * 
	 * @param  string
	 * @return object|string
	 */
	public function getSaveUpdateRelationCode($object_var);
}

/* End of file RelationInterface.php */
/* Location: ./lib/Db/Descriptor */