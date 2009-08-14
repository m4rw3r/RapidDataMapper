<?php
/*
 * Created by Martin Wernståhl on 2009-08-14.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * A class containing utilities used by the RapidDataMapper library.
 */
class Db_Util
{
	
	// ------------------------------------------------------------------------
	
	/**
	 * Compares two arrays like array_diff(), but can also compare arrays of objects.
	 *
	 * The condition is $a !== $b
	 *
	 * @param  array
	 * @param  array
	 * @return array
	 */
	public static function array_odiff($arr1, $arr2)
	{
		if(empty($arr2))
		{
			return $arr1;
		}
		
		$ret = array();
		
		foreach($arr1 as $a)
		{
			if( ! in_array($a, $arr2, true))
			{
				$ret[] = $a;
			}
		}
		
		return $ret;
	}
}


/* End of file Util.php */
/* Location: ./lib/Db */