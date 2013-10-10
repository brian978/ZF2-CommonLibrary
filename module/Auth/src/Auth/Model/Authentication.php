<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Model;

use Auth\Model\Helper\Password as PasswordHelper;
use Zend\Authentication\Adapter\DbTable\AbstractAdapter;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface as DbAdapterInterface;
use Zend\Log\Logger;
use Zend\ServiceManager\ServiceManager;

class Authentication extends AuthenticationService
{
    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $dbAdapter;

    /**
     * @var AbstractAdapter
     */
    protected $adapter;

    /**
     * @var PasswordHelper
     */
    protected $passwordHelper;

    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * @param DbAdapterInterface $dbAdapter
     * @param Helper\Password    $passwordHelper
     */
    public function __construct(DbAdapterInterface $dbAdapter = null, PasswordHelper $passwordHelper = null)
    {
        if (null !== $dbAdapter) {
            $this->setDbAdapter($dbAdapter);
        }

        if (null !== $passwordHelper) {
            $this->setPasswordHelper($passwordHelper);
        }
    }

    /**
     * @param DbAdapterInterface $dbAdapter
     * @return $this
     */
    public function setDbAdapter(DbAdapterInterface $dbAdapter)
    {
        if ($dbAdapter !== null) {
            $this->dbAdapter = $dbAdapter;
        }

        return $this;
    }


    /**
     * @param PasswordHelper $passwordHelper
     * @return $this
     */
    public function setPasswordHelper(PasswordHelper $passwordHelper)
    {
        if ($passwordHelper !== null) {
            $this->passwordHelper = $passwordHelper;
        }

        return $this;
    }

    /**
     * @param Logger $logger
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return AbstractAdapter
     */
    public function getAdapter()
    {
        // Creating a new adapter using the default one
        if (!$this->adapter instanceof AbstractAdapter) {
            $this->adapter = new CredentialTreatmentAdapter($this->dbAdapter, 'users', 'email', 'password');
        }

        return parent::getAdapter();
    }

    /**
     * @param array $credentials
     *
     * @throws \RuntimeException
     * @return $this
     */
    public function setCredentials(array $credentials)
    {
        // Creating a new adapter using the default one
        if ($this->getAdapter() === null) {
            throw new \RuntimeException('Credential treatment adapter not set');
        }

        $defaults = array(
            'email' => 'dummy',
            'password' => ''
        );

        $credentials = array_merge($defaults, $credentials);

        $this->adapter->setIdentity($credentials['email']);
        $this->adapter->setCredential($this->createPassword($credentials));

        return $this;
    }

    /**
     * Encodes and encrypts the password
     *
     * @param array $credentials
     * @throws \RuntimeException
     * @return string
     */
    protected function createPassword($credentials)
    {
        if (null == $this->passwordHelper) {
            throw new \RuntimeException('No password helper has been provided');
        }

        /** @var $platform \Zend\Db\Adapter\Platform\PlatformInterface */
        $platform     = $this->dbAdapter->getPlatform();
        $passwordHash = '';

        /** @var $select \Zend\Db\Sql\Select */
        $select = $this->adapter->getDbSelect();
        $select->from('users')->where(
            'email = ' . $platform->quoteValue($credentials['email'])
        );

        try {
            /** @var $result \Zend\Db\ResultSet\ResultSet */
            $result = $this->dbAdapter->query(
                $select->getSqlString(),
                Adapter::QUERY_MODE_EXECUTE
            );
        } catch (\Exception $e) {
            $this->logger->alert($e->getMessage(), array('file' => __FILE__));
        }

        if (isset($result)) {

            // Email should be unique
            if ($result->count() === 1) {
                $row          = $result->current();
                $passwordHash = $this->passwordHelper->setPassword($credentials['password'])
                    ->processHash($row['password'])
                    ->generateHash();
            }
        }

        return $passwordHash;
    }
}
