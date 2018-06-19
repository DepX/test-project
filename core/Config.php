<?php

namespace Simple;

/**
 * Configuration
 *
 * Class Config
 * @package Simple
 */
class Config
{
    private $config = [];

    /**
     * Set config parameters and convert to object
     *
     * Config constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = json_decode(json_encode($config), false);
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        return (!empty($this->config->{$name})) ? $this->config->{$name} : null;
    }

    /**
     * Convert object to array
     *
     * @return mixed
     */
    public function toArray()
    {
        return json_decode(json_encode($this->config), true);
    }
}