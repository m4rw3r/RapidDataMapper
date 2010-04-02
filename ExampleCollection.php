<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class ExampleCollection extends Rdm_Collection
{
	public static function create()
	{
		return new ExampleCollection();
	}
	
	public function createFilterInstance()
	{
		return new ExampleFilter($this);
	}
	
	public function populate()
	{
		var_dump('Populating...');
		$this->contents = array('ROFLCOPTER', 'LOL', 'LMAO');
		
		$this->is_populated = true;
		$this->is_locked = true;
	}
}


/* End of file ExampleCollection.php */
/* Location: . */