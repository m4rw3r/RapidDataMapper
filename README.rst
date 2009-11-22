===============
RapidDataMapper
===============

A DataMapper and database abstraction for PHP.

Requirements
============

RapidDataMapper:
----------------

* PHP > 5.2
* Appropriate database extension

Tests:
------

Currently, PHPUnit does not support passing parameters by reference to a
mocked method. This means that some test-cases will fail under a vanilla
PHPUnit installation.
Instead of the standard 3.4 version of PHPUnit I recommend using a modified
version which can be found here: http://github.com/m4rw3r/phpunit

* PHPUnit 3.4 or later
* PHP > 5.2

Loading of files
================

RapidDataMapper is trying to use the file organization which is also used by many other
PHP libraries; That is, convert all underscored (and namespace separators) in the
class name to DIRECTORY_SEPARATOR.
That means the class Db_Driver_Mysql_Connection is located in the file
Db/Driver/Mysql/Connection.php.

RapidDataMapper includes an autoloder which will autoload the library files, relative
to the library root. This autoloader is not automatically initialized, as some frameworks
or users already utilizes a compatible autoloader, so you have to call its initializing method::

    // load the library file
    require 'lib/Db.php';
    
    // init the autoloader packaged with the library
    Db::initAutoload();

The mapping from class name to file name is made like this::

    $file = self::$lib_base . str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';

Running the tests
=================

1. Make sure that the tests directory is placed in the same directory as
   the lib folder.
2. Run the tests by executing "PHPUnit tests" in that directory

Building the API documentation
==============================

Requires PHPdocumentor.

Run::

    make phpdoc

The api documentation will be placed in ./api