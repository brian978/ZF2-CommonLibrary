ZF2-ExtendedFramework
=================

Just a module I use that has some extra functionality built on top of ZendFramework 2.
Below you will find details about the goals and designs of the library if you want to
contribute (ideas, code, etc).

Goals
=================
Build a system that is able to map the data from the database to certain objects. The
idea is to make something similar to an ORM from a mapping point of view but avoid all
the configuration hassle. All the links will be done based on the SQL query.

Design
-----------
TableGateway

- handles 1 database table

- processes select objects (usually the action is requested by a ResultProcessor)

- triggers events either on the mappers or the result processors


ResultProcessor

- is assigned to only 1 TableGateway

- it is returned by the TableGateway from methods that interact with the database

- can return either a paginator, a result set or any other type of result based on the
 database query

- the results of the methods may be cached

- contains a EventManager object that can be used by different objects
 (like Paginator and TableGateway)


Database Mapper

- is assigned to only 1 TableGateway

- called by the ResultProcessor via the TableGateway if the data needs to be mapped to objects

- uses a table tracker to collect all the child data mappers that will be used when mapping the
data from the database


GatewayTracker

- a collection that keeps track of all the TableGateway objects

- used by the Database Mapper to determine which mapper objects are available for mapping


Design issues
-----------

What happens if the ResultSet needs a more complicated processing? Do we make a processor
for each method that interacts with the database?

How can we return the result from the ResultProcessor without calling methods
from the TableGateway and ResultProcessor? (except for the ones that return the cache object)
