<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Mapper;

use Library\Model\Mapper\AbstractMapper;

class DefaultMockMapper extends AbstractMapper
{
    /**
     * Class name of the entity that the data will be mapped to
     *
     * @var string
     */
    protected $entityClass = '\Tests\TestHelpers\Model\Entity\MockEntity';

    /**
     * The map that will be used to populate the object
     *
     * @var array
     */
    protected $map = array(
        'default' => array(
            'id' => 'id',
            'field1' => 'testField1',
            'joinedId' => array(
                'testField2',
                'Tests\TestHelpers\Model\Mapper\MockMapper2',
            ),
        )
    );
}
