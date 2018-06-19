<?php

namespace Simple\Mvc;

use Simple\DI\InjectionInterface;
use Simple\Exception;

class Url implements InjectionInterface
{
    private $di;

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
     * Get path
     *
     * @param $params
     * @return bool|mixed
     */
    public function get($params)
    {
        if (isset($params['for'])) {
            if (!empty($params['for'])) {
                $route = $this->getDi()->getService('router')->getRouteByName($params['for']);
                if ($route === false) {
                    throw new Exception('Route "' . $params['for'] . '" is not found');
                }
                if ($route->getPattern() == $route->getCompiledPattern()) {
                    return $route->getPattern();
                }

                // check params
                $routeParams = $route->getParams();
                $path = $route->getCompiledPattern();
                foreach ($routeParams as $route_param) {
                    if (empty($params[$route_param])) {
                        throw new Exception('Not found route parameter "' . $route_param . '"');
                    }
                    preg_match_all("/\((.*?)\)/", $route->getCompiledPattern(), $patternArray, PREG_SET_ORDER);
                    if (!empty($patternArray)) {

                        foreach ($patternArray as $key => $item) {
                            if (!empty($item[0])) {

                                if (empty($routeParams[$key])) {
                                    throw new Exception('Not found route parameter "' . $routeParams[$key] . '"');
                                }

                                if (empty($params[$routeParams[$key]])) {
                                    throw new Exception('Not found route parameter "' . $routeParams[$key] . '"');
                                }

                                if (!preg_match($item[0], $params[$routeParams[$key]])) {
                                    throw new Exception('Error in route name: ' . $params['for'] . ', parameter: ' . $routeParams[$key] . ':' . $item[0] . ', value: ' . $params[$routeParams[$key]]);
                                }

                                // replace
                                $pos = strpos($path, $item[0]);
                                $path = ($pos !== false) ? substr_replace($path, $params[$routeParams[$key]], $pos, strlen($item[0])) : $path;
                            }
                        }
                    }
                }

                if (empty($path)) {
                    throw new Exception('Error in path generate');
                }

                return $path;

            } else {
                throw new Exception('Parameter "for" is empty');
            }
        }

        return false;
    }

    /**
     * Set new parameters in url
     *
     * @param $name
     * @param $value
     * @return string
     */
    public function setParameter($name, $value)
    {
        $newUriArray = [];
        $s_paramsUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        $paramsArray = explode('&', $s_paramsUri);

        $isFound = false;
        foreach ($paramsArray as $item) {
            $paramArray = explode('=', $item);
            if (count($paramArray) == 2) {
                if ($paramArray[0] == $name) {
                    $paramArray[1] = $value;
                    $isFound = true;
                }

                $newUriArray[] = $paramArray[0] . '=' . $paramArray[1];
            }
        }

        if (!$isFound) {
            $newUriArray[] = $name . '=' . $value;
        }

        $newUri = implode('&', $newUriArray);
        if (!empty($newUri)) {
            $newUri = '?' . $newUri;
        }
        return $newUri;
    }
}