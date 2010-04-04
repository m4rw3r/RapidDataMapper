<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

echo "<pre>";

require 'config.php';

class ExampleDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('title'));
	}
}

class Example
{
	public $id;
	public $title;
}

// just create an instance to force reload of the generated objects
ExampleCollection::create();

// Create a dummy object
$e = new Example;
$e->id = 34;
$e->title = 'foobar';
$e->__id = array('id' => 34);
$e->__data['title'] = 'folbar';

// Create a unit of work to test
$u = new ExampleUnitOfWork;

$u->addEntity($e, 'someuid');
$u->addForDelete($e, 'some2');

$u->commit();

// Show the object data
var_dump($e);

$c = ExampleCollection::create();

$c->has()
		->a('b')
		->c('d')
	->end()
	->orHas()
		->a('d')
		->c('b')
	->end();

print_r($c);

var_dump((String) $c);

/* End of file index.php */
/* Location: . */