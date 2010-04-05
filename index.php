<?php
/*
 * Created by Martin Wernståhl on 2010-03-30.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

echo "<pre>";

require 'config.php';

class TrackDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->add($this->newPrimaryKey('id'));
		
		$this->add($this->newColumn('name'));
	}
}

class Track
{
	public $id;
	public $name;
}

$c = TrackCollection::create();

$c->has()
		->name('foobar')
	->end();

print_r($c);

foreach($c as $t)
{
	print_r($t);
}

var_dump((String) $c);

/* End of file index.php */
/* Location: . */