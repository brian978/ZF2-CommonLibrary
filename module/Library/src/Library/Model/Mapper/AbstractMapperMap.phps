<?php

// Map layouts for the standard mapper

array(
    'mapName' => array(
        'id' => 'id',
        'someFieldName' => 'entityField',
        'joinedId' => array( // This would be the field that triggers the dispatch to another mapper
            'mapper' => array(
                'entityField2', // Field from the entity to put the result from the dispatched mapper
                'Full\Qualified\Name\Of\Mapper',
            )
        ),
    )
);

OR

array(
    'id' => 'id',
    'someFieldName' => 'entityField',
    'joinedId' => array( // This would be the field that triggers the dispatch to another mapper
        'mapper' => array(
            'entityField2', // Field from the entity to put the result from the dispatched mapper
            'Full\Qualified\Name\Of\Mapper',
        )
    ),
);


