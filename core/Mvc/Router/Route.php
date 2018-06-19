<?php

namespace Simple\Mvc\Router;

/**
 * Class Route
 * @package Simple\Mvc\Router
 */
class Route
{
    protected $pattern;
    protected $compiledPattern;
    protected $name;
    protected $namespace;
    protected $controller;
    protected $action;
    protected $method = 'GET';
    protected $params = [];
    protected $paramsValue = [];

    /**
     * Get pattern and generate compiled pattern
     *
     * @param $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        $params = [];

        preg_match_all("/{(.*?)}/", $pattern, $patternArray, PREG_SET_ORDER);
        if (!empty($patternArray)) {
            foreach ($patternArray as $item) {
                if (!empty($item[1])) {
                    $patternItemArray = explode(':', $item[1]);

                    if (!empty($patternItemArray[1])) {
                        $pattern = str_replace($item[0], '(' . $patternItemArray[1] . ')', $pattern);
                    } else {
                        $pattern = str_replace($item[0], '([^/]*)', $pattern);
                    }
                    $params[] = $patternItemArray[0];
                }
            }
        }

        $this->params = $params;
        $this->compiledPattern = $pattern;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return mixed
     */
    public function getCompiledPattern()
    {
        return $this->compiledPattern;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param $controller
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $paramsValue
     * @return $this
     */
    public function setParamsValue($paramsValue)
    {
        $this->paramsValue = $paramsValue;
        return $this;
    }

    /**
     * @return array
     */
    public function getParamsValue()
    {
        return $this->paramsValue;
    }
}