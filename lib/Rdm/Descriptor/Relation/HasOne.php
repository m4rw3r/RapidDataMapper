<?php
/*
 * Created by Martin Wernståhl on 2009-08-15.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains specific logic for the Has One relationship type.
 */
class Rdm_Descriptor_Relation_HasOne extends Rdm_Descriptor_Relation_HasMany
{
	public function isPlural()
	{
		return false;
	}
}


/* End of file HasOne.php */
/* Location: ./lib/Rdm/Descriptor/Relation */