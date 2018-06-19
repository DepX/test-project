<?php

namespace Simple\DI;

interface InjectionInterface
{
    /**
     * @param \Simple\DI\DefaultFactory $di
     */
    public function setDi($di);

    /**
     * @return \Simple\DI\DefaultFactory
     */
    public function getDi();
}