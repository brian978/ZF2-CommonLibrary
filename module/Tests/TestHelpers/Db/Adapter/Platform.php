<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-AuthModule
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Tests\TestHelpers\Db\Adapter;

use Zend\Db\Adapter\Platform\PlatformInterface;

class Platform implements PlatformInterface
{
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'DbPlatformMock';
    }

    /**
     * Get quote identifier symbol
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return '';
    }

    /**
     * Quote identifier
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        return $identifier;
    }

    /**
     * Quote identifier chain
     *
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain)
    {
        if (is_array($identifierChain)) {
            $identifierChain = implode(',', $identifierChain);
        }

        return $identifierChain;
    }

    /**
     * Get quote value symbol
     *
     * @return string
     */
    public function getQuoteValueSymbol()
    {
        return '';
    }

    /**
     * Quote value
     *
     * Will throw a notice when used in a workflow that can be considered "unsafe"
     *
     * @param  string $value
     * @return string
     */
    public function quoteValue($value)
    {
        return $value;
    }

    /**
     * Quote Trusted Value
     *
     * The ability to quote values without notices
     *
     * @param $value
     * @return mixed
     */
    public function quoteTrustedValue($value)
    {
        return $value;
    }

    /**
     * Quote value list
     *
     * @param string|string[] $valueList
     * @return string
     */
    public function quoteValueList($valueList)
    {
        // To avoid errors in PHPMD
        unset($valueList);

        return '';
    }

    /**
     * Get identifier separator
     *
     * @return string
     */
    public function getIdentifierSeparator()
    {
        return '.';
    }

    /**
     * Quote identifier in fragment
     *
     * @param  string $identifier
     * @param  array $additionalSafeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $additionalSafeWords = array())
    {
        // To avoid errors in PHPMD
        unset($additionalSafeWords);

        return $identifier;
    }
}
