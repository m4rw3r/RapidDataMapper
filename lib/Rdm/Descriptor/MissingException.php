<?php
/*
 * Created by Martin Wernståhl on 2009-12-04.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Descriptor_MissingException extends Rdm_Descriptor_Exception
{
	public function __construct($class_name)
	{
		parent::__construct($class_name, 'Descriptor is missing');
	}
}


/* End of file MissingException.php */
/* Location: ./lib/Rdm/Descriptor */