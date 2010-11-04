--TEST--
Annotation: Class annotations
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

class NestedAnnotation extends Rdm_Util_Annotation_AbstractAnnotation
{
	// Empty
}

$factory = new Rdm_Util_Annotation_Factory();
$reader  = new Rdm_Util_Annotation_Reader($factory);

echo "Calling readAnnotations(class Foo): ";
var_dump($reader->readAnnotations(new ReflectionClass('Foo')));

echo "\n===\n";

echo "Calling readAnnotations(class Bar): ";
var_dump($reader->readAnnotations(new ReflectionClass('Bar')));

echo "\n===\n";

echo "Calling readAnnotations(class Baz): ";
var_dump($reader->readAnnotations(new ReflectionClass('Baz')));

#[TestAnnotation]
class Foo
{
	
}

#[TestAnnotation(test=value)]
class Bar
{
	#[NotToBeParsed]
}

#[TestAnnotation]
# [ TestAnnotation ( test = value , test2 = [ NestedAnnotation ] ) ]
class Baz
{
	
}

--EXPECTF--
Calling readAnnotations(class Foo): Data received by TestAnnotation: NULL
array(1) {
  [0]=>
  object(TestAnnotation)#%d (0) {
  }
}

===
Calling readAnnotations(class Bar): Data received by TestAnnotation: array(1) {
  ["test"]=>
  string(5) "value"
}
array(1) {
  [0]=>
  object(TestAnnotation)#%d (0) {
  }
}

===
Calling readAnnotations(class Baz): Data received by TestAnnotation: NULL
Data received by TestAnnotation: array(2) {
  ["test"]=>
  string(5) "value"
  ["test2"]=>
  object(NestedAnnotation)#%d (0) {
  }
}
array(2) {
  [0]=>
  object(TestAnnotation)#%d (0) {
  }
  [1]=>
  object(TestAnnotation)#%d (0) {
  }
}