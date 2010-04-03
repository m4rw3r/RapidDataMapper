<?php
/*
 * Created by Martin Wernståhl on 2010-04-02.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Builder_Main extends Rdm_Util_Code_Container
{
	public function __construct(Rdm_Descriptor $descriptor)
	{
		$this->addPart(new Rdm_Builder_Collection($descriptor));
		$this->addPart(new Rdm_Builder_CollectionFilter($descriptor));
	}
	
	// ------------------------------------------------------------------------
	
	public function getName()
	{
		return 'name';
	}
}


/* End of file Main.php */
/* Location: ./lib/Rdm/Builder */