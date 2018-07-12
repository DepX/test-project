<?php

namespace Simple\Cli;

use Simple\DI\InjectionInterface;

class Task implements InjectionInterface
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
}