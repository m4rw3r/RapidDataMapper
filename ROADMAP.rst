====================================
RapidDataMapper              Roadmap
====================================

Note: This roadmap is subject to change

0.7
===

- New Collection API
- Unit of Work

0.8
===

Will move stuff from 1.0 to here

0.9
===

Will move stuff from 1.0 to here

1.0
===

- ReflectionProperty support, so the private properties of the entities can
  be protected without having to go through setters/getters
  (ReflectionProperty is faster provided it is cached during the run)
- Proper PHPdoc comments for return types, so that IDEs will autocomplete
  the generated code properly
- toXML() and toJSON() utilities, for both collections and single entities
- Bidirectional Many to Many relations
- Validation support (either built in or through a third party library or using
  adapters)
- Supported frameworks (compatibility files and manual section): Zend Framework,
  CodeIgniter, Symfony (both 1 and 2)
- DescriptorLoaders for XML (and maybe YAML)
- DescriptorLoader for annotations?
- Table builder from descriptors
- Descriptor and entity creator which reads the database
- Proper cache driver and cache system for caching query results locally



Specifications for the new Collection API
=========================================

A temporary block for the new Collection API while it is in development

Finished
--------

- Create persistent entity from newly created PHP object
- Automatic dirty checks on entities and consequent updates if they has been
  modified when pushChanges() is invoked
- Fetch of single entity based on its primary key or exact column value
- Count queries issued if collection hasn't been populated to avoid fetching
  much data from the server
- Basic filter methods based on the type of the columns
- Column type handling with type objects
- Usage of plain SQL in filters
- Usage of bound parameters in custom SQL
- Built in Unit of Work
- Delete persisted object from database
- Transaction support
- Commit orderer for the unit of work (to specify order of business so that the
  query types are made in the correct order so that we don't invalidate any
  constraints)

Relations:
  - Possibility to create subqueries with related collections
  - Establishing a relationship between a child object and a parent by adding it
    to an already fetched collection (the greedy fetch with has many relations,
    using with(relation-constant))
  - Establishing a relationship between two objects without having to assign
    them to a collection
  - Filter by an already fetched related entity
  - Greedy explicit fetch of related records, with possibilities to filter them
    too, using with(relation-constant)

Todo
----

- More column types and type-related filters
- Multi entity operations
- Possibilities to use collections to create subqueries for use in filters or
  elsewhere (eg. as a union of two collections or something)
- Order by, also for related rows
- Modify code builders so that they use the redirect_write adapter when
  creating write queries, as that will be the adapter used by the ORM for
  querying writes?

Collection object:
  - Utility methods for dealing with the collection contents (more like a normal
    array, eg. first(), last(), map() etc.)
  - Proper (un)serialization()

Relations:
  - Establishing relations between two objects before they have been saved
  - Destroying relationships
  - ON DELETE (?:DO NOTHING|CASCADE|RESTRICT|SET NULL)
  - Limit related rows in with()
  - Check for Has One and Belongs To relations if the related objects
    on the property has been changed (ie. the whole object) on single changes
  - Removal of a related row from a collection