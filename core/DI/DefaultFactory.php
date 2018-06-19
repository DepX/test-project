<?php

namespace Simple\DI;

use Simple\Di;
use Simple\Http\Response\Cookies;
use Simple\Http\Request;
use Simple\Http\Response;

/**
 * The standard dependency injection factory
 *
 * Class DefaultFactory
 * @package Simple\DI
 */
class DefaultFactory extends Di
{
    public function __construct()
    {
        parent::__construct();

        if ($this->service == null) {
            $this->service = new Service();

            $this->registerDefaultsServices();
        }
    }

    /**
     * Registers a service in the services container
     *
     * @param string $name
     * @param mixed $definition
     */
    public function setService($name, $definition)
    {
        if (get_class($definition) != 'Closure') {
            $definition = function () use ($definition) {
                return $definition;
            };
        }

        $func = $definition->bindTo(new \StdClass);
        $func_class = $func();

        // set di
        if (method_exists($func_class,'setDi')) {
            $func_class->setDi($this);
            $definition_new = function () use ($func_class) {
                return $func_class;
            };
            $definition = $definition_new;
        }
        $this->service->set($name, $definition);
    }

    /**
     * Return service by name
     *
     * @param $name
     * @return bool|mixed
     */
    public function getService($name)
    {
        return $this->service->get($name);
    }

    /**
     * Return all services
     *
     * @return bool|mixed
     */
    public function getServices()
    {
        return $this->service->getAll();
    }

    /**
     * Inherited from \Simple\DI\Service
     *
     * @param $name
     * @return bool
     */
    public function __get($name)
    {
        return $this->getService($name);
    }

    private function registerDefaultsServices()
    {
        $this->setService('request', new Request());
        $this->setService('response', new Response());
        $this->setService('cookies', new Cookies());
    }
}