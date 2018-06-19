<?php

namespace Simple\DI;

/**
 * Services container
 *
 * Class Service
 * @package Simple\DI
 */
class Service
{
    private $services = [];

    /**
     * Registers a service in the services container
     *
     * @param string $name
     * @param mixed $definition
     */
    public function set($name, $definition)
    {
        $this->services[$name] = $definition;
    }

    /**
     * Return service by name
     *
     * @param $name
     * @return bool|mixed
     */
    public function get($name)
    {
        return (!empty($this->services[$name])) ? $this->services[$name]() : false;
    }

    /**
     * Return all service
     *
     * @return bool|mixed
     */
    public function getAll()
    {
        return $this->services;
    }

    /**
     * Return service by name
     *
     * @param $name
     * @return bool|mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}