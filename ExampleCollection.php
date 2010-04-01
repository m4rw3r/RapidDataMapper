<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class ExampleCollection extends \Rdm\Collection
{
	const FILTER_CLASS = 'ExampleFilter';
	
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