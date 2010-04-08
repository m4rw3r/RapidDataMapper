<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains specific logic for the Belongs To relationship type.
 */
class Rdm_Descriptor_Relation_BelongsTo extends Rdm_Descriptor_Relation_HasOne
{
	public function getKeys()
	{
		$local = $this->relation->getParentDescriptor();
		$related = $this->relation->getRelatedDescriptor();
		
		if(empty($this->foreign_keys))
		{
			// create the links from the related table to this table, then flip them so they go the other way
			// ie. foreign key -> primary key
			$this->foreign_keys = array_flip($this->relation->guessForeignKeyMappings($related));
		}
		
		$local_keys = $this->relation->getKeyObjects(array_keys($this->foreign_keys), $local);
		$related_keys = $this->relation->getKeyObjects(array_values($this->foreign_keys), $related);
		
		return array(array_values($local_keys), array_values($related_keys));
	}
}


/* End of file BelongsTo.php */
/* Location: ./lib/Rdm/Descriptor/Relation */