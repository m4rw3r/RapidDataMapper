====================================
RapidDataMapper Upgrade Instructions
====================================

This file contains instructions on how to update between versions.

It is by no means complete, instead it should be seen as a list
of the major changes and a guideline, not a thorough upgrade guide.

0.6.z to 0.7.z
==============

Class Naming
------------

The class prefix has been changed from ``Db_`` to ``Rdm_``

Loading of the library
----------------------

The loading of the library is different in several ways:

* The ``Db`` class has been removed, its configuration capabilities
  has been moved into ``Rdm_Config``
* The ``dbdriver`` configuration item has been replaced with ``class``,
  which contains the class name of the driver (eg. ``Rdm_Adapter_MySQL``)
* The bundled Autoloader is now located in ``lib/Rdm/Util/Autoloader.php``
  and is loaded like this::
  
    require 'lib/Rdm/Util/Autoloader.php';
    Rdm_Util_Autoloader::init();

* To use the ORM, you also have to call ``Rdm_Collection::init()`` to
  register the autoloader which generates the Collection classes

Descriptors
-----------

* Descriptors should now inherit ``Rdm_Descriptor`` instead of
  ``Db_Descriptor``
* Types are now constants of ``Rdm_Descriptors`` instead of strings
  with the type names
* ``setConnectionName()`` and ``setConnection()`` has been replaced with
  ``setAdapterName()`` and ``setAdapter()``

Connections
-----------

* Class renamed to ``Rdm_Adapter``
* To get a connection::

  $c = Rdm_Adapter::getInstance($name = 'default')

Performing ORM queries
----------------------

TODO: Basic pointers on how to restructure the Db::find() based queries
      to the collection based variants

Saving objects
--------------

Objects doesn't have to be explicitly saved, instead everything that has
been changed on objects which has been fetched from the database is
UPDATE:d when ``Rdm_Collection::pushChanges()`` or
``<Class>Collection::pushChanges()`` is called::

  $o = UserCollection::fetchByPrimaryKey(32);
  
  $o->name = 'Foobar';
  
  // $o is now changed, issue update query(ies)
  Rdm_Collection::pushChanges();

Create new objects
------------------

Use ``<Class>Collection::persist($object)`` to tell the Unit of Work that
``$object`` is a new instance that should be persisted in the database.
This is then inserted when ``Rdm_Collection::pushChanges()`` is called::

  $u = new User();
  
  $u->name = 'John Doe';
  
  UserCollection::persist($u);
  
  // Insert the user
  Rdm_Collection::pushChanges();

