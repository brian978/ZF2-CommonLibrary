<?php

// Map layouts for the database mapper
array(
   'id' => 'id',
   'someFieldName' => 'entityField',
   'joinedId' => array( // This would be the field that triggers the dispatch to another mapper
       'mapper' => array(
           'entityField2', // Field from the entity to put the result from the dispatched mapper
           'Full\Qualified\Name\Of\Mapper',
       ),
       'dataSource' => array(
           'table' => 'tableToJoin',
           'type' => Select::JOIN_INNER,
           'on' => array(
               'id' => 'id',
           ),
           'columns' => array(
               'testId' => 'id',
               'testField1' => 'field1',
               'testField2' => 'field2',
           )
       )
   )
);


