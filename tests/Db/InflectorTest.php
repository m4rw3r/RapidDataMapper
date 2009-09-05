<?php
/*
 * Created by Martin Wernståhl on 2009-09-05.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

require_once 'PHPUnit/Framework.php';

/**
 * @covers Db_Inflector
 */
class Db_InflectorTest extends PHPUnit_Framework_TestCase
{
	// ------------------------------------------------------------------------
	
	public function setUp()
	{
		require_once dirname(__FILE__).'/../../lib/Db/Inflector.php';
	}
	
	// ------------------------------------------------------------------------
	
	public function testPluralize()
	{
		$this->assertEquals('tables', Db_Inflector::pluralize('table'));
		$this->assertEquals('hives', Db_Inflector::pluralize('hive'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testPluralizePlural()
	{
		$this->assertEquals('tables', Db_Inflector::pluralize('tables'));
		$this->assertEquals('hives', Db_Inflector::pluralize('hives'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testPluralUppercaseFirst()
	{
		$this->assertEquals('Tables', Db_Inflector::pluralize('Table'));
		$this->assertEquals('Children', Db_Inflector::pluralize('Child'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testPluralizeIrregular()
	{
		$this->assertEquals('atlases', Db_Inflector::pluralize('atlas'));
		$this->assertEquals('children', Db_Inflector::pluralize('child'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testPluralizeIrregularPlural()
	{
		$this->assertEquals('atlases', Db_Inflector::pluralize('atlases'));
		$this->assertEquals('children', Db_Inflector::pluralize('children'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testPluralizeUncountable()
	{
		$this->assertEquals('news', Db_Inflector::pluralize('news'));
		$this->assertEquals('equipment', Db_Inflector::pluralize('equipment'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingularize()
	{
		$this->assertEquals('table', Db_Inflector::singularize('tables'));
		$this->assertEquals('hive', Db_Inflector::singularize('hives'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingularizeSingular()
	{
		$this->assertEquals('table', Db_Inflector::singularize('table'));
		$this->assertEquals('hive', Db_Inflector::singularize('hive'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingularizeUppercaseFirst()
	{
		$this->assertEquals('Table', Db_Inflector::singularize('Tables'));
		$this->assertEquals('Child', Db_Inflector::singularize('Children'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingularizeIrregular()
	{
		$this->assertEquals('atlas', Db_Inflector::singularize('atlases'));
		$this->assertEquals('child', Db_Inflector::singularize('children'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingularizeIrregularSingular()
	{
		$this->assertEquals('atlas', Db_Inflector::singularize('atlas'));
		$this->assertEquals('child', Db_Inflector::singularize('child'));
	}
	
	// ------------------------------------------------------------------------
	
	public function testSingularizeUncountable()
	{
		$this->assertEquals('news', Db_Inflector::singularize('news'));
		$this->assertEquals('equipment', Db_Inflector::singularize('equipment'));
	}
}

/* End of file InflectorTest.php */
/* Location: ./tests/Db */