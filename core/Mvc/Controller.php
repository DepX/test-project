<?php

namespace Simple\Mvc;

use Simple\DI\InjectionInterface;

abstract class Controller implements InjectionInterface
{
    protected $di;

    /**
     * @param \Simple\DI\DefaultFactory $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * @return \Simple\DI\DefaultFactory
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * Inherited from \Simple\DI\DefaultFactory
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getDi()->getService($name);
    }
}