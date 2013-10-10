<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Model;

class Authorization
{
    /**
     * @var AuthorizationDbInterface
     */
    protected $model;

    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param AuthorizationDbInterface $model
     * @param Authentication           $auth
     */
    public function __construct(AuthorizationDbInterface $model, Authentication $auth)
    {
        $this->setModel($model);
        $this->setAuth($auth);
    }

    /**
     * @param \Auth\Model\Authentication $auth
     * @return $this
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return \Auth\Model\Authentication
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param \Auth\Model\AuthorizationDbInterface $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return \Auth\Model\AuthorizationDbInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     * @throws \RuntimeException
     */
    public function getRole()
    {
        if ($this->getModel() instanceof AuthorizationDbInterface === false) {
            throw new \RuntimeException('The role information must be in an ArrayObject object', 5);
        }

        $result = $this->getModel()->getInfoByIdentity($this->auth->getIdentity());

        if ($result instanceof \ArrayObject === false) {
            throw new \RuntimeException('The role information must be in an ArrayObject object', 10);
        }

        if ($result->count() === 0) {
            throw new \RuntimeException('The result does not contain any role information', 20);
        }

        if (!isset($result['roleName'])) {
            throw new \RuntimeException('The role data does not contain a "roleName" entry', 30);
        }

        return $result['roleName'];
    }
}
