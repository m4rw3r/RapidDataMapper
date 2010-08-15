<?php
/*
 * Created by Martin Wernståhl on 2010-04-03.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * Contains methods and constants 
 */
final class Rdm_Version
{
	/**
	 * Version of the RapidDataMapper library, in x.y.z(-dev|rc-#) format,
	 * compatible with version_compare().
	 * 
	 * @var string
	 */
	const VERSION = '0.7.0-dev';
	
	// ------------------------------------------------------------------------

	/**
	 * Prints a list of the different types of dependencies and if they are
	 * satisfied or not, good for testing if the current setup can run RapidDataMapper.
	 * 
	 * @return void
	 */
	public static function printDependencies()
	{
		echo "Dependencies for RapidDataMapper ".self::VERSION.":\n";
		
		echo "\nRequired:\n";
		foreach(self::testRequiredDependencies() as $dep => $result)
		{
			echo sprintf('%30s: %s', $dep, $result ? 'YES' : 'NO')."\n";
		}
		
		echo "\nOptional:\n";
		foreach(self::testOptionalDependencies() as $dep => $result)
		{
			echo sprintf('%30s: %s', $dep, $result ? 'YES' : 'NO')."\n";
		}
		
		echo "\nDatabase Extensions:\n";
		foreach(self::testDatabaseExtensions() as $dep => $result)
		{
			echo sprintf('%30s: %s', $dep, $result ? 'YES' : 'NO')."\n";
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Tests the required dependencies which must not fail if RapidDataMapper
	 * is to run flawlessly.
	 * 
	 * @return array(string => boolean)
	 */
	public static function testRequiredDependencies()
	{
		return array(
			'PHP Version > 5.2'          => version_compare(PHP_VERSION, '5.2', '>='),
			'Standard PHP Library (SPL)' => extension_loaded('SPL'),
			'Regular Expressions (PCRE)' => extension_loaded('pcre'),
			'Reflection Extension'       => extension_loaded('Reflection')
			);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Tests for optional dependencies which are required by optional components
	 * of RapidDataMapper.
	 * 
	 * @return array(string => boolean)
	 */
	public static function testOptionalDependencies()
	{
		return array(
			'Namespace Support (PHP 5.3)' => version_compare(PHP_VERSION, '5.3', '>='),
			'APC (Query Cache Driver)'    => extension_loaded('apc'),
			'SimpleXML (XML Descriptors)' => extension_loaded('SimpleXML')
			);
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Tests which database extensions which are loaded, this determines
	 * which Rdm_Adapter variants which can be used.
	 * 
	 * @return array(string => boolean)
	 */
	public static function testDatabaseExtensions()
	{
		return array(
			'MySQL'               => extension_loaded('mysql'),
			'MySQL Improved'      => extension_loaded('mysqli'),
			'MySQL Native Driver' => extension_loaded('mysqlnd'),
			'SQLite'              => extension_loaded('SQLite'),
			'SQLite3'             => extension_loaded('sqlite3'),
			'PostgreSQL'          => extension_loaded('pgsql')
			);
	}
}

/* End of file Version.php */
/* Location: ./lib/Rdm */