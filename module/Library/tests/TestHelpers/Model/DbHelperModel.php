<?php
/**
 * ZF2-CommonLibrary
 *
 */

namespace TestHelpers\Model;

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