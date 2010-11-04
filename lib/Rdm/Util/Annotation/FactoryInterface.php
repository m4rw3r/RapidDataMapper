<?php
/*
 * Created by Martin Wernståhl on 2010-11-24
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
interface Rdm_Util_Annotation_FactoryInterface
{
	
	// ------------------------------------------------------------------------

	/**
	 * Creates the annotation data.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return mixed
	 */
	public function createObject($annotation_name, $params);
}


/* End of file FactoryInterface.php */
/* Location: ./lib/Rdm/Util/Annotation */