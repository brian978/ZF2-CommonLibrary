<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\Model\Db\Components;

trait QueryHelper
{
    /**
     * Adapter platform
     *
     * @var \Zend\Db\Adapter\Platform\PlatformInterface
     */
    protected $platform;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * Can be used to generate a where condition
     *
     * @param string $field
     * @param string|int $value
     * @param string $table
     * @param string $sign
     * @return string
     */
    public function buildWhere($field, $value, $table = null, $sign = '=')
    {
        // When the value is an object it's because of an expression
        if (is_object($value)) {
            return $value;
        }

        if (null === $table) {
            $table = $this->table;
        }

        $where = $this->platform->quoteIdentifierChain(array($table, $field));
        $where .= $sign;
        $where .= $this->platform->quoteValue($value);

        return $where;
    }
}
