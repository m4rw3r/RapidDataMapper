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

- Getter and setter support, so the properties of the entities can be protected
- Proper PHPdoc comments for return types, so that IDEs will autocomplete
  the generated code properly
- toXML() and toJSON() utilities, for both collections and single entities
- Bidirectional Many to Many relations
- Validation support (either built in or through a third party library or using
  adapters)
- Supported frameworks (compatibility files and manual section): Zend Framework,
  CodeIgniter, Symfony (both 1 and 2)
- DescriptorLoaders for XML (and maybe YAML)
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
- Greedy explicit fetch of related records, with possibilities to filter them too
- Filter by an already fetched related entity
- Establishing a relationship between two objects without having to assign
  them to a collection
- Establishing a relationship between a child object and a parent by adding it
  to an already fetched collection (the greedy fetch with has many relations)
- Transaction support

Todo
----

- More column types and type-related filters
- Commit orderer for the unit of work (to specify order of business so that the
  query types are made in the correct order so that we don't invalidate any
  constraints)
- Multi entity operations
- Limit related rows
- ON DELETE (?:DO NOTHING|CASCADE|RESTRICT|SET NULL)
- Removal of a related row from a collection
- Possibilities to use collections to create subqueries for use in filters or
  elsewhere (eg. as a union of two collections or something)
- Order by, also for related rows
- Utility methods for dealing with the collection contents (more like a normal
  array, eg. first(), last(), map() etc.)
