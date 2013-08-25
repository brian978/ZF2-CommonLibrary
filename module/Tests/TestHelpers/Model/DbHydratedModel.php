<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model;

use Library\Model\AbstractDbModel;
use Library\Model\Components\HydratorHelper;

class DbHydratedModel extends AbstractDbModel
{
    use HydratorHelper;

    protected $table = 'test';

    public function fetch()
    {
        // Some dummy data
        $data = array(
            'test1' => 'test1 string',
            'test2' => 'test2 string'
        );

        return array(
            $this->getHydrator()->hydrate($data, new \ArrayObject())
        );
    }

    /**
     * @param $object
     * @return mixed
     */
    protected function doInsert($object)
    {
        // TODO: Implement doInsert() method.
    }

    /**
     * @param $object
     * @return mixed
     */
    protected function doUpdate($object)
    {
        // TODO: Implement doUpdate() method.
    }

    /**
     * @param $object
     * @return mixed
     */
    public function doDelete($object)
    {
        // TODO: Implement doDelete() method.
    }
}
