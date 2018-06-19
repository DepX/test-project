<?php

namespace Simple\Mvc;

use Simple\Exception;
use Simple\Mvc\Router\Route;

class Router
{
    private $defaultNamespace = '';
    private $defaultController = '';
    private $defaultAction = '';

    /**
     * @var \Simple\Mvc\Router\Route
     */
    public $currentRoute = null;

    /**
     * @var \Simple\Mvc\Router\Route
     */
    public $notFound = null;

    /**
     * @var \Simple\Mvc\Router\Route[]
     */
    private $listRouters = [];

    public function __construct($removeSlash = null)
    {
        if (empty($_REQUEST['_path'])) {
            throw new Exception('Error in file htaccess, not appointed "_path"');
        }

        if ($removeSlash === true) {
            $path = $_REQUEST['_path'];
            if (substr($path, -1) == '/' && $path != '/') {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . substr($path, 0, -1));
            }
        }
    }

    /**
     * Set default namespace
     *
     * @param string $namespace
     */
    public function setDefaultNamespace($namespace)
    {
        $this->defaultNamespace = $namespace;
    }

    /**
     * Set default controller name
     *
     * @param string $controllerName
     */
    public function setDefaultController($controllerName)
    {
        $this->defaultController = $controllerName;
    }

    /**
     * Set default action
     *
     * @param string $action
     */
    public function setDefaultAction($action)
    {
        $this->defaultAction = $action;
    }

    /**
     * Created new route
     *
     * @param $pattern
     * @param array $routeParams
     * @return Route
     */
    public function add($pattern, $routeParams = [])
    {
        $route = new Route();
        $route->setPattern($pattern);
        $route->setName((!empty($routeParams['name'])) ? $routeParams['name'] : '');
        $route->setNamespace((!empty($routeParams['namespace'])) ? $routeParams['namespace'] : $this->defaultNamespace);
        $route->setController((!empty($routeParams['controller'])) ? $routeParams['controller'] : $this->defaultController);
        $route->setAction((!empty($routeParams['action'])) ? $routeParams['action'] : $this->defaultAction);
        $route->setMethod('GET');

        $this->listRouters[] = $route;

        return $route;
    }

    /**
     * Returns a route by its id
     *
     * @param $id
     * @return bool|mixed
     */
    public function getRouteById($id)
    {
        $routeClass = function ($routeId) {
            if (!empty($this->listRouters[$routeId])) {
                return $this->listRouters[$routeId];
            }
            return false;
        };

        return $routeClass($id);
    }

    /**
     * Returns a route by its name
     *
     * @param $name
     * @return bool|mixed
     */
    public function getRouteByName($name)
    {
        $routeClass = function ($routeName) {
            foreach ($this->listRouters as $route) {
                if ($route->getName() == $routeName) {
                    return $route;
                }
            }
            return false;
        };

        return $routeClass($name);
    }

    /**
     * Ger current route
     *
     * @return bool|Route
     */
    public function getCurrentRoute()
    {
        foreach ($this->listRouters as $route) {
            $path = $_REQUEST['_path'];

            if ($route->getCompiledPattern() == $path) {
                return $route;
            }

            preg_match('#^' . $route->getCompiledPattern() . '$#u', $path, $matches);
            if (!empty($matches)) {
                $countMatches = count($matches);

                if ($countMatches > 1) {
                    unset($matches[0]);

                    $routeParamsValue = [];
                    foreach ($matches as $key => $match) {
                        $routeParamsValue[] = $match;
                    }

                    $route->setParamsValue($routeParamsValue);
                }

                return $route;
            }
        }
        return false;
    }

    /**
     * @param array $routeParams
     */
    public function notFound($routeParams = [])
    {
        $route = new Route();
        $route->setNamespace((!empty($routeParams['namespace'])) ? $routeParams['namespace'] : $this->defaultNamespace);
        $route->setController((!empty($routeParams['controller'])) ? $routeParams['controller'] : $this->defaultController);
        $route->setAction((!empty($routeParams['action'])) ? $routeParams['action'] : $this->defaultAction);

        $this->notFound = $route;
    }
}