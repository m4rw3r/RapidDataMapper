--TEST--
Annotation: Method annotations
--FILE--
<?php

require __DIR__.'/../lib/Rdm/Util/Autoloader.php';
Rdm_Util_Autoloader::init();

class TestAnnotation extends Rdm_Util_Annotation_AbstractAnnotation
{
	public function __construct($params = null)
	{
		echo "Data received by TestAnnotation: ";
		var_dump($params);
	}
}

$factory = new Rdm_Util_Annotation_Factory();
$reader  = new Rdm_Util_Annotation_Reader($factory);

var_dump($reader->readAnnotations(new ReflectionMethod('Foo', 'test')));


class Foo
{
	#[TestAnnotation]
	public function test()
	{
		#[NotToBeParsed]
	}
}

--EXPECTF--
Data received by TestAnnotation: NULL
array(1) {
  [0]=>
  object(TestAnnotation)#%d (0) {
  }
}