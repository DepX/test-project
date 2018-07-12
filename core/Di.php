<?php

namespace Simple;

/**
 * Class Dependency Injection
 * @package Simple
 */
class Di
{
    protected static $default;

    protected $service;

    public function __construct()
    {
        if (self::$default == null) {
            self::$default = $this;
        }
    }

    /**
     * @return mixed
     */
    public static function getDefault()
    {
        return self::$default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        self::$default = $default;
    }
}