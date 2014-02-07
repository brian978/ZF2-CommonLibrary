<?php

// Map layouts for the standard mapper
array(
    'mapName' => array(
        'id' => 'id',
        'someFieldName' => 'entityField',
        'joinedId' => array( // This would be the field that triggers the dispatch to another mapper
            'mapper' => array(
                'entityField2', // Field where to put the result
                'Full\Qualified\Name\Of\Mapper',
                'default' // Map name to use
            )
        ),
    )
);


