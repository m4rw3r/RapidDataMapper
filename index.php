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
	}
}

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