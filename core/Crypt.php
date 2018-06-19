<?php

namespace Simple;

/**
 * Class Crypt
 * @package Simple
 */
class Crypt
{
    private $cost = 8;

    private $method = 'AES-256-CFB';
    private $key = '&wW5kzMe$Kb#7&t?9axKA@Q$?JY-MB%=';
    private $iv = 'dFm@$2Khx2N$vUy4';

    /**
     * Method
     *
     * @param $method
     */
    public function setMethod($method)
    {
        if (!empty($method)) {
            $this->method = $method;
        }
    }

    /**
     * Secret key
     *
     * @param $key
     */
    public function setKey($key)
    {
        if (!empty($key)) {
            $this->key = $key;
        }
    }

    /**
     * @param $iv
     */
    public function setIv($iv)
    {
        if (!empty($iv)) {
            $this->iv = $iv;
        }
    }

    /**
     * @param $cost
     */
    public function setCost($cost)
    {
        if (intval($cost) <= 12) {
            $this->cost = $cost;
        }
    }

    public function encrypt($text, $key = '')
    {
        return openssl_encrypt(
            $text,
            $this->method,
            (!empty($key)) ? $key : $this->key,
            0,
            $this->iv
        );
    }

    /**
     * Generate password hash
     *
     * @param $password
     * @return bool|string
     */
    public function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => $this->cost
        ]);
    }

    /**
     * Check password hash
     *
     * @param $password
     * @param $passwordHash
     * @return bool
     */
    public function checkHash($password, $passwordHash)
    {
        return password_verify($password, $passwordHash);
    }
}