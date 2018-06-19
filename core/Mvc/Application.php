<?php

namespace Simple\Mvc;

use Simple\Exception;

/**
 * Initializing all components the application
 *
 * Class Application
 * @package Simple\Mvc
 */
class Application
{
    private $di;

    /**
     * @param \Simple\DI\DefaultFactory $di
     */
    public function __construct($di)
    {
        $this->di = $di;
    }

    /**
     * Load application
     */
    public function load()
    {
        $routerService = $this->di->getService('router');
        $currentRoute = $routerService->getCurrentRoute();

        // check current route
        $currentRoute = ($currentRoute == false) ? $routerService->notFound : $currentRoute;

        $s_controllerClass = '\\' . $currentRoute->getNamespace() . '\\' . ucfirst($currentRoute->getController()) . 'Controller';
        $s_controllerMethod = $currentRoute->getAction() . 'Action';

        if (!class_exists($s_controllerClass)) {
            throw new Exception('Class not found: ' . $s_controllerClass);
        }

        $controllerClass = new $s_controllerClass();

        if (!method_exists($controllerClass,$s_controllerMethod)) {
            throw new Exception('Method not found: ' . $s_controllerClass . '::' . $s_controllerMethod);
        }

        $checkMethod = new \ReflectionMethod($controllerClass, $s_controllerMethod);
        // check parameters route
        foreach ($currentRoute->getParams() as $argsRoute) {
            $isFound = false;
            foreach ($checkMethod->getParameters() as $argsMethod) {
                if ($argsRoute == $argsMethod->name) {
                    $isFound = true;
                    break;
                }
            }

            if (!$isFound) {
                throw new Exception('Undefined parameter "' . $argsRoute . '" in route');
            }
        }
        // check arguments method
        foreach ($checkMethod->getParameters() as $argsMethod) {
            $isFound = false;
            foreach ($currentRoute->getParams() as $argsRoute) {
                if ($argsRoute == $argsMethod->name) {
                    $isFound = true;
                    break;
                }
            }

            if (!$isFound) {
                throw new Exception('Undefined argument "' . $argsMethod . '" in method: ' . $s_controllerClass . '::' . $s_controllerMethod);
            }
        }

        $methodParams = [];
        foreach ($currentRoute->getParamsValue() as $item) {
            $methodParams[] = $item;
        }

        // init method controller
        $controllerClass->setDi($this->di);
        call_user_func_array([$controllerClass, $s_controllerMethod], $methodParams);
    }
}