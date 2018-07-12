<?php

namespace Simple\Cli;

use Simple\DI\DefaultFactory;
use Simple\DI\InjectionInterface;
use Simple\Exception;

class Console implements InjectionInterface
{
    private $di;

    private $arguments = [
        'task' => 'index',
        'action' => 'index',
        'params' => []
    ];

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
        return (!empty($this->di)) ? $this->di : DefaultFactory::getDefault();
    }

    /**
     * @param $args
     */
    public function setArguments($args)
    {
        $arguments = $this->arguments;

        if (count($args)) {
            array_shift($args);
        }

        $arguments['task'] = count($args) ? array_shift($args) : 'index';
        $arguments['action'] = count($args) ? array_shift($args) : 'index';

        if (count($args)) {
            foreach ($args as $arg) {
                $arguments['params'][] = $arg;
            }
        }

        $this->arguments = $arguments;
    }

    /**
     * Load cli
     */
    public function load()
    {
        $className = ucfirst($this->arguments['task']) . 'Task';
        $classAction = ucfirst($this->arguments['action']) . 'Action';

        if (!class_exists($className)) {
            throw new Exception('Class not found: ' . $className);
        }

        if (!method_exists($className, $classAction)) {
            throw new Exception('Method not found: ' . $className . '::' . $classAction);
        }

        $paramsMethod = [];
        $countImportantValues = 0;
        $checkMethod = new \ReflectionMethod($className, $classAction);
        foreach ($checkMethod->getParameters() as $key => $param) {
            $paramsMethod[$key] = [
                'name' => $param->getName(),
                'isOptional' => $param->isOptional()
            ];
            if (!$param->isOptional()) {
                $countImportantValues++;
            }
        }

        if (count($this->arguments['params']) < $countImportantValues) {
            throw new Exception('Check parameters in method: ' . $className . '::' . $classAction);
        }

        // init method
        $taskClass = new $className();
        $taskClass->setDi($this->di);
        call_user_func_array([$taskClass, $classAction], $this->arguments['params']);
    }
}