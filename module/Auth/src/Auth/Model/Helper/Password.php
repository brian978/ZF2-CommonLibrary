<?php
/**
 * ZF2-AuthModule
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Auth\Model\Helper;

class Password
{
    /**
     * @var string
     */
    protected $salt = '';

    /**
     * @var int
     */
    protected $hashCut = 0;

    /**
     * @var string
     */
    protected $plainPassword = '';

    /**
     * @var int
     */
    protected $saltLen = 0;

    /**
     * @param string $plainPassword
     */
    public function __construct($plainPassword = '')
    {
        if (!empty($plainPassword)) {
            $this->setPassword($plainPassword);
        }
    }

    /**
     * @param string $plainPassword
     * @return $this
     */
    public function setPassword($plainPassword = '')
    {
        if (is_string($plainPassword) && strlen($plainPassword) > 0) {
            $this->plainPassword = $plainPassword;
        }

        return $this;
    }

    /**
     * Processes the password and extracts the salt from it
     *
     * @param $hash
     * @return $this
     */
    public function processHash($hash)
    {
        if (strlen($hash) > 3) {
            $hashCutLen    = substr($hash, -1, 1);
            $this->hashCut = substr($hash, -$hashCutLen - 1, $hashCutLen);
            $this->saltLen = substr($hash, -$hashCutLen - 3, 2);
            $this->salt    = substr($hash, $this->hashCut, $this->saltLen);
        }

        return $this;
    }

    /**
     * Hashes the password with a new salt or an existing one (if the processHash method has been called)
     *
     * @throws \RuntimeException
     * @return string
     */
    public function generateHash()
    {
        if (empty($this->plainPassword)) {
            throw new \RuntimeException('No password given');
        }

        if (empty($this->salt)) {
            $this->salt    = substr(sha1(uniqid('', true) . time()), 0, rand(10, 20));
            $this->saltLen = strlen($this->salt);
            $this->hashCut = rand(4, $this->saltLen);
        }

        $hashCutLen = strlen((string)$this->hashCut);
        $hash       = hash('sha512', $this->plainPassword . $this->salt);

        $finalHash = substr($hash, 0, $this->hashCut);
        $finalHash .= $this->salt;
        $finalHash .= substr($hash, $this->hashCut);
        $finalHash .= $this->saltLen;
        $finalHash .= $this->hashCut;
        $finalHash .= $hashCutLen;

        return $finalHash;
    }
}
