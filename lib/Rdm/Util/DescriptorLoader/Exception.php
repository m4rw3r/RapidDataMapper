<?php
/*
 * Created by Martin Wernståhl on 2010-07-04.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
class Rdm_Util_DescriptorLoader_Exception extends RuntimeException implements Rdm_Exception
{
	/**
	 * Creates an exception telling the user that the descriptor class cannot be
	 * found in the file where it was expected to be.
	 * 
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return Rdm_Util_DescriptorLoader_Exception
	 */
	public static function fileMissingClass($file, $class, $descriptor_class)
	{
		return new Rdm_Util_DescriptorLoader_Exception(sprintf('The descriptor class (%s) for the class "%s" cannot be found in the descriptor file "%s".', $descriptor_class, $class, $file));
	}
}


/* End of file Exception.php */
/* Location: ./lib/Rdm/Util/DescriptorLoader */