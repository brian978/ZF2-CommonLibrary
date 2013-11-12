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

- processes select objects (usually the action is requested by a ResultProcessor when calling the getResultSet() method)

- attaches listeners to events from the data mappers and result processors


ResultProcessor

- is assigned to only 1 TableGateway and 1 method from a TableGateway

- it is returned by the TableGateway from methods that interact with the database

- can return either a paginator, a result set or any other type of result based on the
 database query

- the results of the methods may be cached

- contains an EventManager object that can be used by different objects
 (like Paginator and TableGateway) to listen in different events that the interface offers


Database Mapper

- is assigned to only 1 TableGateway

- called by the ResultProcessor via the TableGateway if the data needs to be mapped to objects

- uses a GatewayTracker to collect all the child data mappers that will be used when mapping the
data from the database


GatewayTracker

- a collection that keeps track of all the TableGateway objects

- used by the Database Mapper to determine which mapper objects are available for mapping


Design issues
--------------

**Issue 1:** What happens if the ResultSet needs a more complicated processing? Do we make a processor
for each method that interacts with the database?

*Solution 1:* We use an event called ResultProcessorInterface::EVENT_PROCESS_ROW which is handled in the gateway method


**Issue 2:** How can we return the result from the ResultProcessor without calling methods
from the TableGateway and ResultProcessor using the cache? (except for the ones that return the cache object)

*Solution 1:* Pending...


**Issue 3:** When caching the response of a method from the TableGateway the cache stores the ResultProcessor and not
the result from the ResultProcessor method call. The cache should cascade to the ResultProcessor even though it's called
from the TableGateway.

*Solution 1:* Pending...

**Issue 4:** What would happen with the map given to the getResultSet() method if automatic mapping on join would be
implemented?

*Solution 1:* Pending...
