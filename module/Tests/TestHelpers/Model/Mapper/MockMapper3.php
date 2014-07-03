<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model\Mapper;

use Acamar\Model\Mapper\AbstractMapper;

class MockMapper3 extends AbstractMapper
{
    /**
     * Class name of the entity that the data will be mapped to
     *
     * @var string
     */
    protected $entityClass = '\Tests\TestHelpers\Model\Entity\MappedMockEntity';

    /**
     * The map that will be used to populate the object
     *
     * @var array
     */
    protected $map = array(
        'default' => array(
            'joinedId' => 'id',
            'joinedField' => 'testField1',
        )
    );
}
