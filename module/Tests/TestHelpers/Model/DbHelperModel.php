<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Model;

use Library\Model\AbstractDbHelperModel;

class DbHelperModel extends AbstractDbHelperModel
{
    protected $table = 'test';

    public function getWhere()
    {
        return $this->where;
    }

    public function getJoin()
    {
        return $this->join;
    }
}
