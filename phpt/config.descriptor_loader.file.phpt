--TEST--
Test of the Rdm_Util_DescriptorLoader_File class
--FILE--
<?php

include 'config/config.php';

$contents_user = '<?php
class UserDescriptor extends Rdm_Descriptor
{
	public function __construct()
	{
		$this->setClass(\'User\');
		$this->add($this->newPrimaryKey(\'id\'));
		$this->add($this->newColumn(\'name\'));
	}
}';

$contents_fake = '<?php echo "foobar\n";';

mkdir(dirname(__FILE__).'/tmp');
file_put_contents(dirname(__FILE__).'/tmp/User.php', $contents_user);
file_put_contents(dirname(__FILE__).'/tmp/Foobar.php', $contents_fake);

$c = Config::getConfig();

$c->addDescriptorLoader(array(new Rdm_Util_DescriptorLoader_File(dirname(__FILE__).'/tmp'), 'load'));

class User
{
	public $id;
	public $name;
}

try
{
	$c->getDescriptor('Foobar');
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

try
{
	$c->getDescriptor('Baz');
}
catch(Rdm_Exception $e)
{
	var_dump($e->getMessage());
}

$desc = $c->getDescriptor('User');

echo 'class: ';
var_dump($desc->getClass());

echo 'pks: ';
foreach($desc->getPrimaryKeys() as $c)
{
	var_dump($c->getProperty());
}

echo 'columns: ';
foreach($desc->getColumns() as $c)
{
	var_dump($c->getProperty());
}

--CLEAN--
<?php
foreach(glob(dirname(__FILE__).'/tmp/*.php') as $f)
{
	unlink($f);
}

@rmdir(dirname(__FILE__).'/tmp');

--EXPECT--
foobar
string(148) "The descriptor class (FoobarDescriptor) for the class "Foobar" cannot be found in the descriptor file "/Users/m4rw3r/Sites/RDM/phpt/tmp/Foobar.php"."
string(49) "Descriptor for class "Baz": Descriptor is missing"
class: string(4) "User"
pks: string(2) "id"
columns: string(4) "name"