<?php

namespace Simple\Http\Response;

/**
 * Class Cookies
 * @package Simple\Http
 */
class Cookies
{
    /**
     * Set new cookie
     *
     * @param $name
     * @param $value
     * @param int $time
     */
    public function set($name, $value, $time = 0)
    {
        setcookie($name, $value, $time);
    }

    /**
     * Get cookie
     *
     * @param $name
     * @return bool
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $_COOKIE[$name];
        }

        return false;
    }

    /**
     * Check cookie
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * Delete cookie
     *
     * @param $name
     */
    public function delete($name)
    {
        setcookie($name, "", time() - 10);
    }
}