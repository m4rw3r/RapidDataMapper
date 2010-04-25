<?php
/*
 * Created by Martin Wernståhl on 2010-04-25.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Describes the integer data type.
 */
class Rdm_Descriptor_Type_Int extends Rdm_Descriptor_Type_Generic
{
	public function getCastToPhpCode($source)
	{
		return '(Int)'.$source;
	}
	
	public function getCastFromPhpCode($source)
	{
		return '(Int)'.$source;
	}
}


/* End of file Int.php */
/* Location: ./lib/Rdm/Descriptor/Type */