<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db;

use Library\Model\Mapper\Db\TableInterface;
use Zend\Db\TableGateway\TableGateway;

abstract class AbstractTableGateway extends TableGateway implements TableInterface
{

}
