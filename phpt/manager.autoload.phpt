--TEST--
Test to see that the autoloader is added at the appropriate time, ie. using the Rdm_CollectionManager->registerCollectionAutoloader()
--FILE--
<?php

include dirname(__FILE__).'/../lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

$c = new Rdm_Config();

$m = new Rdm_CollectionManager($c);
$m->registerCollectionAutoloader(false);

try
{
	$l = new FooCollection();
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

--EXPECT--
string(49) "Descriptor for class "Foo": Descriptor is missing"